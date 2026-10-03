<?php

namespace App\Services\Licensing;

use App\Models\Player;
use App\Models\PlayerLicense;
use App\Services\ApiConnectorState;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Registre d'identité externe FIFA ID, consulté de façon facultative pendant
 * l'approbation d'une licence : le joueur existe-t-il sous cet identifiant, et
 * son nom et sa date de naissance concordent-ils avec la fiche FIT ?
 *
 * Connecteur REST générique (GET {url}/persons/{identifiant FIFA}, jeton Bearer),
 * à brancher sur le service FIFA Connect ID de la fédération. Sans configuration,
 * aucune vérification n'est simulée : le résultat est « non connecté ».
 */
final class FifaIdRegistry
{
    public function __construct(private readonly ApiConnectorState $connectorState)
    {
    }

    public const STATUSES = [
        'not_configured' => 'Registre FIFA ID non connecté',
        'disabled' => 'Registre FIFA ID désactivé',
        'missing_id' => 'Identifiant FIFA absent de la fiche joueur',
        'match' => 'Identité confirmée par FIFA ID',
        'partial' => 'Identité partiellement concordante',
        'mismatch' => 'Identité discordante',
        'not_found' => 'Identifiant inconnu de FIFA ID',
        'error' => 'Registre FIFA ID injoignable',
    ];

    public function isConfigured(): bool
    {
        return filled(config('services.fifa_id.url')) && filled(config('services.fifa_id.token'));
    }

    public function isEnabled(): bool
    {
        return $this->connectorState->enabled('fifa_id', $this->isConfigured());
    }

    public function verify(Player $player): array
    {
        return $this->verifyPerson($player->fifa_connect_id, (string) $player->first_name, (string) $player->last_name, $player->date_of_birth);
    }

    /** Titulaire d'une licence : joueur, ou officiel (nom international FIFA Connect). */
    public function verifyLicense(PlayerLicense $license): array
    {
        if ($license->club_official_id && ($official = $license->clubOfficial)) {
            return $this->verifyPerson($official->person_fifa_id, (string) $official->international_first_name, (string) $official->international_last_name, $official->date_of_birth);
        }

        return $license->player ? $this->verify($license->player) : $this->verifyPerson(null, '', '', null);
    }

    /** @return array{status:string, label:string, checked_at:string, registry?:array, differences?:array, message?:string} */
    public function verifyPerson(?string $fifaIdentifier, string $firstName, string $lastName, $dateOfBirth): array
    {
        $person = (object) ['fifa_connect_id' => $fifaIdentifier, 'first_name' => $firstName, 'last_name' => $lastName, 'date_of_birth' => $dateOfBirth];
        $result = fn (string $status, array $extra = []) => ['status' => $status, 'label' => self::STATUSES[$status], 'checked_at' => now()->toIso8601String()] + $extra;

        if (!$this->isConfigured()) {
            return $result('not_configured');
        }
        if (!$this->isEnabled()) {
            return $result('disabled');
        }
        $fifaId = trim((string) $person->fifa_connect_id);
        if ($fifaId === '') {
            return $result('missing_id');
        }

        try {
            $response = Http::timeout(config('services.fifa_id.timeout', 10))->acceptJson()
                ->withToken(config('services.fifa_id.token'))
                ->get(rtrim(config('services.fifa_id.url'), '/') . '/persons/' . rawurlencode($fifaId));
        } catch (\Throwable $e) {
            return $result('error', ['message' => mb_substr($e->getMessage(), 0, 200)]);
        }
        if ($response->status() === 404) {
            return $result('not_found');
        }
        if (!$response->successful()) {
            return $result('error', ['message' => 'Réponse HTTP ' . $response->status()]);
        }

        $found = (array) $response->json();
        $registry = [
            'first_name' => $found['firstName'] ?? $found['first_name'] ?? null,
            'last_name' => $found['lastName'] ?? $found['last_name'] ?? null,
            'date_of_birth' => $found['dateOfBirth'] ?? $found['date_of_birth'] ?? null,
            'nationality' => $found['nationality'] ?? null,
        ];
        $differences = [];
        foreach (['first_name', 'last_name'] as $field) {
            if ($registry[$field] !== null && $this->normalize($registry[$field]) !== $this->normalize((string) $person->{$field})) {
                $differences[] = $field;
            }
        }
        if ($registry['date_of_birth'] !== null && $person->date_of_birth
            && Carbon::parse($registry['date_of_birth'])->toDateString() !== Carbon::parse($person->date_of_birth)->toDateString()) {
            $differences[] = 'date_of_birth';
        }
        $compared = count(array_filter([$registry['first_name'], $registry['last_name'], $registry['date_of_birth'] && $person->date_of_birth ? 1 : null]));
        $status = $differences === [] ? ($compared >= 3 ? 'match' : 'partial') : (count($differences) >= 2 ? 'mismatch' : 'partial');

        return $result($status, ['registry' => $registry, 'differences' => $differences]);
    }

    private function normalize(string $value): string
    {
        return trim(preg_replace('/[^a-z]+/', ' ', Str::ascii(mb_strtolower($value))));
    }
}
