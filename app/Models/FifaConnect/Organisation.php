<?php

namespace App\Models\FifaConnect;

use App\Models\FifaConnect\Concerns\HasFifaPicture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    use HasFifaPicture;

    public const FIFA_PICTURE_OWNER_TYPE = 'organisation';
    protected $table = 'fifa_connect_organisations';

    protected $guarded = [];

    protected $casts = [
        'foundation_date' => 'date',
        'dissolution_date' => 'date',
    ];

    public function localNames(): HasMany
    {
        return $this->hasMany(OrganisationLocalName::class, 'organisation_id');
    }

    public function nationalIdentifiers(): HasMany
    {
        return $this->hasMany(
            OrganisationNationalIdentifier::class,
            'organisation_id'
        );
    }

    public function supportedDisciplines(): HasMany
    {
        return $this->hasMany(SupportedDiscipline::class, 'organisation_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'owner_id')
            ->where('owner_type', 'organisation');
    }
}
