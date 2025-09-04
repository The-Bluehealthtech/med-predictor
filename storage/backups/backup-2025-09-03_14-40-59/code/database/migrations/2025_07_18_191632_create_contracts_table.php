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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('transfer_id')->nullable()->constrained('transfers')->onDelete('set null');
            
            // Informations du contrat
            $table->enum('contract_type', ['permanent', 'loan', 'trial', 'amateur'])->default('permanent');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Informations financières
            $table->decimal('salary', 15, 2)->nullable();
            $table->decimal('bonus', 15, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->enum('payment_frequency', ['weekly', 'monthly', 'yearly'])->default('monthly');
            
            // Clauses et conditions
            $table->json('clauses')->nullable(); // Clauses spéciales
            $table->json('bonuses')->nullable(); // Bonus et primes
            $table->text('special_conditions')->nullable();
            
            // Informations FIFA
            $table->string('fifa_contract_id')->nullable()->unique();
            $table->json('fifa_contract_data')->nullable();
            
            // Métadonnées
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Index
            $table->index(['player_id', 'is_active']);
            $table->index(['club_id', 'is_active']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
