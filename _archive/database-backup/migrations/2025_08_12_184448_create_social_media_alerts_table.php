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
        Schema::create('social_media_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->timestamp('alert_timestamp');
            $table->string('views'); // Format: "2.5K", "15.2K"
            $table->integer('engagement');
            $table->enum('sentiment', ['positive', 'negative', 'neutral']);
            $table->enum('platform', ['instagram', 'twitter', 'facebook', 'tiktok', 'youtube']);
            $table->boolean('needs_response')->default(false);
            $table->string('hashtag')->nullable();
            $table->string('user_mention')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_alerts');
    }
};
