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
            // Match Information
            $table->string('match_number')->nullable()->after('match_id');
            $table->string('stadium_venue')->nullable()->after('match_number');
            $table->string('weather_conditions')->nullable()->after('stadium_venue');
            $table->string('pitch_conditions')->nullable()->after('weather_conditions');
            $table->time('kickoff_time')->nullable()->after('pitch_conditions');
            
            // Team Rosters (JSON fields for flexibility)
            $table->json('home_team_roster')->nullable()->after('kickoff_time');
            $table->json('away_team_roster')->nullable()->after('home_team_roster');
            $table->json('home_team_substitutes')->nullable()->after('away_team_roster');
            $table->json('away_team_substitutes')->nullable()->after('home_team_substitutes');
            
            // Team Staff
            $table->string('home_team_coach')->nullable()->after('away_team_substitutes');
            $table->string('away_team_coach')->nullable()->after('home_team_coach');
            $table->string('home_team_manager')->nullable()->after('away_team_coach');
            $table->string('away_team_manager')->nullable()->after('home_team_manager');
            
            // Match Officials
            $table->foreignId('main_referee_id')->nullable()->constrained('users')->onDelete('set null')->after('away_team_manager');
            $table->foreignId('assistant_referee_1_id')->nullable()->constrained('users')->onDelete('set null')->after('main_referee_id');
            $table->foreignId('assistant_referee_2_id')->nullable()->constrained('users')->onDelete('set null')->after('assistant_referee_1_id');
            $table->foreignId('fourth_official_id')->nullable()->constrained('users')->onDelete('set null')->after('assistant_referee_2_id');
            $table->foreignId('var_referee_id')->nullable()->constrained('users')->onDelete('set null')->after('fourth_official_id');
            $table->foreignId('var_assistant_id')->nullable()->constrained('users')->onDelete('set null')->after('var_referee_id');
            
            // Match Statistics
            $table->json('match_statistics')->nullable()->after('var_assistant_id');
            
            // Referee Report
            $table->text('referee_report')->nullable()->after('match_statistics');
            $table->string('match_status')->default('completed')->after('referee_report'); // completed, suspended, abandoned
            $table->text('suspension_reason')->nullable()->after('match_status');
            $table->text('crowd_issues')->nullable()->after('suspension_reason');
            $table->text('protests_incidents')->nullable()->after('crowd_issues');
            
            // Team Signatures
            $table->string('home_team_signature')->nullable()->after('protests_incidents');
            $table->string('away_team_signature')->nullable()->after('home_team_signature');
            $table->timestamp('home_team_signed_at')->nullable()->after('away_team_signature');
            $table->timestamp('away_team_signed_at')->nullable()->after('home_team_signed_at');
            
            // Match Observer
            $table->foreignId('match_observer_id')->nullable()->constrained('users')->onDelete('set null')->after('away_team_signed_at');
            $table->text('observer_comments')->nullable()->after('match_observer_id');
            $table->timestamp('observer_signed_at')->nullable()->after('observer_comments');
            
            // Digital Signatures
            $table->string('referee_digital_signature')->nullable()->after('observer_signed_at');
            $table->timestamp('referee_signed_at')->nullable()->after('referee_digital_signature');
            
            // Penalty Shootout Data
            $table->json('penalty_shootout_data')->nullable()->after('referee_signed_at');
            
            // VAR Decisions
            $table->json('var_decisions')->nullable()->after('penalty_shootout_data');
            
            // Match Sheet Version
            $table->integer('version')->default(1)->after('var_decisions');
            $table->json('change_log')->nullable()->after('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_sheets', function (Blueprint $table) {
            $table->dropForeign(['main_referee_id']);
            $table->dropForeign(['assistant_referee_1_id']);
            $table->dropForeign(['assistant_referee_2_id']);
            $table->dropForeign(['fourth_official_id']);
            $table->dropForeign(['var_referee_id']);
            $table->dropForeign(['var_assistant_id']);
            $table->dropForeign(['match_observer_id']);
            
            $table->dropColumn([
                'match_number',
                'stadium_venue',
                'weather_conditions',
                'pitch_conditions',
                'kickoff_time',
                'home_team_roster',
                'away_team_roster',
                'home_team_substitutes',
                'away_team_substitutes',
                'home_team_coach',
                'away_team_coach',
                'home_team_manager',
                'away_team_manager',
                'main_referee_id',
                'assistant_referee_1_id',
                'assistant_referee_2_id',
                'fourth_official_id',
                'var_referee_id',
                'var_assistant_id',
                'match_statistics',
                'referee_report',
                'match_status',
                'suspension_reason',
                'crowd_issues',
                'protests_incidents',
                'home_team_signature',
                'away_team_signature',
                'home_team_signed_at',
                'away_team_signed_at',
                'match_observer_id',
                'observer_comments',
                'observer_signed_at',
                'referee_digital_signature',
                'referee_signed_at',
                'penalty_shootout_data',
                'var_decisions',
                'version',
                'change_log'
            ]);
        });
    }
};
