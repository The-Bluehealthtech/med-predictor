<?php

namespace App\Models\FifaConnect\Concerns;

use App\Models\FifaConnect\Picture;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasFifaPicture
{
    public function picture(): HasOne
    {
        return $this->hasOne(
            Picture::class,
            'owner_id'
        )->where(
            'owner_type',
            static::FIFA_PICTURE_OWNER_TYPE
        );
    }
}
