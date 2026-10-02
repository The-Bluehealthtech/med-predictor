<?php

namespace App\Services\Dtn;

use App\Models\User;

/**
 * Droits (abilities Sanctum) des jetons d'API des sélections nationales.
 * Un jeton est limité à un seul espace ; le droit médical n'est accordé
 * qu'aux jetons des comptes médicaux de cet espace.
 */
final class ApiAbilities
{
    public const DTN_PLAYERS_READ = 'dtn:players:read';
    public const DTN_SELECTIONS_READ = 'dtn:selections:read';
    public const DTN_SELECTIONS_WRITE = 'dtn:selections:write';
    public const CLUB_SELECTIONS_READ = 'club:selections:read';
    public const CLUB_SELECTIONS_WRITE = 'club:selections:write';
    public const MEDICAL = 'selections:medical';

    public const SPACES = [
        'federation' => [
            'label' => 'Espace fédération (DTN)',
            'prefix' => 'dtn-federation',
            'abilities' => [self::DTN_PLAYERS_READ, self::DTN_SELECTIONS_READ, self::DTN_SELECTIONS_WRITE],
            'medical_roles' => ['association_medical'],
            'permission' => DtnAccess::FEDERATION_PERMISSION,
        ],
        'club' => [
            'label' => 'Espace club',
            'prefix' => 'club-selections',
            'abilities' => [self::CLUB_SELECTIONS_READ, self::CLUB_SELECTIONS_WRITE],
            'medical_roles' => ['club_medical', 'team_doctor'],
            'permission' => DtnAccess::CLUB_PERMISSION,
        ],
    ];

    /** Droits accordés à un nouveau jeton de cet espace pour cet utilisateur. */
    public static function forSpace(string $space, User $user, bool $withMedical): array
    {
        $config = self::SPACES[$space];
        $abilities = $config['abilities'];
        if ($withMedical && in_array($user->role, $config['medical_roles'], true)) {
            $abilities[] = self::MEDICAL;
        }

        return $abilities;
    }

    public static function canHaveMedical(string $space, User $user): bool
    {
        return in_array($user->role, self::SPACES[$space]['medical_roles'], true);
    }
}
