<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DisciplineCase extends Model
{
    protected $table = 'fifa_connect_discipline_cases';

    protected $guarded = [];

    protected $casts = [
        'case_date' => 'date',
    ];

    public function caseMatchEvent(): HasOne
    {
        return $this->hasOne(CaseMatchEvent::class, 'case_id');
    }

    public function matchEvent(): HasOne
    {
        return $this->hasOne(CaseMatchEvent::class, 'case_id');
    }

    public function sanctions(): HasMany
    {
        return $this->hasMany(Sanction::class, 'case_id');
    }
}
