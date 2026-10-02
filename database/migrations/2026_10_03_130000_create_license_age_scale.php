<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Licences alignées sur l'enregistrement FIFA Connect (registration.xsd) :
 * type d'enregistrement (Player, TeamOfficial, OrganisationOfficial), discipline
 * (Football, Futsal, BeachSoccer), niveau du joueur (pro, amateur), nature
 * (Registration, Loan), rôle de l'officiel, genre de la personne (male, female),
 * saison. Une licence vaut au plus une saison.
 *
 * Barème de la fédération : par genre et discipline, catégories d'âge (paramètre
 * national, comme l'AgeCategory des compétitions FIFA Connect ; absent de
 * l'enregistrement lui-même) avec niveaux autorisés, tarifs, règle PCMA et pièces.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('license_scale_settings')) {
            Schema::create('license_scale_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('association_id')->unique();
                $table->string('currency', 3)->default('EUR');
                $table->unsignedTinyInteger('season_start_month')->default(7);
                $table->unsignedTinyInteger('season_start_day')->default(1);
                $table->unsignedTinyInteger('reference_month')->default(1);
                $table->unsignedTinyInteger('reference_day')->default(1);
                $table->json('official_fees')->nullable(); // { "TeamOfficial": 50, "OrganisationOfficial": 30 }
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('license_age_categories')) {
            Schema::create('license_age_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('association_id')->index();
                $table->string('gender', 10);      // GenderType : male | female
                $table->string('discipline', 20);  // DisciplineType : Football | Futsal | BeachSoccer
                $table->string('code', 20);
                $table->string('label', 60);
                $table->unsignedTinyInteger('max_age')->nullable(); // « moins de » ; vide = senior
                $table->json('allowed_levels');    // RegistrationLevelType : amateur | pro
                $table->json('fees')->nullable();  // { "amateur": 30, "pro": 300 }
                $table->string('pcma_rule', 20)->default('pro'); // none | pro | all
                $table->json('required_documents');
                $table->unsignedSmallInteger('position')->default(0);
                $table->timestamps();
                $table->unique(['association_id', 'gender', 'discipline', 'code'], 'license_age_categories_scope_code');
            });
        }

        Schema::table('player_licenses', function (Blueprint $table) {
            $add = fn (string $column, callable $definition) => Schema::hasColumn('player_licenses', $column) ? null : $definition($table);
            $add('club_official_id', fn ($t) => $t->foreignId('club_official_id')->nullable()->constrained('club_officials')->nullOnDelete());
            $add('registration_type', fn ($t) => $t->string('registration_type', 30)->nullable());
            $add('discipline', fn ($t) => $t->string('discipline', 20)->nullable());
            $add('level', fn ($t) => $t->string('level', 10)->nullable());
            $add('registration_nature', fn ($t) => $t->string('registration_nature', 20)->nullable());
            $add('team_official_role', fn ($t) => $t->string('team_official_role', 40)->nullable());
            $add('organisation_official_role', fn ($t) => $t->string('organisation_official_role', 40)->nullable());
            $add('gender', fn ($t) => $t->string('gender', 10)->nullable());
            $add('age_category', fn ($t) => $t->string('age_category', 20)->nullable());
            $add('fee_amount', fn ($t) => $t->decimal('fee_amount', 10, 2)->nullable());
            $add('fee_currency', fn ($t) => $t->string('fee_currency', 3)->nullable());
        });
        // Une licence d'officiel n'a pas de joueur.
        Schema::table('player_licenses', function (Blueprint $table) {
            $table->unsignedBigInteger('player_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('player_licenses', 'club_official_id')) {
            Schema::table('player_licenses', fn (Blueprint $table) => $table->dropConstrainedForeignId('club_official_id'));
        }
        // Un seul dropColumn par modification (contrainte SQLite).
        $columns = array_values(array_filter(['registration_type', 'discipline', 'level', 'registration_nature', 'team_official_role',
            'organisation_official_role', 'gender', 'age_category', 'fee_amount', 'fee_currency'], fn ($c) => Schema::hasColumn('player_licenses', $c)));
        if ($columns) {
            Schema::table('player_licenses', fn (Blueprint $table) => $table->dropColumn($columns));
        }
        Schema::dropIfExists('license_age_categories');
        Schema::dropIfExists('license_scale_settings');
    }
};
