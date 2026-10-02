<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Dirigeant ou membre du staff d'un club, au format des données FIFA Connect. */
class ClubOfficial extends Model
{
    public const TEAM_OFFICIAL = 'TeamOfficial';
    public const ORGANISATION_OFFICIAL = 'OrganisationOfficial';

    protected $guarded = ['id', 'club_id', 'created_by'];

    protected $casts = [
        'date_of_birth' => 'date',
        'registration_valid_from' => 'date',
        'registration_valid_to' => 'date',
        'certification_valid_from' => 'date',
        'certification_valid_to' => 'date',
        'is_head_coach' => 'boolean',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function fullName(): string
    {
        return trim($this->international_first_name . ' ' . $this->international_last_name);
    }

    public function roleCode(): ?string
    {
        return $this->registration_type === self::TEAM_OFFICIAL ? $this->team_official_role : $this->organisation_official_role;
    }

    public function roleLabel(): string
    {
        $group = $this->registration_type === self::TEAM_OFFICIAL ? 'team_official' : 'organisation_official';

        return config("fifa_connect_roles.{$group}.{$this->roleCode()}.label") ?? (string) $this->roleCode();
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && (!$this->registration_valid_to || $this->registration_valid_to->isFuture() || $this->registration_valid_to->isToday());
    }
}
