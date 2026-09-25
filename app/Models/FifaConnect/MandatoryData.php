<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MandatoryData extends Model
{
    protected $table = 'fifa_connect_mandatory_data';
    protected $guarded = [];
    protected $casts = ['received_at' => 'datetime'];

    public function parts(): HasMany
    {
        return $this->hasMany(MandatoryPart::class, 'mandatory_data_id')
            ->whereNull('parent_part_id')->orderBy('order_number');
    }
}
