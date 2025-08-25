<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_connected_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('joueurs')->onDelete('cascade');
            $table->string('device_name')->comment('Nom de l\'appareil (ex: Apple Watch Series 9)');
            $table->string('device_type')->comment('Type d\'appareil (smartwatch, smartphone, tracker, etc.)');
            $table->string('device_model')->comment('Modèle spécifique');
            $table->string('manufacturer')->comment('Fabricant (Apple, Samsung, Garmin, etc.)');
            $table->string('serial_number')->unique()->comment('Numéro de série unique');
            $table->string('firmware_version')->nullable()->comment('Version du firmware');
            $table->string('app_version')->nullable()->comment('Version de l\'application');
            
            // === ÉTAT DE CONNEXION ===
            $table->boolean('is_connected')->default(false)->comment('Appareil connecté ou non');
            $table->timestamp('last_connected_at')->nullable()->comment('Dernière connexion');
            $table->timestamp('last_sync_at')->nullable()->comment('Dernière synchronisation');
            $table->string('connection_method')->nullable()->comment('Méthode de connexion (Bluetooth, WiFi, etc.)');
            $table->string('connection_status')->default('disconnected')->comment('Statut de la connexion');
            
            // === INFORMATIONS TECHNIQUES ===
            $table->integer('battery_level')->nullable()->comment('Niveau de batterie en %');
            $table->enum('battery_status', ['charging', 'discharging', 'full', 'low', 'critical'])->nullable();
            $table->timestamp('battery_last_updated')->nullable()->comment('Dernière mise à jour batterie');
            $table->string('storage_available')->nullable()->comment('Stockage disponible');
            $table->string('storage_total')->nullable()->comment('Stockage total');
            $table->string('memory_available')->nullable()->comment('Mémoire disponible');
            
            // === CAPACITÉS DE L'APPAREIL ===
            $table->json('sensors_available')->nullable()->comment('Capteurs disponibles (GPS, HR, accéléromètre, etc.)');
            $table->json('features_enabled')->nullable()->comment('Fonctionnalités activées');
            $table->boolean('gps_enabled')->default(false)->comment('GPS activé');
            $table->boolean('heart_rate_enabled')->default(false)->comment('Fréquence cardiaque activée');
            $table->boolean('sleep_tracking_enabled')->default(false)->comment('Suivi du sommeil activé');
            $table->boolean('activity_tracking_enabled')->default(false)->comment('Suivi d\'activité activé');
            
            // === CONFIGURATION ===
            $table->json('device_settings')->nullable()->comment('Paramètres de l\'appareil');
            $table->json('notification_preferences')->nullable()->comment('Préférences de notifications');
            $table->string('timezone')->default('UTC')->comment('Fuseau horaire de l\'appareil');
            $table->string('language')->default('en')->comment('Langue de l\'appareil');
            
            // === SÉCURITÉ ===
            $table->boolean('is_authenticated')->default(false)->comment('Appareil authentifié');
            $table->string('authentication_method')->nullable()->comment('Méthode d\'authentification');
            $table->timestamp('last_authentication_at')->nullable()->comment('Dernière authentification');
            $table->boolean('is_encrypted')->default(false)->comment('Données chiffrées');
            
            // === MÉTADONNÉES ===
            $table->enum('status', ['active', 'inactive', 'maintenance', 'retired', 'lost'])->default('active');
            $table->date('purchase_date')->nullable()->comment('Date d\'achat');
            $table->date('warranty_expiry')->nullable()->comment('Expiration de la garantie');
            $table->text('notes')->nullable()->comment('Notes sur l\'appareil');
            $table->json('metadata')->nullable()->comment('Métadonnées supplémentaires');
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['player_id', 'device_type']);
            $table->index(['is_connected', 'last_sync_at']);
            $table->index(['device_type', 'status']);
            $table->index(['manufacturer', 'device_model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_connected_devices');
    }
};








