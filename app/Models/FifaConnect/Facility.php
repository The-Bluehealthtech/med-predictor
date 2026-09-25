<?php

namespace App\Models\FifaConnect;

use App\Models\FifaConnect\Concerns\HasFifaPicture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    use HasFifaPicture;

    public const FIFA_PICTURE_OWNER_TYPE = 'facility';
    protected $table = 'fifa_connect_facilities';

    protected $guarded = [];

    public function localNames(): HasMany
    {
        return $this->hasMany(FacilityLocalName::class, 'facility_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class, 'facility_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'owner_id')
            ->where('owner_type', 'facility');
    }
}
