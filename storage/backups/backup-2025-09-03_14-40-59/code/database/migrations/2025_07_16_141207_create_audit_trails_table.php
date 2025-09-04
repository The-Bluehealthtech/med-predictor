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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // create, update, delete, login, logout, view, export, etc.
            $table->string('model_type')->nullable(); // User, Player, HealthRecord, etc.
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected record
            $table->string('table_name')->nullable(); // Database table name
            $table->string('event_type'); // user_action, system_event, security_event, data_access
            $table->string('severity')->default('info'); // info, warning, error, critical
            $table->text('description'); // Human readable description
            $table->json('old_values')->nullable(); // Previous values before change
            $table->json('new_values')->nullable(); // New values after change
            $table->json('metadata')->nullable(); // Additional context data
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->string('request_method')->nullable(); // GET, POST, PUT, DELETE
            $table->string('request_url')->nullable();
            $table->string('request_id')->nullable(); // Unique request identifier
            $table->timestamp('occurred_at');
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['user_id', 'occurred_at']);
            $table->index(['action', 'occurred_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['event_type', 'severity']);
            $table->index(['table_name', 'occurred_at']);
            $table->index(['ip_address', 'occurred_at']);
            $table->index(['session_id', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
