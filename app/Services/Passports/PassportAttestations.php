<?php

namespace App\Services\Passports;

use App\Models\PassportAttestation;
use App\Models\Player;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

/**
 * Signature électronique simple du passeport médical par un médecin :
 * authentification FIT, confirmation par mot de passe, empreinte SHA-256 du
 * contenu médical attesté. Si les données changent après la signature, le
 * passeport redevient « non attesté » jusqu'à une nouvelle signature.
 */
final class PassportAttestations
{
    /** Rôles autorisés à signer : les médecins, pas l'administrateur système. */
    public const SIGNER_ROLES = ['club_medical', 'association_medical', 'doctor', 'team_doctor', 'medical_staff'];

    public function __construct(private readonly PassportAccess $access)
    {
    }

    public function canAttest(User $user, Player $player): bool
    {
        return in_array($user->role, self::SIGNER_ROLES, true) && $this->access->canViewMedical($user, $player);
    }

    /** Contenu médical attesté : patient et sections, sans les éléments propres à chaque génération. */
    public function content(array $summary): array
    {
        $normalize = function ($value) use (&$normalize) {
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->toDateString();
            }

            return is_array($value) ? array_map($normalize, $value) : $value;
        };

        return $normalize(['patient' => $summary['patient'], 'sections' => $summary['sections']]);
    }

    public function hash(array $summary): string
    {
        return hash('sha256', json_encode($this->content($summary), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function attest(User $user, Player $player, array $summary, string $purpose, ?string $license, ?string $ip): PassportAttestation
    {
        return PassportAttestation::create([
            'player_id' => $player->id, 'user_id' => $user->id, 'signer_name' => $user->name, 'signer_role' => $user->role,
            'signer_license' => $license, 'purpose' => $purpose, 'content_sha256' => $this->hash($summary),
            'content' => $this->content($summary), 'signed_at' => now(), 'ip' => $ip,
        ]);
    }

    /** État de l'attestation pour le résumé courant : valid, outdated (données modifiées) ou none. */
    public function status(Player $player, array $summary): array
    {
        $latest = Schema::hasTable('passport_attestations')
            ? PassportAttestation::query()->where('player_id', $player->id)->latest('signed_at')->latest('id')->first()
            : null;
        if (!$latest) {
            return ['state' => 'none', 'attestation' => null, 'hash' => $this->hash($summary)];
        }
        $current = $this->hash($summary);

        return ['state' => hash_equals($latest->content_sha256, $current) ? 'valid' : 'outdated', 'attestation' => $latest, 'hash' => $current];
    }
}
