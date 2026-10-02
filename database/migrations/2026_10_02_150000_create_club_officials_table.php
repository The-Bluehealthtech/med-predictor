<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Dirigeants et staff des clubs, structurés comme les données FIFA Connect (Person, Registration,
// Certification). L'identifiant FIFA reste vide tant qu'il n'a pas été attribué par FIFA Connect.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('club_officials')) {
            return;
        }
        Schema::create('club_officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            // Person
            $table->string('person_fifa_id')->nullable()->index();
            $table->string('international_first_name');
            $table->string('international_last_name');
            $table->string('local_first_name')->nullable();
            $table->string('local_last_name')->nullable();
            $table->string('popular_name')->nullable();
            $table->string('gender', 10);
            $table->date('date_of_birth');
            $table->string('nationality', 2);
            $table->string('second_nationality', 2)->nullable();
            $table->string('country_of_birth', 2)->nullable();
            $table->string('place_of_birth')->nullable();
            // Registration
            $table->string('registration_type', 30);
            $table->string('team_official_role', 40)->nullable();
            $table->string('organisation_official_role', 40)->nullable();
            $table->string('role_description')->nullable();
            $table->boolean('is_head_coach')->default(false);
            $table->string('status', 20)->default('active');
            $table->string('discipline', 20)->default('Football');
            $table->date('registration_valid_from');
            $table->date('registration_valid_to')->nullable();
            // Certification (diplôme d'entraîneur ou autre qualification)
            $table->string('certification_type', 40)->nullable();
            $table->string('certification_name')->nullable();
            $table->string('certification_number')->nullable();
            $table->date('certification_valid_from')->nullable();
            $table->date('certification_valid_to')->nullable();
            // Contact (données locales, non transmises à FIFA Connect)
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['club_id', 'registration_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_officials');
    }
};
