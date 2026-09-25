<?php

namespace App\Models\FifaConnect;

use App\Models\FifaConnect\Concerns\HasFifaPicture;

use App\Models\Player;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    use HasFifaPicture;

    public const FIFA_PICTURE_OWNER_TYPE = 'person';
    protected $table = 'fifa_connect_persons';

    protected $fillable = [
        'person_fifa_id',
        'international_first_name',
        'international_last_name',
        'popular_name',
        'local_first_name',
        'local_last_name',
        'local_birth_name',
        'local_system_ma_id',
        'local_language',
        'local_country',
        'gender',
        'nationality',
        'second_nationality',
        'date_of_birth',
        'country_of_birth',
        'region_of_birth',
        'place_of_birth',
        'player_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function localNames(): HasMany
    {
        return $this->hasMany(PersonLocalName::class, 'person_id');
    }

    public function nationalIdentifiers(): HasMany
    {
        return $this->hasMany(PersonNationalIdentifier::class, 'person_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'person_id');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class, 'person_id');
    }
}
