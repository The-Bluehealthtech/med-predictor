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
        Schema::table('competitions', function (Blueprint $table) {
            $table->boolean('fixtures_validated')->default(false)->after('require_federation_license');
            $table->timestamp('fixtures_validated_at')->nullable()->after('fixtures_validated');
            $table->unsignedBigInteger('validated_by')->nullable()->after('fixtures_validated_at');
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['fixtures_validated', 'fixtures_validated_at', 'validated_by']);
        });
    }
};
