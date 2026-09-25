<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Setting key (e.g., 'app_name', 'max_file_size')
            $table->string('name'); // Human readable name
            $table->text('description')->nullable(); // Description of the setting
            $table->text('value')->nullable(); // Setting value
            $table->string('type')->default('string'); // string, integer, boolean, json, text
            $table->string('group')->default('general'); // Group category (general, security, email, etc.)
            $table->boolean('is_public')->default(false); // Can be accessed publicly
            $table->boolean('is_editable')->default(true); // Can be edited by admin
            $table->boolean('is_required')->default(false); // Required setting
            $table->json('options')->nullable(); // For select/radio options
            $table->string('validation_rules')->nullable(); // Laravel validation rules
            $table->string('default_value')->nullable(); // Default value
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['group', 'is_public']);
            $table->index(['key', 'is_editable']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
