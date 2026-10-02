<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('external_entity_links')) {
            return;
        }

        Schema::create('external_entity_links', function (Blueprint $table) {
            $table->id();
            $table->string('source', 50);
            $table->string('entity_type', 30);
            $table->string('external_id', 191);
            $table->unsignedBigInteger('local_id');
            $table->string('season', 30)->nullable();
            $table->text('source_url')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['source', 'entity_type', 'external_id'],
                'external_entity_links_source_entity_external_unique'
            );
            $table->index(['entity_type', 'local_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_entity_links');
    }
};
