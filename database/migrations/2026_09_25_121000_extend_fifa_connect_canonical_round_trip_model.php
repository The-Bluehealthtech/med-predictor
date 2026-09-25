<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'fifa_connect_certifications',
            function (Blueprint $table) {
                $table->string('description')->nullable()
                    ->after('certification_nature');
            }
        );

        Schema::table(
            'fifa_connect_matches',
            function (Blueprint $table) {
                $table->string(
                    'facility_international_short_name'
                )->nullable()->after('facility_fifa_id');
            }
        );

        Schema::create(
            'fifa_connect_match_competition_contexts',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('match_id')
                    ->constrained('fifa_connect_matches')
                    ->cascadeOnDelete();
                $table->foreignId('parent_context_id')
                    ->nullable()
                    ->constrained(
                        'fifa_connect_match_competition_contexts'
                    )
                    ->cascadeOnDelete();
                $table->string('competition_fifa_id');
                $table->string('international_name');
                $table->string(
                    'international_short_name'
                )->nullable();
                $table->string(
                    'organisation_international_name'
                )->nullable();
                $table->string(
                    'organisation_international_short_name'
                );
                $table->timestamps();

                $table->index([
                    'match_id',
                    'competition_fifa_id',
                ], 'fc_match_comp_context_idx');
            }
        );

        Schema::create(
            'fifa_connect_match_facility_contexts',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('match_id')
                    ->unique()
                    ->constrained('fifa_connect_matches')
                    ->cascadeOnDelete();
                $table->string('facility_fifa_id');
                $table->string('international_name')
                    ->nullable();
                $table->string(
                    'international_short_name'
                )->nullable();
                $table->unsignedInteger(
                    'field_order_number'
                )->nullable();
                $table->unsignedInteger('capacity')
                    ->nullable();
                $table->string('town');
                $table->string('country');
                $table->timestamps();
            }
        );

        Schema::create(
            'fifa_connect_case_match_events',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('case_id')
                    ->unique()
                    ->constrained(
                        'fifa_connect_discipline_cases'
                    )
                    ->cascadeOnDelete();
                $table->string('match_phase');
                $table->unsignedInteger('minute');
                $table->unsignedInteger(
                    'stoppage_time'
                )->nullable();
                $table->string('event_type');
                $table->string(
                    'event_detail_type'
                )->nullable();
                $table->string('player_fifa_id')
                    ->nullable();
                $table->string(
                    'team_official_fifa_id'
                )->nullable();
                $table->string('player_fifa_id_2')
                    ->nullable();
                $table->string('match_team')
                    ->nullable();
                $table->timestamps();
            }
        );

        Schema::create(
            'fifa_connect_event_messages',
            function (Blueprint $table) {
                $table->id();
                $table->string('message_nature');
                $table->string('match_fifa_id');
                $table->date('export_date_time');
                $table->string('event_id');
                $table->string('match_phase');
                $table->unsignedInteger('minute');
                $table->unsignedInteger(
                    'stoppage_time'
                )->nullable();
                $table->string('event_type');
                $table->string(
                    'event_detail_type'
                )->nullable();
                $table->string('player_fifa_id')
                    ->nullable();
                $table->string(
                    'team_official_fifa_id'
                )->nullable();
                $table->string('player_fifa_id_2')
                    ->nullable();
                $table->string('match_team')
                    ->nullable();
                $table->unsignedInteger(
                    'player_shirt_number'
                )->nullable();
                $table->unsignedInteger(
                    'player_shirt_number_2'
                )->nullable();
                $table->timestamps();

                $table->index([
                    'match_fifa_id',
                    'event_id',
                ], 'fc_event_message_match_event_idx');
            }
        );

        Schema::create(
            'fifa_connect_event_message_scores',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_message_id')
                    ->constrained(
                        'fifa_connect_event_messages'
                    )
                    ->cascadeOnDelete();
                $table->unsignedTinyInteger('order_number');
                $table->text('value');
                $table->timestamps();

                $table->unique([
                    'event_message_id',
                    'order_number',
                ], 'fc_event_message_score_unique');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'fifa_connect_event_message_scores'
        );
        Schema::dropIfExists(
            'fifa_connect_event_messages'
        );
        Schema::dropIfExists(
            'fifa_connect_case_match_events'
        );
        Schema::dropIfExists(
            'fifa_connect_match_facility_contexts'
        );
        Schema::dropIfExists(
            'fifa_connect_match_competition_contexts'
        );

        Schema::table(
            'fifa_connect_matches',
            function (Blueprint $table) {
                $table->dropColumn(
                    'facility_international_short_name'
                );
            }
        );

        Schema::table(
            'fifa_connect_certifications',
            function (Blueprint $table) {
                $table->dropColumn('description');
            }
        );
    }
};
