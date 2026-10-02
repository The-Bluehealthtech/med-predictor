<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
final class ImagingStudy extends Model {
    protected $table='fit_imaging_studies';
    protected $guarded=['id'];
    protected $casts=['exam_date'=>'date','source_identity'=>'array','identity_checked'=>'boolean','identity_checked_at'=>'datetime'];
    public function instances() { return $this->hasMany(ImagingInstance::class,'study_id'); }
    public function reports() { return $this->hasMany(ImagingReport::class,'study_id')->orderByDesc('version'); }
    public function player() { return $this->belongsTo(Player::class); }
    public function healthRecord() { return $this->belongsTo(HealthRecord::class); }
}
