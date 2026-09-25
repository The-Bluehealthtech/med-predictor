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
        Schema::table('health_records', function (Blueprint $table) {
            // Enhanced AUT (Autorisation d'Usage Thérapeutique) fields
            $table->string('aut_substance')->nullable()->comment('Substance médicamenteuse autorisée');
            $table->string('aut_dosage')->nullable()->comment('Posologie prescrite');
            $table->text('aut_diagnosis')->nullable()->comment('Diagnostic médical justifiant l\'AUT');
            $table->text('aut_justification')->nullable()->comment('Justification médicale de l\'usage thérapeutique');
            $table->date('aut_start_date')->nullable()->comment('Date de début de l\'AUT');
            $table->date('aut_end_date')->nullable()->comment('Date de fin de l\'AUT');
            $table->string('aut_application_form_path')->nullable()->comment('Chemin vers le formulaire de demande AUT');
            $table->string('aut_response_document_path')->nullable()->comment('Chemin vers le document de réponse officielle');
            $table->text('aut_notes')->nullable()->comment('Notes et observations sur l\'AUT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropColumn([
                'aut_substance',
                'aut_dosage', 
                'aut_diagnosis',
                'aut_justification',
                'aut_start_date',
                'aut_end_date',
                'aut_application_form_path',
                'aut_response_document_path',
                'aut_notes'
            ]);
        });
    }
};
