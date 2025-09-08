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
        Schema::create('transfer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained('transfers')->onDelete('cascade');
            $table->foreignId('payer_id')->nullable()->constrained('clubs')->onDelete('set null');
            $table->foreignId('payee_id')->nullable()->constrained('clubs')->onDelete('set null');
            
            // Informations du paiement
            $table->enum('payment_type', [
                'transfer_fee', 'training_compensation', 'solidarity_contribution', 'other'
            ])->default('transfer_fee');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('EUR');
            $table->enum('payment_method', [
                'bank_transfer', 'check', 'cash', 'other'
            ])->default('bank_transfer');
            
            // Statut du paiement
            $table->enum('payment_status', [
                'pending', 'processing', 'completed', 'failed', 'cancelled'
            ])->default('pending');
            
            // Dates
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->timestamp('processed_at')->nullable();
            
            // Informations de transaction
            $table->string('transaction_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('payment_notes')->nullable();
            
            // Informations FIFA
            $table->string('fifa_payment_id')->nullable();
            $table->json('fifa_payment_data')->nullable();
            
            // Métadonnées
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Index
            $table->index(['transfer_id', 'payment_type']);
            $table->index(['payment_status']);
            $table->index(['due_date']);
            $table->index(['transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_payments');
    }
};
