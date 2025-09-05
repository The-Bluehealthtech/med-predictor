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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['system', 'association', 'club', 'federation', 'organization'])->default('organization');
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('active');
            $table->string('fifa_connect_id')->nullable()->unique();
            $table->string('fifa_code', 3)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('language', 5)->default('en');
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('parent_tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index(['country', 'status']);
            $table->index('parent_tenant_id');
            $table->index('fifa_connect_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};



