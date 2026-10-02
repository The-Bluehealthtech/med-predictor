<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
final class ImagingReport extends Model {
    protected $table='fit_imaging_reports';
    protected $guarded=['id'];
    protected $casts=['patient_snapshot'=>'array','reference_images'=>'array','age_review'=>'array','validated_at'=>'datetime','pacs_sent_at'=>'datetime'];
    public function study() { return $this->belongsTo(ImagingStudy::class,'study_id'); }
    public function validator() { return $this->belongsTo(User::class,'validated_by'); }
    protected static function booted(): void {
        static::updating(function (self $report) {
            if ($report->getOriginal('status')==='validated' && array_diff(array_keys($report->getDirty()),['pacs_status','pacs_sent_at','updated_at'])) {
                throw ValidationException::withMessages(['report'=>'Le rapport validé est immuable. Créez une nouvelle version.']);
            }
        });
    }
}
