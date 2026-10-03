<?php

namespace App\Services\DicomWeb;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Accès DICOMweb au PACS (IHE RAD WIA, Web-based Image Access ; DICOM PS3.18) :
 *  - QIDO-RS : séries d'un examen, images d'une série (application/dicom+json) ;
 *  - WADO-RS : métadonnées d'une image et image DICOM d'origine (multipart/related).
 * Seule l'adresse HTTPS configurée (MEDICAL_PACS_DICOMWEB_URL) est utilisée ; aucune image
 * n'est conservée par FIT (ni base, ni cache).
 */
final class DicomWebClient
{
    public const UID = '/^[0-9]+(\.[0-9]+){1,63}$/';

    public function configured(): bool
    {
        return str_starts_with((string) config('medical_imaging.pacs_url'), 'https://');
    }

    /** @return list<array{uid:string, number:?int, modality:?string, description:?string, instances:?int}> */
    public function series(string $study): array
    {
        $rows = $this->json('/studies/' . $this->uid($study) . '/series');

        return collect($rows)->map(fn ($r) => [
            'uid' => (string) $this->value($r, '0020000E'),
            'number' => $this->int($this->value($r, '00200011')),
            'modality' => $this->value($r, '00080060'),
            'description' => $this->value($r, '0008103E'),
            'instances' => $this->int($this->value($r, '00201209')),
        ])->filter(fn ($s) => preg_match(self::UID, $s['uid']))->sortBy(fn ($s) => $s['number'] ?? PHP_INT_MAX)->values()->all();
    }

    /** @return list<array{uid:string, number:?int, frames:int}> */
    public function instances(string $study, string $series): array
    {
        $rows = $this->json('/studies/' . $this->uid($study) . '/series/' . $this->uid($series) . '/instances');

        return collect($rows)->map(fn ($r) => [
            'uid' => (string) $this->value($r, '00080018'),
            'number' => $this->int($this->value($r, '00200013')),
            'frames' => max(1, (int) ($this->int($this->value($r, '00280008')) ?? 1)),
        ])->filter(fn ($i) => preg_match(self::UID, $i['uid']))->sortBy(fn ($i) => $i['number'] ?? PHP_INT_MAX)->values()->all();
    }

    /** Métadonnées DICOM JSON d'une image (fenêtre, modalité), sans pixels. */
    public function metadata(string $study, string $series, string $instance): array
    {
        $rows = $this->json('/studies/' . $this->uid($study) . '/series/' . $this->uid($series) . '/instances/' . $this->uid($instance) . '/metadata');

        return $rows[0] ?? [];
    }

    /** Image DICOM d'origine (WADO-RS, syntaxe de transfert stockée). */
    public function retrieve(string $study, string $series, string $instance): string
    {
        $response = $this->send(fn (PendingRequest $http) => $http->withHeaders(['Accept' => 'multipart/related; type="application/dicom"; transfer-syntax=*'])
            ->get($this->url('/studies/' . $this->uid($study) . '/series/' . $this->uid($series) . '/instances/' . $this->uid($instance))));
        $parts = self::multipart((string) $response->header('Content-Type'), $response->body());
        if ($parts === []) {
            throw new RuntimeException('Réponse WADO-RS sans image DICOM.');
        }

        return $parts[0];
    }

    /** Corps des parties d'une réponse multipart/related (RFC 2387). @return list<string> */
    public static function multipart(string $contentType, string $body): array
    {
        if (!preg_match('/boundary="?([^";]+)"?/i', $contentType, $m)) {
            return [];
        }
        $parts = [];
        foreach (explode('--' . $m[1], $body) as $chunk) {
            if ($chunk === '' || str_starts_with($chunk, '--')) {
                continue;
            }
            $split = strpos($chunk, "\r\n\r\n");
            if ($split === false) {
                continue;
            }
            $content = substr($chunk, $split + 4);
            if (str_ends_with($content, "\r\n")) {
                $content = substr($content, 0, -2);
            }
            if ($content !== '') {
                $parts[] = $content;
            }
        }

        return $parts;
    }

    /** Valeur simple d'un attribut DICOM JSON (PS3.18 F.2). */
    public function value(array $dataset, string $tag): ?string
    {
        $value = $dataset[$tag]['Value'][0] ?? null;
        if (is_array($value)) {
            $value = $value['Alphabetic'] ?? null; // PN
        }

        return $value === null ? null : (string) $value;
    }

    private function json(string $path): array
    {
        $response = $this->send(fn (PendingRequest $http) => $http->accept('application/dicom+json')->get($this->url($path)));
        if ($response->status() === 204) {
            return [];
        }
        $rows = $response->json();

        return is_array($rows) ? $rows : [];
    }

    private function send(callable $call): Response
    {
        if (!$this->configured()) {
            throw new RuntimeException('PACS DICOMweb non configuré (MEDICAL_PACS_DICOMWEB_URL en HTTPS).');
        }
        $http = Http::timeout(60)->withOptions(['allow_redirects' => false]);
        if ($token = config('medical_imaging.pacs_token')) {
            $http = $http->withToken((string) $token);
        }
        try {
            $response = $call($http);
        } catch (ConnectionException) {
            throw new RuntimeException('PACS injoignable.');
        }
        if (!$response->successful()) {
            throw new RuntimeException('Le PACS a refusé la requête (HTTP ' . $response->status() . ').');
        }

        return $response;
    }

    private function uid(string $uid): string
    {
        if (!preg_match(self::UID, $uid)) {
            throw new RuntimeException('UID DICOM invalide.');
        }

        return $uid;
    }

    private function url(string $path): string
    {
        return rtrim((string) config('medical_imaging.pacs_url'), '/') . $path;
    }

    private function int(?string $value): ?int
    {
        return $value !== null && is_numeric($value) ? (int) $value : null;
    }
}
