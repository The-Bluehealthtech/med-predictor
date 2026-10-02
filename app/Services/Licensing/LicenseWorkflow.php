<?php

namespace App\Services\Licensing;

use App\Models\ClubOfficial;
use App\Models\Player;
use App\Models\PlayerLicense;
use App\Models\PlayerLicenseDocument;
use App\Models\PlayerLicenseEvent;
use App\Models\User;
use App\Notifications\LicenseWorkflowNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Circuit de licence (joueurs, officiels d'équipe, dirigeants), aligné sur
 * l'enregistrement FIFA Connect : le club demande, la fédération approuve, demande
 * un complément ou refuse ; la vérification d'identité FIFA ID est facultative.
 * Chaque étape est historisée et notifiée (club ↔ fédération) ; les pièces
 * justificatives exigées dépendent du type de licence (config/licensing.php).
 *
 *   Club : demande ──► En attente ──► Fédération : approuve ──► Active
 *                          ▲    │                └─ refuse ──► Refusée
 *                          │    └─ demande un complément ──► Complément demandé
 *                          └──────── le club répond ◄────────────┘
 */
final class LicenseWorkflow
{
    public const STATUS_LABELS = [
        'pending' => 'En attente de la fédération',
        'justification_requested' => 'Complément demandé au club',
        'active' => 'Active',
        'revoked' => 'Refusée',
        'expired' => 'Expirée',
        'suspended' => 'Suspendue',
    ];

    /** Anciens types (licences antérieures à l'alignement FIFA Connect), pour l'affichage seulement. */
    public const TYPES = [
        'amateur' => 'Amateur', 'professional' => 'Professionnelle', 'futsal' => 'Futsal',
        'beach_soccer' => 'Beach soccer', 'youth' => 'Jeunes', 'international' => 'Internationale', 'player' => 'Joueur',
    ];

    /** Libellé d'une licence selon FIFA Connect : type d'enregistrement, discipline, genre, catégorie, niveau, nature ou rôle. */
    public static function describe(PlayerLicense $license): string
    {
        $c = fn (string $list, ?string $key) => $key === null ? null : config("licensing.{$list}.{$key}", $key);
        if (!$license->registration_type) {
            return self::TYPES[$license->license_type] ?? (string) $license->license_type;
        }
        if ($license->registration_type === 'Player') {
            return collect(['Joueur', $c('disciplines', $license->discipline), $c('genders', $license->gender), $license->age_category ? str_replace('SENIOR', 'Senior', $license->age_category) : null,
                $c('levels', $license->level), $license->registration_nature === 'Loan' ? 'prêt' : null])->filter()->implode(' · ');
        }
        $role = $license->registration_type === 'TeamOfficial'
            ? config("fifa_connect_roles.team_official.{$license->team_official_role}.label", $license->team_official_role)
            : config("fifa_connect_roles.organisation_official.{$license->organisation_official_role}.label", $license->organisation_official_role);

        return collect([$c('registration_types', $license->registration_type), $role, $c('disciplines', $license->discipline)])->filter()->implode(' · ');
    }

    private const CLUB_ROLES = ['club_admin', 'club_manager'];

    private const FEDERATION_ROLES = ['association_admin', 'association_registrar'];

    public function canRequest(User $user): bool
    {
        return $user->isSystemAdmin() || (in_array($user->role, self::CLUB_ROLES, true) && $user->club_id)
            || (in_array($user->role, self::FEDERATION_ROLES, true) && $user->association_id);
    }

    public function canApprove(User $user): bool
    {
        return $user->isSystemAdmin() || (in_array($user->role, self::FEDERATION_ROLES, true) && $user->association_id);
    }

    /** Joueurs pour lesquels le compte peut déposer une demande (club : les siens ; fédération : ceux de ses clubs). */
    public function requestablePlayers(User $user): Builder
    {
        $query = Player::query();
        if ($user->isSystemAdmin()) {
            return $query;
        }
        if ($user->club_id && !$user->isAssociationUser()) {
            return $query->where('club_id', $user->club_id);
        }
        if ($user->association_id) {
            return $query->whereIn('club_id', DB::table('clubs')->where('association_id', $user->association_id)->select('id')); // périmètre explicite, sans filtre de tenant implicite
        }

        return $query->whereRaw('1 = 0');
    }

    /** Officiels d'équipe et dirigeants pour lesquels le compte peut déposer une demande. */
    public function requestableOfficials(User $user): Builder
    {
        $query = ClubOfficial::query();
        if ($user->isSystemAdmin()) {
            return $query;
        }
        if ($user->club_id && !$user->isAssociationUser()) {
            return $query->where('club_id', $user->club_id);
        }
        if ($user->association_id) {
            return $query->whereIn('club_id', DB::table('clubs')->where('association_id', $user->association_id)->select('id'));
        }

        return $query->whereRaw('1 = 0');
    }

    /** Licences visibles : celles des joueurs et des officiels du périmètre. */
    public function licenses(User $user): Builder
    {
        return PlayerLicense::query()->where(fn ($q) => $q->whereIn('player_id', $this->requestablePlayers($user)->select('players.id'))
            ->orWhereIn('club_official_id', $this->requestableOfficials($user)->select('club_officials.id')));
    }

    public function canAccess(User $user, PlayerLicense $license): bool
    {
        return $this->licenses($user)->whereKey($license->getKey())->exists();
    }

    /** Pièces exigées pour une demande : celles de la catégorie d'âge du barème et du type de licence. */
    public function requiredDocuments(PlayerLicense $license): array
    {
        $scale = app(LicenseScale::class);
        if ($license->club_official_id) {
            return $license->clubOfficial ? $scale->officialRules($license->clubOfficial)['documents'] : config('licensing.official_documents', []);
        }
        if (!$license->registration_type || !$license->player) {
            return []; // licence antérieure à l'alignement FIFA Connect : pas de pièces exigées a posteriori
        }

        return $scale->playerRules($license->player, (string) $license->discipline, (string) $license->level, (string) ($license->registration_nature ?: 'Registration'))['documents'];
    }

    /** Pièces exigées encore absentes de la demande. */
    public function missingDocuments(PlayerLicense $license): array
    {
        $present = $license->documents()->pluck('document_type')->unique()->all();

        return array_values(array_diff($this->requiredDocuments($license), $present));
    }

    /**
     * Dépôt d'une licence de joueur (enregistrement FIFA Connect « Player ») :
     * discipline, niveau, nature, saison ; pièces exigées par le barème.
     */
    public function submitPlayer(Player $player, User $by, array $data, array $files): PlayerLicense
    {
        $scale = app(LicenseScale::class);
        $settings = $scale->settings($scale->associationOfClub($player->club_id));
        $season = $scale->seasonByLabel($settings, $data['season']) ?? throw new InvalidArgumentException('Saison non disponible.');
        if (!empty($data['gender']) && !$player->gender) {
            $player->forceFill(['gender' => $data['gender']])->save(); // FIFA Connect : genre obligatoire sur la personne
        }
        $rules = $scale->playerRules($player, $data['discipline'], $data['level'], $data['registration_nature'], $season);
        if ($rules['gender_missing']) {
            throw new InvalidArgumentException('Le genre du joueur est obligatoire (FIFA Connect).');
        }
        if (!$rules['allowed']) {
            throw new InvalidArgumentException('Niveau ' . mb_strtolower(config("licensing.levels.{$data['level']}")) . " non autorisé en catégorie {$rules['category_label']} selon le barème de la fédération.");
        }
        $this->assertNoDuplicate(PlayerLicense::query()->where('player_id', $player->id), $data['discipline'], $season['label']);
        $this->assertDocuments($rules['documents'], $files);

        $license = PlayerLicense::create([
            'player_id' => $player->id,
            'club_id' => $player->club_id,
            'registration_type' => 'Player',
            'discipline' => $data['discipline'],
            'level' => $data['level'],
            'registration_nature' => $data['registration_nature'],
            'gender' => $rules['gender'],
            'license_type' => $data['level'],
            'season' => $season['label'],
            'status' => 'pending',
            'approval_status' => 'pending',
            'contract_start_date' => (now()->startOfDay()->gt($season['start']) ? now()->startOfDay() : $season['start'])->toDateString(),
            'contract_end_date' => $season['end']->toDateString(),
            'expiry_date' => $season['end']->toDateString(),
            'fifa_connect_id' => $player->fifa_connect_id,
            'requested_by' => $by->id,
            'notes' => $data['notes'] ?? null,
            // Catégorie d'âge et tarif du barème au moment du dépôt.
            'age_category' => $rules['category'],
            'fee_amount' => $rules['fee'],
            'fee_currency' => $rules['fee'] !== null ? $rules['currency'] : null,
        ]);

        return $this->afterSubmit($license, $by, $files, $data['notes'] ?? null);
    }

    /** Dépôt d'une licence d'officiel d'équipe ou de dirigeant (TeamOfficial, OrganisationOfficial). */
    public function submitOfficial(ClubOfficial $official, User $by, array $data, array $files): PlayerLicense
    {
        $scale = app(LicenseScale::class);
        $settings = $scale->settings($scale->associationOfClub($official->club_id));
        $season = $scale->seasonByLabel($settings, $data['season']) ?? throw new InvalidArgumentException('Saison non disponible.');
        $rules = $scale->officialRules($official, $season);
        $this->assertNoDuplicate(PlayerLicense::query()->where('club_official_id', $official->id), $data['discipline'], $season['label']);
        $this->assertDocuments($rules['documents'], $files);

        $license = PlayerLicense::create([
            'club_official_id' => $official->id,
            'club_id' => $official->club_id,
            'registration_type' => $official->registration_type,
            'team_official_role' => $official->team_official_role,
            'organisation_official_role' => $official->organisation_official_role,
            'discipline' => $data['discipline'],
            'gender' => $official->gender,
            'license_type' => $official->registration_type,
            'season' => $season['label'],
            'status' => 'pending',
            'approval_status' => 'pending',
            'contract_start_date' => (now()->startOfDay()->gt($season['start']) ? now()->startOfDay() : $season['start'])->toDateString(),
            'contract_end_date' => $season['end']->toDateString(),
            'expiry_date' => $season['end']->toDateString(),
            'fifa_connect_id' => $official->person_fifa_id,
            'requested_by' => $by->id,
            'notes' => $data['notes'] ?? null,
            'fee_amount' => $rules['fee'],
            'fee_currency' => $rules['fee'] !== null ? $rules['currency'] : null,
        ]);

        return $this->afterSubmit($license, $by, $files, $data['notes'] ?? null);
    }

    private function assertNoDuplicate(Builder $query, string $discipline, string $season): void
    {
        if ($query->where('discipline', $discipline)->where('season', $season)->whereIn('status', ['pending', 'justification_requested', 'active'])->exists()) {
            throw new InvalidArgumentException("Une licence active ou une demande en cours existe déjà pour cette discipline et la saison {$season}.");
        }
    }

    private function assertDocuments(array $required, array $files): void
    {
        $missing = array_diff($required, array_keys(array_filter($files)));
        if ($missing !== []) {
            throw new InvalidArgumentException('Pièces manquantes : ' . collect($missing)->map(fn ($t) => config("licensing.documents.{$t}", $t))->implode(', ') . '.');
        }
    }

    private function afterSubmit(PlayerLicense $license, User $by, array $files, ?string $notes): PlayerLicense
    {
        $this->storeDocuments($license, $files, $by, false);
        $this->event($license, $by, 'submitted', $notes);
        $this->notifyFederation($license, 'submitted', 'Nouvelle demande de licence : ' . $this->holderName($license) . ' — ' . self::describe($license) . ' (' . $this->clubName($license) . ').');

        return $license;
    }

    /** Ajout de pièces par le club tant que la demande n'est pas tranchée. */
    public function addDocuments(PlayerLicense $license, User $by, array $files): int
    {
        if (!in_array($license->status, ['pending', 'justification_requested'], true)) {
            throw new InvalidArgumentException('Les pièces ne peuvent plus être modifiées : la demande est déjà tranchée.');
        }

        return $this->storeDocuments($license, $files, $by, true);
    }

    /** Décision de la fédération : approve, request_info (message obligatoire) ou reject (motif obligatoire). */
    public function decide(PlayerLicense $license, User $by, string $decision, ?string $message = null): void
    {
        if ($license->status !== 'pending') {
            throw new InvalidArgumentException('Seule une demande en attente de la fédération peut recevoir une décision.');
        }
        $message = trim((string) $message);

        if ($decision === 'approve' && !$license->club_official_id && ($pcma = app(PcmaRequirement::class)->check($license))['blocking']) {
            throw new InvalidArgumentException('Approbation impossible : ' . $pcma['reason'] . ' État actuel : ' . mb_strtolower($pcma['status']['label']) . '. Demandez au club de faire réaliser et signer le PCMA.');
        }
        if ($decision === 'approve' && ($missing = $this->missingDocuments($license)) !== []) {
            throw new InvalidArgumentException('Approbation impossible : pièces manquantes (' . collect($missing)->map(fn ($t) => config("licensing.documents.{$t}", $t))->implode(', ') . '). Demandez un complément au club.');
        }

        match ($decision) {
            'approve' => $this->approve($license, $by),
            'request_info' => $message === ''
                ? throw new InvalidArgumentException('Précisez au club ce qu\'il doit compléter.')
                : $license->update(['status' => 'justification_requested', 'approval_status' => 'pending', 'rejection_reason' => $message,
                    'approved_by' => $by->id, 'approved_at' => now()]),
            'reject' => $message === ''
                ? throw new InvalidArgumentException('Le motif du refus est obligatoire.')
                : $license->update(['status' => 'revoked', 'approval_status' => 'rejected', 'rejection_reason' => $message,
                    'approved_by' => $by->id, 'approved_at' => now()]),
            default => throw new InvalidArgumentException('Décision inconnue.'),
        };

        $action = ['approve' => 'approved', 'request_info' => 'info_requested', 'reject' => 'rejected'][$decision];
        $this->event($license, $by, $action, $message ?: null);
        $text = match ($decision) {
            'approve' => 'Licence approuvée pour ' . $this->holderName($license) . ', valable jusqu\'au ' . $license->expiry_date?->format('d/m/Y') . '.',
            'request_info' => 'Complément demandé par la fédération pour la licence de ' . $this->holderName($license) . ' : ' . $message,
            'reject' => 'Demande de licence refusée pour ' . $this->holderName($license) . ' : ' . $message,
        };
        $this->notifyClub($license, $action, $text);
    }

    /** Réponse du club à une demande de complément (message et pièces) : la demande repart à la fédération. */
    public function respond(PlayerLicense $license, User $by, string $response, array $files = []): void
    {
        if ($license->status !== 'justification_requested') {
            throw new InvalidArgumentException('Aucun complément n\'est attendu pour cette demande.');
        }
        $response = trim($response);
        if ($response === '' && array_filter($files) === []) {
            throw new InvalidArgumentException('Ajoutez une réponse ou une pièce justificative.');
        }
        $this->storeDocuments($license, $files, $by, false);
        $license->update(['status' => 'pending', 'approval_status' => 'pending', 'club_response' => $response ?: $license->club_response]);
        $this->event($license, $by, 'responded', $response ?: null);
        $this->notifyFederation($license, 'responded', 'Complément reçu pour la licence de ' . $this->holderName($license) . ' (' . $this->clubName($license) . ').');
    }

    /** Vérification d'identité auprès du registre FIFA ID, enregistrée sur la demande. */
    public function recordIdentityCheck(PlayerLicense $license, array $result, ?User $by = null): void
    {
        $license->update(['identity_check_status' => $result['status'], 'identity_checked_at' => now(), 'identity_check' => $result]);
        $this->event($license, $by, 'identity_checked', $result['label'] ?? $result['status']);
    }

    /** Comptes du club à notifier : responsables du club et auteur de la demande. */
    public function clubRecipients(PlayerLicense $license): Collection
    {
        return User::query()->where(fn ($q) => $q->where(fn ($c) => $c->where('club_id', $license->club_id)->whereIn('role', self::CLUB_ROLES))
            ->orWhere('id', $license->requested_by ?: 0))->get();
    }

    /** Comptes de la fédération du club à notifier. */
    public function federationRecipients(PlayerLicense $license): Collection
    {
        $associationId = DB::table('clubs')->where('id', $license->club_id)->value('association_id');

        return $associationId ? User::query()->where('association_id', $associationId)->whereIn('role', self::FEDERATION_ROLES)->get() : collect();
    }

    private function storeDocuments(PlayerLicense $license, array $files, ?User $by, bool $withEvent): int
    {
        $stored = 0;
        foreach ($files as $type => $file) {
            if (!$file instanceof UploadedFile || !array_key_exists($type, config('licensing.documents', []))) {
                continue;
            }
            $content = $file->get();
            PlayerLicenseDocument::query()->create([
                'player_license_id' => $license->id,
                'document_type' => $type,
                'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => strlen($content),
                'sha256' => hash('sha256', $content),
                'content_base64' => base64_encode($content),
                'uploaded_by' => $by?->id,
            ]);
            $stored++;
            if ($withEvent) {
                $this->event($license, $by, 'document_added', config("licensing.documents.{$type}", $type));
            }
        }

        return $stored;
    }

    private function event(PlayerLicense $license, ?User $by, string $action, ?string $message = null): void
    {
        PlayerLicenseEvent::query()->create(['player_license_id' => $license->id, 'user_id' => $by?->id, 'action' => $action, 'message' => $message]);
    }

    private function notifyFederation(PlayerLicense $license, string $action, string $message): void
    {
        $this->notify($this->federationRecipients($license), $license, $action, $message, route('licenses.review', $license));
    }

    private function notifyClub(PlayerLicense $license, string $action, string $message): void
    {
        $this->notify($this->clubRecipients($license), $license, $action, $message, route('modules.licenses.index'));
    }

    /** Une notification qui échoue ne bloque jamais l'étape du circuit. */
    private function notify(Collection $users, PlayerLicense $license, string $action, string $message, string $url): void
    {
        try {
            Notification::send($users->unique('id'), new LicenseWorkflowNotification($license, $action, mb_substr($message, 0, 400), $url));
        } catch (\Throwable $e) {
            Log::warning('licences : notification non envoyée', ['license' => $license->id, 'error' => $e->getMessage()]);
        }
    }

    /** Titulaire de la licence : joueur ou officiel (nom international FIFA Connect). */
    public function holderName(PlayerLicense $license): string
    {
        if ($license->club_official_id) {
            $official = $license->clubOfficial;

            return $official ? (trim($official->international_first_name . ' ' . $official->international_last_name) ?: (string) $official->popular_name) : 'officiel #' . $license->club_official_id;
        }
        $player = $license->player;

        return $player ? (trim($player->first_name . ' ' . $player->last_name) ?: (string) $player->name) : 'joueur #' . $license->player_id;
    }

    private function clubName(PlayerLicense $license): string
    {
        return (string) (DB::table('clubs')->where('id', $license->club_id)->value('name') ?? 'club');
    }

    private function approve(PlayerLicense $license, User $by): void
    {
        if (!$license->expiry_date || $license->expiry_date->isPast()) {
            throw new InvalidArgumentException('Une date d\'expiration future est requise avant approbation.');
        }
        $license->update(['status' => 'active', 'approval_status' => 'approved', 'approved_by' => $by->id, 'approved_at' => now(),
            'issue_date' => now()->toDateString(), 'issued_date' => now()->toDateString(), 'issued_by' => (string) $by->id]);
    }
}
