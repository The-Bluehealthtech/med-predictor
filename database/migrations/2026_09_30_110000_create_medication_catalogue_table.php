<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('medication_catalogue', function (Blueprint $table) {
            $table->string('product_id')->primary();
            $table->string('name');
            $table->text('search_text');
            $table->string('source_version');
            $table->json('payload');
        });
    }
    public function down(): void { Schema::dropIfExists('medication_catalogue'); }
};
