<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table transverse de traçabilité des lots (Livrable 1, §7).
     * batch_type distingue un import réel (Livrable 2) d'une génération de
     * démonstration (Livrable 3) ; toutes les tables de données de match
     * qui reçoivent is_demo référencent cette table via import_batch_id.
     */
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_type'); // 'import' | 'demo_generation'
            $table->string('source_label');
            $table->string('status')->default('running'); // running | completed | failed
            $table->string('seed')->nullable();
            $table->json('params')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
