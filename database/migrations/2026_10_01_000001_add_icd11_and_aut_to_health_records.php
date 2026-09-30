<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::table('health_records',function(Blueprint $t){
            $t->json('icd11_diagnoses')->nullable();
        });
        Schema::table('tue_requests',function(Blueprint $t){
            // Le joueur principal est canonique ; les liens athlètes historiques restent.
            $t->unsignedBigInteger('athlete_id')->nullable()->change();
            $t->string('medication')->nullable()->change();
            $t->text('reason')->nullable()->change();
            $t->foreignId('player_id')->nullable()->constrained('players')->nullOnDelete();
            $t->foreignId('health_record_id')->nullable()->constrained('health_records')->nullOnDelete();
            $t->json('aut_form_data')->nullable();
        });
        Schema::create('medical_aut_documents',function(Blueprint $t){
            $t->id();$t->foreignId('tue_request_id')->constrained('tue_requests')->cascadeOnDelete();
            $t->string('original_name');$t->string('mime_type');$t->string('sha256',64);
            $t->longText('content');$t->timestamps();
        });
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
        // Ne pas rétablir NOT NULL : des demandes sans ancien athlète peuvent exister.
    }
};
