<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('fit_imaging_studies', function (Blueprint $t) {
            $t->id(); $t->foreignId('health_record_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('player_id')->index(); $t->date('exam_date');
            $t->string('modality', 8); $t->string('purpose', 20)->default('general');
            $t->string('body_region'); $t->text('indication')->nullable(); $t->string('source');
            $t->string('study_uid', 64)->unique(); $t->json('source_identity')->nullable();
            $t->boolean('identity_checked')->default(false); $t->text('identity_note')->nullable();
            $t->unsignedBigInteger('identity_checked_by')->nullable(); $t->timestamp('identity_checked_at')->nullable();
            $t->unsignedBigInteger('created_by'); $t->timestamps();
        });
        Schema::create('fit_imaging_instances', function (Blueprint $t) {
            $t->id(); $t->foreignId('study_id')->constrained('fit_imaging_studies')->cascadeOnDelete();
            $t->string('original_name'); $t->string('mime_type'); $t->string('sha256',64);
            $t->string('series_uid',64); $t->string('sop_uid',64)->unique(); $t->string('sop_class_uid',64);
            $t->json('metadata'); $t->longText('content'); $t->unsignedBigInteger('uploaded_by'); $t->timestamps();
            $t->unique(['study_id','sha256']);
        });
        Schema::create('fit_imaging_reports', function (Blueprint $t) {
            $t->id(); $t->foreignId('study_id')->constrained('fit_imaging_studies')->cascadeOnDelete();
            $t->unsignedInteger('version'); $t->unsignedInteger('edit_revision')->default(1); $t->string('status',20)->default('draft');
            $t->text('technique')->nullable(); $t->string('quality',24)->default('interpretable');
            $t->text('findings')->nullable(); $t->text('conclusion')->nullable();
            $t->json('patient_snapshot')->nullable(); $t->string('validator_name')->nullable(); $t->json('reference_images')->nullable(); $t->json('age_review')->nullable();
            $t->unsignedBigInteger('authored_by'); $t->unsignedBigInteger('validated_by')->nullable();
            $t->timestamp('validated_at')->nullable(); $t->string('sop_uid',64)->unique();
            $t->string('pacs_status',24)->default('not_sent'); $t->timestamp('pacs_sent_at')->nullable();
            $t->timestamps(); $t->unique(['study_id','version']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('fit_imaging_reports'); Schema::dropIfExists('fit_imaging_instances'); Schema::dropIfExists('fit_imaging_studies');
    }
};
