<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventMessage extends Model
{
    protected $table = 'fifa_connect_event_messages';

    protected $guarded = [];

    protected $casts = [
        'export_date_time' => 'date',
    ];

    public function scores(): HasMany
    {
        return $this->hasMany(
            EventMessageScore::class,
            'event_message_id'
        )->orderBy('order_number');
    }
}
