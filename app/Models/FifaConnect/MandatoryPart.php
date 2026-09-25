<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MandatoryPart extends Model
{
    protected $table = 'fifa_connect_mandatory_parts';
    protected $guarded = [];
    protected $casts = ['is_attribute' => 'boolean'];

    public function subPath(): HasOne
    {
        return $this->hasOne(self::class, 'parent_part_id');
    }
}
