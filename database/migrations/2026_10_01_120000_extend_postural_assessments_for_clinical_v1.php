<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postural_assessments', function (Blueprint $table) {
            $table->foreignId('health_record_id')->nullable()->after('user_id')->constrained('health_records')->nullOnDelete();
            $table->text('overall_impression')->nullable()->after('clinical_notes');
            $table->json('context')->nullable()->after('recommendations');
            $table->timestamp('validated_at')->nullable()->after('assessment_date');
            $table->foreignId('validated_by')->nullable()->after('validated_at')->constrained('users')->nullOnDelete();

            $table->index('health_record_id');
            $table->index(['status', 'assessment_date']);
        });
    }

    public function down(): void
    {
        Schema::table('postural_assessments', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropForeign(['health_record_id']);
            $table->dropIndex(['status', 'assessment_date']);
            $table->dropIndex(['health_record_id']);
            $table->dropColumn(['health_record_id', 'overall_impression', 'context', 'validated_at', 'validated_by']);
        });
    }
};
