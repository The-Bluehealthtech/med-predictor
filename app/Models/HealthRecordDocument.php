<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
final class HealthRecordDocument extends Model
{
    protected $fillable=['health_record_id','player_id','section','entry_id','exam_date',
        'original_name','mime_type','sha256','content','recorded_by'];
    protected $casts=['content'=>'encrypted','exam_date'=>'date'];
    protected $hidden=['content'];
}
