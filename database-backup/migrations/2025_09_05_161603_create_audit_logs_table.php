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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // created, updated, deleted, accessed, etc.
            $table->string('model_type'); // App\Models\User, App\Models\Club, etc.
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected model
            $table->string('model_name')->nullable(); // Human readable name
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values
            $table->json('changes')->nullable(); // What changed
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('description')->nullable(); // Human readable description
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->nullable(); // Cached user name
            $table->string('user_email')->nullable(); // Cached user email
            $table->string('tenant_id')->nullable(); // For multi-tenancy
            $table->string('module')->nullable(); // Which module triggered this
            $table->string('severity')->default('info'); // info, warning, error, critical
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['event_type', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['severity', 'created_at']);
            $table->index(['tenant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
