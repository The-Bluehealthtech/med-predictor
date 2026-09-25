<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fifa_connect_persons', function (Blueprint $table) {
            $table->id();
            $table->string('person_fifa_id')->unique();
            $table->string('international_first_name')->nullable();
            $table->string('international_last_name')->nullable();
            $table->string('popular_name')->nullable();
            $table->string('local_first_name')->nullable();
            $table->string('local_last_name')->nullable();
            $table->string('local_birth_name')->nullable();
            $table->string('local_system_ma_id')->nullable();
            $table->string('local_language')->nullable();
            $table->string('local_country')->nullable();
            $table->string('gender');
            $table->string('nationality');
            $table->string('second_nationality')->nullable();
            $table->date('date_of_birth');
            $table->string('country_of_birth');
            $table->string('region_of_birth')->nullable();
            $table->string('place_of_birth');
            $table->foreignId('player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->timestamps();

            $table->index(['nationality', 'date_of_birth']);
        });

        Schema::create('fifa_connect_person_local_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('fifa_connect_persons')->cascadeOnDelete();
            $table->string('language');
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name');
            $table->string('birth_name')->nullable();
            $table->string('popular_name')->nullable();
            $table->timestamps();

            $table->unique(['person_id', 'language', 'last_name'], 'fc_person_local_name_unique');
        });

        Schema::create('fifa_connect_person_national_identifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('fifa_connect_persons')->cascadeOnDelete();
            $table->string('identifier');
            $table->string('nature');
            $table->string('country');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['person_id', 'identifier', 'nature', 'country'], 'fc_person_national_identifier_unique');
        });

        Schema::create('fifa_connect_organisations', function (Blueprint $table) {
            $table->id();
            $table->string('organisation_fifa_id')->nullable()->unique();
            $table->string('status');
            $table->string('international_name')->nullable();
            $table->string('international_short_name')->nullable();
            $table->string('local_name')->nullable();
            $table->string('local_short_name')->nullable();
            $table->string('local_system_ma_id')->nullable();
            $table->string('local_language')->nullable();
            $table->string('local_country')->nullable();
            $table->string('organisation_nature');
            $table->date('foundation_date')->nullable();
            $table->date('dissolution_date')->nullable();
            $table->string('parent_organisation_fifa_id')->nullable();
            $table->string('web_address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('logo_link')->nullable();
            $table->string('logo_mime_type')->nullable();
            $table->foreignId('association_id')->nullable()->constrained('associations')->nullOnDelete();
            $table->foreignId('club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->timestamps();

            $table->index('parent_organisation_fifa_id');
        });

        Schema::create('fifa_connect_organisation_local_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('fifa_connect_organisations')->cascadeOnDelete();
            $table->string('language');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->timestamps();

            $table->unique(['organisation_id', 'language', 'name'], 'fc_org_local_name_unique');
        });

        Schema::create('fifa_connect_organisation_national_identifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('fifa_connect_organisations')->cascadeOnDelete();
            $table->string('identifier');
            $table->string('nature');
            $table->string('country');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['organisation_id', 'identifier', 'nature', 'country'], 'fc_org_national_identifier_unique');
        });

        Schema::create('fifa_connect_supported_disciplines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('fifa_connect_organisations')->cascadeOnDelete();
            $table->string('discipline')->nullable();
            $table->string('gender')->nullable();
            $table->timestamps();

            $table->unique(['organisation_id', 'discipline', 'gender'], 'fc_org_discipline_unique');
        });

        Schema::create('fifa_connect_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');
            $table->string('country');
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('town');
            $table->string('address');
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
        });

        Schema::create('fifa_connect_pictures', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');
            $table->string('storage_mode');
            $table->longText('embedded_base64')->nullable();
            $table->string('picture_link')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
        });

        Schema::create('fifa_connect_mandatory_data', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->string('parameter_name');
            $table->timestamps();

            $table->unique(['service_name', 'parameter_name'], 'fc_mandatory_data_unique');
        });

        Schema::create('fifa_connect_mandatory_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandatory_data_id')->constrained('fifa_connect_mandatory_data')->cascadeOnDelete();
            $table->foreignId('parent_part_id')->nullable()->constrained('fifa_connect_mandatory_parts')->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_attribute');
            $table->timestamps();

            $table->index(['mandatory_data_id', 'parent_part_id'], 'fc_mandatory_part_parent_idx');
        });

        Schema::create('fifa_connect_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('fifa_connect_persons')->cascadeOnDelete();
            $table->string('person_fifa_id');
            $table->string('organisation_fifa_id');
            $table->string('registration_type');
            $table->string('status');
            $table->date('registration_valid_from');
            $table->date('registration_valid_to')->nullable();
            $table->string('level')->nullable();
            $table->string('discipline')->nullable();
            $table->string('registration_nature')->nullable();
            $table->string('club_training_category')->nullable();
            $table->string('match_official_role')->nullable();
            $table->string('team_official_role')->nullable();
            $table->string('organisation_official_role')->nullable();
            $table->foreignId('player_license_id')->nullable()->constrained('player_licenses')->nullOnDelete();
            $table->timestamps();

            $table->index(['person_fifa_id', 'status']);
            $table->index(['organisation_fifa_id', 'status']);
            $table->index(['registration_type', 'discipline']);
        });

        Schema::create('fifa_connect_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('fifa_connect_persons')->cascadeOnDelete();
            $table->string('certification_type');
            $table->string('status');
            $table->date('certification_valid_from');
            $table->date('certification_valid_to')->nullable();
            $table->string('certification_nature');
            $table->timestamps();

            $table->index(['person_id', 'certification_type', 'status'], 'fc_cert_person_type_status_idx');
        });

        Schema::create('fifa_connect_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('facility_fifa_id')->nullable()->unique();
            $table->string('status');
            $table->string('international_name')->nullable();
            $table->string('international_short_name')->nullable();
            $table->string('local_name')->nullable();
            $table->string('local_short_name')->nullable();
            $table->string('local_system_ma_id')->nullable();
            $table->string('local_language')->nullable();
            $table->string('local_country')->nullable();
            $table->string('organisation_fifa_id')->nullable();
            $table->string('parent_facility_fifa_id')->nullable();
            $table->string('web_address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->timestamps();

            $table->index('organisation_fifa_id');
            $table->index('parent_facility_fifa_id');
        });

        Schema::create('fifa_connect_facility_local_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('fifa_connect_facilities')->cascadeOnDelete();
            $table->string('language');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->timestamps();

            $table->unique(['facility_id', 'language', 'name'], 'fc_facility_local_name_unique');
        });

        Schema::create('fifa_connect_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('fifa_connect_facilities')->cascadeOnDelete();
            $table->unsignedInteger('order_number');
            $table->string('discipline');
            $table->unsignedInteger('capacity');
            $table->string('ground_nature');
            $table->float('length')->nullable();
            $table->float('width')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->unique(['facility_id', 'order_number']);
        });

        Schema::create('fifa_connect_competitions', function (Blueprint $table) {
            $table->id();
            $table->string('competition_fifa_id')->unique();
            $table->foreignId('parent_competition_id')->nullable()->constrained('fifa_connect_competitions')->nullOnDelete();
            $table->string('international_name');
            $table->string('international_short_name')->nullable();
            $table->string('organisation_fifa_id');
            $table->string('organisation_international_name')->nullable();
            $table->string('organisation_international_short_name')->nullable();
            $table->unsignedInteger('season');
            $table->string('status');
            $table->unsignedInteger('order_number')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->unsignedInteger('number_of_participants')->nullable();
            $table->string('system_nature');
            $table->unsignedInteger('system_multiplier')->nullable();
            $table->string('nature_fifa_id');
            $table->string('nature_international_name')->nullable();
            $table->string('nature_international_short_name');
            $table->string('team_character');
            $table->string('discipline');
            $table->string('age_category');
            $table->string('age_category_name')->nullable();
            $table->string('gender')->nullable();
            $table->foreignId('competition_id')->nullable()->constrained('competitions')->nullOnDelete();
            $table->timestamps();

            $table->index(['organisation_fifa_id', 'season']);
            $table->index(['status', 'discipline']);
        });

        Schema::create('fifa_connect_competition_local_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('fifa_connect_competitions')->cascadeOnDelete();
            $table->string('language');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->timestamps();

            $table->unique(['competition_id', 'language', 'name'], 'fc_comp_local_name_unique');
        });

        Schema::create('fifa_connect_competition_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('fifa_connect_competitions')->cascadeOnDelete();
            $table->string('organisation_fifa_id');
            $table->string('team_fifa_id')->nullable();
            $table->string('international_name')->nullable();
            $table->string('international_short_name')->nullable();
            $table->string('member_association')->nullable();
            $table->unsignedInteger('ranking_position')->nullable();
            $table->unsignedInteger('ranking_matches_played')->nullable();
            $table->unsignedInteger('ranking_wins')->nullable();
            $table->unsignedInteger('ranking_draws')->nullable();
            $table->unsignedInteger('ranking_losses')->nullable();
            $table->unsignedInteger('ranking_goals_for')->nullable();
            $table->unsignedInteger('ranking_goals_against')->nullable();
            $table->unsignedInteger('ranking_goal_difference')->nullable();
            $table->unsignedInteger('ranking_points')->nullable();
            $table->unsignedInteger('ranking_negative_points')->nullable();
            $table->timestamps();

            $table->index(['competition_id', 'organisation_fifa_id'], 'fc_comp_team_org_idx');
        });

        Schema::create('fifa_connect_competition_team_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_team_id')->constrained('fifa_connect_competition_teams')->cascadeOnDelete();
            $table->string('person_fifa_id');
            $table->string('role_type');
            $table->string('international_first_name')->nullable();
            $table->string('international_last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('member_association')->nullable();
            $table->timestamps();

            $table->unique(['competition_team_id', 'person_fifa_id', 'role_type'], 'fc_comp_team_person_unique');
        });

        Schema::create('fifa_connect_matches', function (Blueprint $table) {
            $table->id();
            $table->string('match_fifa_id')->unique();
            $table->string('status');
            $table->dateTime('date_time_local')->nullable();
            $table->unsignedInteger('matchday')->nullable();
            $table->unsignedInteger('attendance')->nullable();
            $table->string('competition_fifa_id');
            $table->string('competition_element_fifa_id')->nullable();
            $table->string('facility_fifa_id')->nullable();
            $table->foreignId('match_id')->nullable()->constrained('matches')->nullOnDelete();
            $table->timestamps();

            $table->index(['competition_fifa_id', 'status']);
        });

        Schema::create('fifa_connect_match_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('fifa_connect_matches')->cascadeOnDelete();
            $table->string('phase');
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->unsignedInteger('regular_time')->nullable();
            $table->unsignedInteger('stoppage_time')->nullable();
            $table->unsignedInteger('home_score');
            $table->unsignedInteger('away_score');
            $table->timestamps();
        });

        Schema::create('fifa_connect_match_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('fifa_connect_matches')->cascadeOnDelete();
            $table->string('event_id')->nullable();
            $table->string('match_phase');
            $table->unsignedInteger('minute');
            $table->unsignedInteger('stoppage_time')->nullable();
            $table->string('event_type');
            $table->string('event_detail_type')->nullable();
            $table->string('player_fifa_id')->nullable();
            $table->string('team_official_fifa_id')->nullable();
            $table->string('player_fifa_id_2')->nullable();
            $table->string('match_team')->nullable();
            $table->unsignedInteger('player_shirt_number')->nullable();
            $table->unsignedInteger('player_shirt_number_2')->nullable();
            $table->timestamps();

            $table->index(['match_id', 'minute']);
            $table->index(['event_type', 'match_phase']);
        });

        Schema::create('fifa_connect_match_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('fifa_connect_matches')->cascadeOnDelete();
            $table->string('team_nature');
            $table->string('organisation_fifa_id');
            $table->string('team_fifa_id')->nullable();
            $table->string('international_name')->nullable();
            $table->string('international_short_name')->nullable();
            $table->string('international_code')->nullable();
            $table->unsignedInteger('final_result')->nullable();
            $table->string('member_association')->nullable();
            $table->timestamps();

            $table->unique(['match_id', 'team_nature']);
        });

        Schema::create('fifa_connect_match_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_team_id')->constrained('fifa_connect_match_teams')->cascadeOnDelete();
            $table->string('person_fifa_id');
            $table->string('shirt_number');
            $table->boolean('captain');
            $table->boolean('goalkeeper');
            $table->boolean('starting_lineup');
            $table->boolean('played')->nullable();
            $table->string('international_first_name')->nullable();
            $table->string('international_last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('member_association')->nullable();
            $table->timestamps();

            $table->unique(['match_team_id', 'person_fifa_id']);
        });

        Schema::create('fifa_connect_match_officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('fifa_connect_matches')->cascadeOnDelete();
            $table->string('person_fifa_id');
            $table->string('role');
            $table->string('role_description')->nullable();
            $table->string('international_first_name')->nullable();
            $table->string('international_last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('member_association')->nullable();
            $table->timestamps();

            $table->index(['match_id', 'role']);
        });

        Schema::create('fifa_connect_team_officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_team_id')->constrained('fifa_connect_match_teams')->cascadeOnDelete();
            $table->string('person_fifa_id');
            $table->string('role');
            $table->string('role_description')->nullable();
            $table->string('international_first_name')->nullable();
            $table->string('international_last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('member_association')->nullable();
            $table->timestamps();

            $table->index(['match_team_id', 'role']);
        });

        Schema::create('fifa_connect_discipline_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_fifa_id')->unique();
            $table->string('organisation_fifa_id');
            $table->date('case_date');
            $table->string('offender_nature');
            $table->string('offender_organisation_fifa_id')->nullable();
            $table->string('offender_person_fifa_id')->nullable();
            $table->string('offender_person_nature')->nullable();
            $table->string('team_official_nature')->nullable();
            $table->string('match_official_nature')->nullable();
            $table->string('organisation_official_nature')->nullable();
            $table->text('description');
            $table->string('status');
            $table->string('competition_fifa_id')->nullable();
            $table->string('match_fifa_id')->nullable();
            $table->foreignId('match_event_id')->nullable()->constrained('fifa_connect_match_events')->nullOnDelete();
            $table->timestamps();

            $table->index(['offender_person_fifa_id', 'status']);
            $table->index(['offender_organisation_fifa_id', 'status']);
        });

        Schema::create('fifa_connect_sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('fifa_connect_discipline_cases')->cascadeOnDelete();
            $table->string('status');
            $table->string('person_sanction_nature')->nullable();
            $table->string('organisation_sanction_nature')->nullable();
            $table->double('value')->nullable();
            $table->string('measure')->nullable();
            $table->string('currency')->nullable();
            $table->double('value_served')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->timestamps();
        });

        Schema::create('fifa_connect_data_holders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('association_id')->constrained('associations')->cascadeOnDelete();
            $table->string('person_fifa_id');
            $table->string('claim_status')->default('pending');
            $table->string('claim_reason')->nullable();
            $table->timestamp('registered_as_holder_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->string('merged_into_fifa_id')->nullable();
            $table->boolean('remote_deleted')->default(false);
            $table->string('last_remote_status')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->unique(['association_id', 'person_fifa_id'], 'fc_data_holder_unique');
        });

        Schema::create('fifa_connect_exchange_messages', function (Blueprint $table) {
            $table->id();
            $table->string('scenario_type');
            $table->string('message_nature')->nullable();
            $table->string('event_id')->nullable();
            $table->string('subject_fifa_id')->nullable();
            $table->timestamp('exported_at')->nullable();
            $table->string('direction');
            $table->string('validation_status')->default('pending');
            $table->text('validation_error')->nullable();
            $table->string('payload_hash')->nullable();
            $table->longText('payload_xml')->nullable();
            $table->timestamps();

            $table->index(['scenario_type', 'direction']);
            $table->index(['subject_fifa_id', 'created_at']);
        });
    }

    public function down(): void
    {
        $tables = [
            'fifa_connect_exchange_messages',
            'fifa_connect_data_holders',
            'fifa_connect_sanctions',
            'fifa_connect_discipline_cases',
            'fifa_connect_team_officials',
            'fifa_connect_match_officials',
            'fifa_connect_match_players',
            'fifa_connect_match_teams',
            'fifa_connect_match_events',
            'fifa_connect_match_phases',
            'fifa_connect_matches',
            'fifa_connect_competition_team_persons',
            'fifa_connect_competition_teams',
            'fifa_connect_competition_local_names',
            'fifa_connect_competitions',
            'fifa_connect_fields',
            'fifa_connect_facility_local_names',
            'fifa_connect_facilities',
            'fifa_connect_certifications',
            'fifa_connect_registrations',
            'fifa_connect_mandatory_parts',
            'fifa_connect_mandatory_data',
            'fifa_connect_pictures',
            'fifa_connect_addresses',
            'fifa_connect_supported_disciplines',
            'fifa_connect_organisation_national_identifiers',
            'fifa_connect_organisation_local_names',
            'fifa_connect_organisations',
            'fifa_connect_person_national_identifiers',
            'fifa_connect_person_local_names',
            'fifa_connect_persons',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
