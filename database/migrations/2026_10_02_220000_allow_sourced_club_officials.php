<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('club_officials', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->change();
            $table->string('nationality', 2)->nullable()->change();
            $table->string('source', 40)->nullable()->index();
            $table->text('source_url')->nullable();
            $table->timestamp('retrieved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('club_officials', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn(['source', 'source_url', 'retrieved_at']);
        });
    }
};
