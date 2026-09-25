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
        Schema::create('performance_dashboards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('dashboard_type', ['federation', 'club', 'player', 'coach', 'medical', 'referee']);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('cascade');
            $table->foreignId('association_id')->nullable()->constrained('associations')->onDelete('cascade');
            $table->foreignId('federation_id')->nullable()->constrained('federations')->onDelete('cascade');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_public')->default(false);
            $table->json('layout_config')->nullable();
            $table->json('widgets_config')->nullable();
            $table->json('filters_config')->nullable();
            $table->integer('refresh_interval')->nullable(); // in seconds
            $table->timestamp('last_refresh')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['user_id', 'dashboard_type']);
            $table->index(['club_id', 'dashboard_type']);
            $table->index(['association_id', 'dashboard_type']);
            $table->index(['federation_id', 'dashboard_type']);
            $table->index(['is_default', 'dashboard_type']);
            $table->index(['is_public', 'dashboard_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_dashboards');
    }
};
