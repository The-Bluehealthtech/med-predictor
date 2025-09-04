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
        // Players table indexes
        Schema::table('players', function (Blueprint $table) {
            if (!Schema::hasIndex('players', 'players_club_id_position_index')) {
                $table->index(['club_id', 'position']);
            }
            if (!Schema::hasIndex('players', 'players_association_id_nationality_index')) {
                $table->index(['association_id', 'nationality']);
            }
            if (!Schema::hasIndex('players', 'players_overall_rating_potential_rating_index')) {
                $table->index(['overall_rating', 'potential_rating']);
            }
            if (!Schema::hasIndex('players', 'players_date_of_birth_index')) {
                $table->index(['date_of_birth']);
            }
            if (!Schema::hasIndex('players', 'players_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            if (!Schema::hasIndex('players', 'players_fifa_connect_id_index')) {
                $table->index(['fifa_connect_id']);
            }
        });

        // Clubs table indexes
        Schema::table('clubs', function (Blueprint $table) {
            if (!Schema::hasIndex('clubs', 'clubs_association_id_status_index')) {
                $table->index(['association_id', 'status']);
            }
            if (!Schema::hasIndex('clubs', 'clubs_country_league_index')) {
                $table->index(['country', 'league']);
            }
            if (!Schema::hasIndex('clubs', 'clubs_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            if (!Schema::hasIndex('clubs', 'clubs_fifa_connect_id_index')) {
                $table->index(['fifa_connect_id']);
            }
        });

        // Health records table indexes
        Schema::table('health_records', function (Blueprint $table) {
            if (!Schema::hasIndex('health_records', 'health_records_player_id_record_date_index')) {
                $table->index(['player_id', 'record_date']);
            }
            if (!Schema::hasIndex('health_records', 'health_records_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
            if (!Schema::hasIndex('health_records', 'health_records_status_record_date_index')) {
                $table->index(['status', 'record_date']);
            }
            if (!Schema::hasIndex('health_records', 'health_records_risk_score_prediction_confidence_index')) {
                $table->index(['risk_score', 'prediction_confidence']);
            }
            if (!Schema::hasIndex('health_records', 'health_records_next_checkup_date_index')) {
                $table->index(['next_checkup_date']);
            }
        });

        // Medical predictions table indexes
        Schema::table('medical_predictions', function (Blueprint $table) {
            if (!Schema::hasIndex('medical_predictions', 'medical_predictions_player_id_prediction_type_index')) {
                $table->index(['player_id', 'prediction_type']);
            }
            if (!Schema::hasIndex('medical_predictions', 'medical_predictions_health_record_id_status_index')) {
                $table->index(['health_record_id', 'status']);
            }
            if (!Schema::hasIndex('medical_predictions', 'medical_predictions_prediction_date_status_index')) {
                $table->index(['prediction_date', 'status']);
            }
            if (!Schema::hasIndex('medical_predictions', 'medical_predictions_risk_probability_confidence_score_index')) {
                $table->index(['risk_probability', 'confidence_score']);
            }
            if (!Schema::hasIndex('medical_predictions', 'medical_predictions_valid_until_index')) {
                $table->index(['valid_until']);
            }
        });

        // Matches table indexes
        Schema::table('matches', function (Blueprint $table) {
            if (!Schema::hasIndex('matches', 'matches_competition_id_match_status_index')) {
                $table->index(['competition_id', 'match_status']);
            }
            if (!Schema::hasIndex('matches', 'matches_home_team_id_away_team_id_index')) {
                $table->index(['home_team_id', 'away_team_id']);
            }
            if (!Schema::hasIndex('matches', 'matches_match_date_kickoff_time_index')) {
                $table->index(['match_date', 'kickoff_time']);
            }
            if (!Schema::hasIndex('matches', 'matches_match_status_match_date_index')) {
                $table->index(['match_status', 'match_date']);
            }
            if (!Schema::hasIndex('matches', 'matches_venue_stadium_index')) {
                $table->index(['venue', 'stadium']);
            }
        });

        // Competitions table indexes
        Schema::table('competitions', function (Blueprint $table) {
            if (!Schema::hasIndex('competitions', 'competitions_season_id_status_index')) {
                $table->index(['season_id', 'status']);
            }
            if (!Schema::hasIndex('competitions', 'competitions_association_id_type_index')) {
                $table->index(['association_id', 'type']);
            }
            if (!Schema::hasIndex('competitions', 'competitions_status_start_date_index')) {
                $table->index(['status', 'start_date']);
            }
            if (!Schema::hasIndex('competitions', 'competitions_fifa_connect_id_index')) {
                $table->index(['fifa_connect_id']);
            }
        });

        // Teams table indexes
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasIndex('teams', 'teams_club_id_type_index')) {
                $table->index(['club_id', 'type']);
            }
            if (!Schema::hasIndex('teams', 'teams_association_id_status_index')) {
                $table->index(['association_id', 'status']);
            }
            if (!Schema::hasIndex('teams', 'teams_status_season_index')) {
                $table->index(['status', 'season']);
            }
        });

        // Lineups table indexes
        Schema::table('lineups', function (Blueprint $table) {
            if (!Schema::hasIndex('lineups', 'lineups_team_id_match_id_index')) {
                $table->index(['team_id', 'match_id']);
            }
            if (!Schema::hasIndex('lineups', 'lineups_club_id_competition_id_index')) {
                $table->index(['club_id', 'competition_id']);
            }
            if (!Schema::hasIndex('lineups', 'lineups_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
        });

        // Lineup players table indexes
        Schema::table('lineup_players', function (Blueprint $table) {
            if (!Schema::hasIndex('lineup_players', 'lineup_players_lineup_id_is_substitute_index')) {
                $table->index(['lineup_id', 'is_substitute']);
            }
            if (!Schema::hasIndex('lineup_players', 'lineup_players_player_id_fitness_status_index')) {
                $table->index(['player_id', 'fitness_status']);
            }
            if (!Schema::hasIndex('lineup_players', 'lineup_players_assigned_position_position_order_index')) {
                $table->index(['assigned_position', 'position_order']);
            }
        });

        // Player licenses table indexes
        Schema::table('player_licenses', function (Blueprint $table) {
            if (!Schema::hasIndex('player_licenses', 'player_licenses_player_id_status_index')) {
                $table->index(['player_id', 'status']);
            }
            if (!Schema::hasIndex('player_licenses', 'player_licenses_club_id_status_index')) {
                $table->index(['club_id', 'status']);
            }
            if (!Schema::hasIndex('player_licenses', 'player_licenses_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            if (!Schema::hasIndex('player_licenses', 'player_licenses_expiry_date_index')) {
                $table->index(['expiry_date']);
            }
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasIndex('users', 'users_role_entity_type_index')) {
                $table->index(['role', 'entity_type']);
            }
            if (!Schema::hasIndex('users', 'users_club_id_status_index')) {
                $table->index(['club_id', 'status']);
            }
            if (!Schema::hasIndex('users', 'users_association_id_status_index')) {
                $table->index(['association_id', 'status']);
            }
            if (!Schema::hasIndex('users', 'users_status_last_login_at_index')) {
                $table->index(['status', 'last_login_at']);
            }
        });

        // Match events table indexes
        Schema::table('match_events', function (Blueprint $table) {
            if (!Schema::hasIndex('match_events', 'match_events_match_id_event_type_index')) {
                $table->index(['match_id', 'event_type']);
            }
            if (!Schema::hasIndex('match_events', 'match_events_player_id_event_type_index')) {
                $table->index(['player_id', 'event_type']);
            }
            if (!Schema::hasIndex('match_events', 'match_events_team_id_minute_index')) {
                $table->index(['team_id', 'minute']);
            }
            if (!Schema::hasIndex('match_events', 'match_events_event_type_minute_index')) {
                $table->index(['event_type', 'minute']);
            }
        });

        // Audit trail table indexes
        Schema::table('audit_trails', function (Blueprint $table) {
            if (!Schema::hasIndex('audit_trails', 'audit_trails_user_id_action_index')) {
                $table->index(['user_id', 'action']);
            }
            if (!Schema::hasIndex('audit_trails', 'audit_trails_entity_type_entity_id_index')) {
                $table->index(['entity_type', 'entity_id']);
            }
            if (!Schema::hasIndex('audit_trails', 'audit_trails_action_created_at_index')) {
                $table->index(['action', 'created_at']);
            }
            if (!Schema::hasIndex('audit_trails', 'audit_trails_ip_address_created_at_index')) {
                $table->index(['ip_address', 'created_at']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Players table indexes
        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex(['club_id', 'position']);
            $table->dropIndex(['association_id', 'nationality']);
            $table->dropIndex(['overall_rating', 'potential_rating']);
            $table->dropIndex(['date_of_birth']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['fifa_connect_id']);
        });

        // Clubs table indexes
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropIndex(['association_id', 'status']);
            $table->dropIndex(['country', 'league']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['fifa_connect_id']);
        });

        // Health records table indexes
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropIndex(['player_id', 'record_date']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['status', 'record_date']);
            $table->dropIndex(['risk_score', 'prediction_confidence']);
            $table->dropIndex(['next_checkup_date']);
        });

        // Medical predictions table indexes
        Schema::table('medical_predictions', function (Blueprint $table) {
            $table->dropIndex(['player_id', 'prediction_type']);
            $table->dropIndex(['health_record_id', 'status']);
            $table->dropIndex(['prediction_date', 'status']);
            $table->dropIndex(['risk_probability', 'confidence_score']);
            $table->dropIndex(['valid_until']);
        });

        // Matches table indexes
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex(['competition_id', 'match_status']);
            $table->dropIndex(['home_team_id', 'away_team_id']);
            $table->dropIndex(['match_date', 'kickoff_time']);
            $table->dropIndex(['match_status', 'match_date']);
            $table->dropIndex(['venue', 'stadium']);
        });

        // Competitions table indexes
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropIndex(['season_id', 'status']);
            $table->dropIndex(['association_id', 'type']);
            $table->dropIndex(['status', 'start_date']);
            $table->dropIndex(['fifa_connect_id']);
        });

        // Teams table indexes
        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['club_id', 'type']);
            $table->dropIndex(['association_id', 'status']);
            $table->dropIndex(['status', 'season']);
        });

        // Lineups table indexes
        Schema::table('lineups', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'match_id']);
            $table->dropIndex(['club_id', 'competition_id']);
            $table->dropIndex(['status', 'created_at']);
        });

        // Lineup players table indexes
        Schema::table('lineup_players', function (Blueprint $table) {
            $table->dropIndex(['lineup_id', 'is_substitute']);
            $table->dropIndex(['player_id', 'fitness_status']);
            $table->dropIndex(['assigned_position', 'position_order']);
        });

        // Player licenses table indexes
        Schema::table('player_licenses', function (Blueprint $table) {
            $table->dropIndex(['player_id', 'status']);
            $table->dropIndex(['club_id', 'status']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['expiry_date']);
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'entity_type']);
            $table->dropIndex(['club_id', 'status']);
            $table->dropIndex(['association_id', 'status']);
            $table->dropIndex(['status', 'last_login_at']);
        });

        // Match events table indexes
        Schema::table('match_events', function (Blueprint $table) {
            $table->dropIndex(['match_id', 'event_type']);
            $table->dropIndex(['player_id', 'event_type']);
            $table->dropIndex(['team_id', 'minute']);
            $table->dropIndex(['event_type', 'minute']);
        });

        // Audit trail table indexes
        Schema::table('audit_trails', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'action']);
            $table->dropIndex(['entity_type', 'entity_id']);
            $table->dropIndex(['action', 'created_at']);
            $table->dropIndex(['ip_address', 'created_at']);
        });
    }
}; 