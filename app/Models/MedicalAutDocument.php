<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
final class MedicalAutDocument extends Model
{
    protected $fillable=['tue_request_id','original_name','mime_type','sha256','content'];
    // Justificatifs chiffrés dans la base principale ; aucun lien public.
    protected $casts=['content'=>'encrypted'];
}
