<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
final class HealthRecordDocument extends Model
{
    protected $fillable=['health_record_id','player_id','section','entry_id','exam_date',
        'original_name','mime_type','sha256','content','recorded_by'];
    protected $casts=['content'=>'encrypted','exam_date'=>'date'];
    protected $hidden=['content'];

    protected static function booted(): void
    {
        // Une pièce ne peut jamais être rattachée au joueur d'un autre dossier.
        static::saving(function (self $document) {
            $playerId = \Illuminate\Support\Facades\DB::table('health_records')
                ->where('id', $document->health_record_id)->sharedLock()->value('player_id');
            if ($playerId === null || (int) $playerId !== (int) $document->player_id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'document' => 'Le joueur de la pièce jointe doit correspondre au dossier médical.',
                ]);
            }
        });
    }
}
