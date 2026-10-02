<?php
namespace App\Models;
use Illuminate\Database\Eloquent\{Model,Builder};
final class ImagingInstance extends Model {
    protected $table='fit_imaging_instances';
    protected $guarded=['id'];
    protected $casts=['metadata'=>'array','content'=>'encrypted'];
    protected $hidden=['content'];
    public function scopeMetadataOnly(Builder $query): Builder {
        return $query->select(['id','study_id','original_name','mime_type','sha256','series_uid','sop_uid','sop_class_uid','metadata','uploaded_by','created_at','updated_at']);
    }
    public function study() { return $this->belongsTo(ImagingStudy::class,'study_id'); }
}
