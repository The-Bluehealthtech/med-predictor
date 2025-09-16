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
        Schema::table('pcmas', function (Blueprint $table) {
            $table->boolean('is_signed')->default(false)->after('ultrasound_file');
            $table->timestamp('signed_at')->nullable()->after('is_signed');
            $table->string('signed_by')->nullable()->after('signed_at');
            $table->string('license_number')->nullable()->after('signed_by');
            $table->text('signature_image')->nullable()->after('license_number');
            $table->json('signature_data')->nullable()->after('signature_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcmas', function (Blueprint $table) {
            $table->dropColumn([
                'is_signed',
                'signed_at',
                'signed_by',
                'license_number',
                'signature_image',
                'signature_data'
            ]);
        });
    }
};
