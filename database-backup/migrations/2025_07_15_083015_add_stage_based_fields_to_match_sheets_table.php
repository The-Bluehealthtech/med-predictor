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
            // Stage-based lifecycle fields
            $table->enum('stage', ['in_progress', 'before_game_signed', 'after_game_signed', 'fa_validated'])
                  ->default('in_progress')
                  ->after('status');
            
            // Stage timestamps
            $table->timestamp('stage_in_progress_at')->nullable()->after('stage');
            $table->timestamp('stage_before_game_signed_at')->nullable()->after('stage_in_progress_at');
            $table->timestamp('stage_after_game_signed_at')->nullable()->after('stage_before_game_signed_at');
            $table->timestamp('stage_fa_validated_at')->nullable()->after('stage_after_game_signed_at');
            
            // Team lineup signatures
            $table->string('home_team_lineup_signature')->nullable()->after('stage_fa_validated_at');
            $table->string('away_team_lineup_signature')->nullable()->after('home_team_lineup_signature');
            $table->timestamp('home_team_lineup_signed_at')->nullable()->after('away_team_lineup_signature');
            $table->timestamp('away_team_lineup_signed_at')->nullable()->after('home_team_lineup_signed_at');
            
            // Team post-match signatures and comments
            $table->string('home_team_post_match_signature')->nullable()->after('away_team_lineup_signed_at');
            $table->string('away_team_post_match_signature')->nullable()->after('home_team_post_match_signature');
            $table->timestamp('home_team_post_match_signed_at')->nullable()->after('away_team_post_match_signature');
            $table->timestamp('away_team_post_match_signed_at')->nullable()->after('home_team_post_match_signed_at');
            $table->text('home_team_post_match_comments')->nullable()->after('home_team_post_match_signed_at');
            $table->text('away_team_post_match_comments')->nullable()->after('home_team_post_match_comments');
            
            // FA validation fields
            $table->foreignId('fa_validated_by')->nullable()->constrained('users')->onDelete('set null')->after('away_team_post_match_comments');
            $table->text('fa_validation_notes')->nullable()->after('fa_validated_by');
            
            // Referee assignment
            $table->foreignId('assigned_referee_id')->nullable()->constrained('users')->onDelete('set null')->after('fa_validation_notes');
            $table->timestamp('referee_assigned_at')->nullable()->after('assigned_referee_id');
            
            // Lineup lock status
            $table->boolean('lineups_locked')->default(false)->after('referee_assigned_at');
            $table->timestamp('lineups_locked_at')->nullable()->after('lineups_locked');
            
            // Match events lock status
            $table->boolean('match_events_locked')->default(false)->after('lineups_locked_at');
            $table->timestamp('match_events_locked_at')->nullable()->after('match_events_locked');
            
            // Audit trail
            $table->json('stage_transition_log')->nullable()->after('match_events_locked_at');
            $table->json('user_action_log')->nullable()->after('stage_transition_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_sheets', function (Blueprint $table) {
            $table->dropForeign(['fa_validated_by']);
            $table->dropForeign(['assigned_referee_id']);
            
            $table->dropColumn([
                'stage',
                'stage_in_progress_at',
                'stage_before_game_signed_at',
                'stage_after_game_signed_at',
                'stage_fa_validated_at',
                'home_team_lineup_signature',
                'away_team_lineup_signature',
                'home_team_lineup_signed_at',
                'away_team_lineup_signed_at',
                'home_team_post_match_signature',
                'away_team_post_match_signature',
                'home_team_post_match_signed_at',
                'away_team_post_match_signed_at',
                'home_team_post_match_comments',
                'away_team_post_match_comments',
                'fa_validated_by',
                'fa_validation_notes',
                'assigned_referee_id',
                'referee_assigned_at',
                'lineups_locked',
                'lineups_locked_at',
                'match_events_locked',
                'match_events_locked_at',
                'stage_transition_log',
                'user_action_log'
            ]);
        });
    }
};
