<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
return new class extends Migration {
    public function up(): void
    {
        if(DB::connection()->getDriverName()==='pgsql'){
            // Attendre un verrou indéfiniment empêcherait Render d'ouvrir le port.
            DB::statement("SET LOCAL lock_timeout = '10s'");
            DB::statement("SET LOCAL statement_timeout = '90s'");
        }
        if(!Schema::hasColumn('health_records','icd11_diagnoses')){
            Schema::table('health_records',fn(Blueprint $t)=>$t->json('icd11_diagnoses')->nullable());
        }
        if(DB::connection()->getDriverName()==='pgsql'){
            // Seule la nullabilité change : ne pas convertir les types historiques.
            DB::statement('ALTER TABLE tue_requests ALTER COLUMN athlete_id DROP NOT NULL, ALTER COLUMN medication DROP NOT NULL, ALTER COLUMN reason DROP NOT NULL');
        } else {
            Schema::table('tue_requests',function(Blueprint $t){
                $t->unsignedBigInteger('athlete_id')->nullable()->change();
                $t->string('medication')->nullable()->change();
                $t->text('reason')->nullable()->change();
            });
        }
        if(!Schema::hasColumn('tue_requests','player_id')){
            Schema::table('tue_requests',fn(Blueprint $t)=>$t->foreignId('player_id')->nullable()->constrained('players')->nullOnDelete());
        }
        if(!Schema::hasColumn('tue_requests','health_record_id')){
            Schema::table('tue_requests',fn(Blueprint $t)=>$t->foreignId('health_record_id')->nullable()->constrained('health_records')->nullOnDelete());
        }
        if(!Schema::hasColumn('tue_requests','aut_form_data')){
            Schema::table('tue_requests',fn(Blueprint $t)=>$t->json('aut_form_data')->nullable());
        }
        if(!Schema::hasTable('medical_aut_documents')){
            Schema::create('medical_aut_documents',function(Blueprint $t){
                $t->id();$t->foreignId('tue_request_id')->constrained('tue_requests')->cascadeOnDelete();
                $t->string('original_name');$t->string('mime_type');$t->string('sha256',64);
                $t->longText('content');$t->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('medical_aut_documents');
        Schema::table('tue_requests',function(Blueprint $t){
            $t->dropConstrainedForeignId('player_id');
            $t->dropConstrainedForeignId('health_record_id');
            $t->dropColumn('aut_form_data');
        });
        Schema::table('health_records',fn(Blueprint $t)=>$t->dropColumn('icd11_diagnoses'));
        // Des dossiers sans ancien athlète peuvent exister : conserver leur nullabilité.
    }
};
