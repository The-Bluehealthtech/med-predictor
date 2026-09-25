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
        Schema::table('players', function (Blueprint $table) {
            $table->string('address')->nullable()->after('association_id');
            $table->string('contact_phone')->nullable()->after('address');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->string('legal_guardian')->nullable()->after('contact_email');
            $table->string('school_professional_status')->nullable()->after('legal_guardian');
            $table->string('parental_consent')->nullable()->after('school_professional_status');
            $table->string('license_type')->nullable()->after('parental_consent');
            $table->text('previous_clubs')->nullable()->after('license_type');
            $table->string('previous_license_number')->nullable()->after('previous_clubs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'contact_phone',
                'contact_email',
                'legal_guardian',
                'school_professional_status',
                'parental_consent',
                'license_type',
                'previous_clubs',
                'previous_license_number'
            ]);
        });
    }
};
