<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};
return new class extends Migration {
    public function up(): void {
        if(DB::connection()->getDriverName()==='pgsql') {
            DB::statement("SET LOCAL lock_timeout = '10s'");
            DB::statement("SET LOCAL statement_timeout = '90s'");
        }
        if(Schema::hasTable('health_record_documents')) return;
        Schema::create('health_record_documents',function(Blueprint $t){
            $t->id(); $t->foreignId('health_record_id')->constrained('health_records')->cascadeOnDelete();
            // Le dossier porte déjà le lien joueur : éviter un verrou redondant sur players.
            $t->unsignedBigInteger('player_id')->index();
            $t->string('section'); $t->uuid('entry_id')->index(); $t->date('exam_date');
            $t->string('original_name'); $t->string('mime_type'); $t->string('sha256',64);
            $t->longText('content'); $t->unsignedBigInteger('recorded_by'); $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('health_record_documents'); }
};
