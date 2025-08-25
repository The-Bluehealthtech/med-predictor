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
            // Informations de base de l'agent
            $table->string('agent_name')->nullable()->after('nationality');
            $table->string('agent_phone')->nullable()->after('agent_name');
            $table->string('agent_email')->nullable()->after('agent_phone');
            $table->string('agent_agency')->nullable()->after('agent_email');
            $table->string('agent_country')->nullable()->after('agent_agency');
            
            // Détails du contrat avec l'agent
            $table->string('agent_contract_type')->default('Standard')->after('agent_country');
            $table->date('agent_signed_date')->nullable()->after('agent_contract_type');
            $table->decimal('agent_commission', 5, 2)->default(5.00)->after('agent_signed_date'); // 5.00 = 5%
            
            // Contact d'urgence de l'agent
            $table->string('agent_emergency_phone')->nullable()->after('agent_commission');
            $table->string('agent_emergency_relation')->nullable()->after('agent_emergency_phone');
            
            // Informations supplémentaires
            $table->text('agent_notes')->nullable()->after('agent_emergency_relation');
            $table->string('agent_fifa_id')->nullable()->after('agent_notes');
            $table->string('agent_license_number')->nullable()->after('agent_fifa_id');
            $table->enum('agent_status', ['active', 'inactive', 'suspended'])->default('active')->after('agent_license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'agent_name',
                'agent_phone', 
                'agent_email',
                'agent_agency',
                'agent_country',
                'agent_contract_type',
                'agent_signed_date',
                'agent_commission',
                'agent_emergency_phone',
                'agent_emergency_relation',
                'agent_notes',
                'agent_fifa_id',
                'agent_license_number',
                'agent_status'
            ]);
        });
    }
};
