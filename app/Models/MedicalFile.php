<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Fichier médical conservé en base (contenu en base64, jamais sérialisé). */
class MedicalFile extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['content_base64'];

    public function ref(): string
    {
        return 'medical-file:' . $this->id;
    }
}
