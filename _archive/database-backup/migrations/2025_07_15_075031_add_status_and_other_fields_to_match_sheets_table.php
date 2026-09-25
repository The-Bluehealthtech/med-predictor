<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('match_sheets', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('match_id');
            $table->foreignId('referee_id')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->integer('home_team_score')->nullable()->after('referee_id');
            $table->integer('away_team_score')->nullable()->after('home_team_score');
            $table->text('notes')->nullable()->after('away_team_score');
            $table->timestamp('submitted_at')->nullable()->after('notes');
            $table->timestamp('validated_at')->nullable()->after('submitted_at');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null')->after('validated_at');
            $table->text('validation_notes')->nullable()->after('validated_by');
            $table->timestamp('rejected_at')->nullable()->after('validation_notes');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null')->after('rejected_at');
            $table->text('rejection_reason')->nullable()->after('rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_sheets', function (Blueprint $table) {
            $table->dropForeign(['referee_id']);
            $table->dropForeign(['validated_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn([
                'status',
                'referee_id',
                'home_team_score',
                'away_team_score',
                'notes',
                'submitted_at',
                'validated_at',
                'validated_by',
                'validation_notes',
                'rejected_at',
                'rejected_by',
                'rejection_reason'
            ]);
        });
    }
};
