<?php

use App\Services\FifaConnect\IsoCountries;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Champs exigés par le message FIFA Connect PersonLocal (Data Standard 3.3) :
 * pays et lieu de naissance du joueur, langue de ses noms locaux (ISO 639-2) ;
 * code pays ISO du club (LocalCountry) ; langue des noms par défaut de la
 * fédération. Données de démonstration complétées : code pays des clubs déduit
 * du pays de leur fédération, langue « fra » pour la fédération tunisienne (noms
 * des joueurs de démonstration en français), nationalité « Ivory Coast »
 * remplacée par le nom ISO « Côte d'Ivoire ».
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            if (!Schema::hasColumn('players', 'country_of_birth')) {
                $table->string('country_of_birth', 2)->nullable();
            }
            if (!Schema::hasColumn('players', 'place_of_birth')) {
                $table->string('place_of_birth', 100)->nullable();
            }
            if (!Schema::hasColumn('players', 'local_language')) {
                $table->string('local_language', 3)->nullable();
            }
        });
        Schema::table('clubs', function (Blueprint $table) {
            if (!Schema::hasColumn('clubs', 'country_code')) {
                $table->string('country_code', 2)->nullable();
            }
        });
        if (Schema::hasTable('license_scale_settings') && !Schema::hasColumn('license_scale_settings', 'local_language')) {
            Schema::table('license_scale_settings', fn (Blueprint $table) => $table->string('local_language', 3)->nullable());
        }

        $iso = new IsoCountries;
        foreach (DB::table('associations')->get(['id', 'country']) as $association) {
            if ($code = $iso->code($association->country)) {
                DB::table('clubs')->where('association_id', $association->id)->whereNull('country_code')->update(['country_code' => $code]);
            }
        }
        $tunisia = DB::table('associations')->get(['id', 'country'])->first(fn ($a) => $iso->code($a->country) === 'TN');
        if ($tunisia && Schema::hasTable('license_scale_settings')) {
            $existing = DB::table('license_scale_settings')->where('association_id', $tunisia->id)->first();
            if ($existing) {
                DB::table('license_scale_settings')->where('id', $existing->id)->whereNull('local_language')->update(['local_language' => 'fra']);
            } else {
                DB::table('license_scale_settings')->insert(['association_id' => $tunisia->id, 'currency' => 'EUR', 'season_start_month' => 7, 'season_start_day' => 1,
                    'reference_month' => 1, 'reference_day' => 1, 'local_language' => 'fra', 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        DB::table('players')->where('nationality', 'Ivory Coast')->update(['nationality' => "Côte d'Ivoire"]);
    }

    public function down(): void
    {
        foreach (['players' => ['country_of_birth', 'place_of_birth', 'local_language'], 'clubs' => ['country_code'], 'license_scale_settings' => ['local_language']] as $table => $columns) {
            $present = array_values(array_filter($columns, fn ($c) => Schema::hasTable($table) && Schema::hasColumn($table, $c)));
            if ($present) {
                Schema::table($table, fn (Blueprint $t) => $t->dropColumn($present));
            }
        }
    }
};
