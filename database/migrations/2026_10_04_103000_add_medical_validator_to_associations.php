<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { if (Schema::hasTable('associations') && !Schema::hasColumn('associations','medical_validator_user_id')) Schema::table('associations', function (Blueprint $table) { $table->foreignId('medical_validator_user_id')->nullable()->constrained('users')->nullOnDelete(); }); }
 public function down(): void { if (Schema::hasTable('associations') && Schema::hasColumn('associations','medical_validator_user_id')) Schema::table('associations', function (Blueprint $table) { $table->dropConstrainedForeignId('medical_validator_user_id'); }); }
};
