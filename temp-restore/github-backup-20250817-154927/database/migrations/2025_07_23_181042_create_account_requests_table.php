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
        Schema::create('account_requests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('organization_name');
            $table->string('organization_type');
            $table->string('football_type');
            $table->string('country');
            $table->string('city')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'contacted', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('association_id')->nullable()->constrained('associations')->onDelete('set null');
            $table->string('generated_username')->nullable();
            $table->string('generated_password')->nullable();
            $table->timestamp('user_created_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['email']);
            $table->index(['organization_type']);
            $table->index(['football_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_requests');
    }
};
