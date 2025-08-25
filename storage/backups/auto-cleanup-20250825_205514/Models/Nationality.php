<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    use HasFactory;

    protected $table = 'nationalities';

    protected $fillable = [
        'name',
        'flag_path',
        'flag_url'
    ];

    /**
     * Relation avec les joueurs
     */
    public function players()
    {
        return $this->hasMany(Player::class, 'nationality', 'name');
    }
}










