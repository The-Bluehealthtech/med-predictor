<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\User;
use App\Models\Club;
use App\Models\Association;
use App\Models\FifaConnectId;
use App\Models\PlayerLicense;
use App\Models\HealthRecord;
use App\Models\MedicalPrediction;
use App\Models\PlayerDocument;
use App\Models\PlayerFitnessLog;
use Carbon\Carbon;

class PlayerDashboardSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test association and club
        $association = Association::firstOrCreate([
            'name' => 'Test Football Association',
            'fifa_association_id' => 'TFA-001'
        ], [
            'country' => 'Test Country',
            'association_logo_url' => null,
            'contact_email' => 'test@tfa.com',
            'contact_phone' => '+1234567890',
            'website' => 'https://tfa.test',
            'status' => 'active'
        ]);

        $club = Club::firstOrCreate([
            'name' => 'Test FC',
            'fifa_club_id' => 'TFC-001'
        ], [
            'association_id' => $association->id,
            'country' => 'Test Country',
            'city' => 'Test City',
            'club_logo_url' => null,
            'contact_email' => 'info@testfc.com',
            'contact_phone' => '+1234567890',
            'website' => 'https://testfc.test',
            'status' => 'active'
        ]);

        // Create FIFA Connect ID for player
        $fifaConnectId = FifaConnectId::create([
            'fifa_id' => 'PLAYER-001',
            'entity_type' => 'player',
            'status' => 'active',
            'metadata' => [
                'created_by' => 1
            ]
        ]);

        // Create test player with dashboard data
        $player = Player::create([
            'fifa_connect_id' => $fifaConnectId->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1995-06-15',
            'nationality' => 'Test Country',
            'position' => 'Forward',
            'club_id' => $club->id,
            'association_id' => $association->id,
            'height' => 180,
            'weight' => 75,
            'email' => 'john.doe@testfc.com',
            'phone' => '+1234567890',
            'jersey_number' => 10,
            'overall_rating' => 85,
            'potential_rating' => 88,
            'value_eur' => 50000000,
            'wage_eur' => 50000,
            'contract_valid_until' => '2026-06-30',
            'player_picture' => null,
            // Player Dashboard fields
            'ghs_physical_score' => 85.5,
            'ghs_mental_score' => 78.2,
            'ghs_civic_score' => 92.1,
            'ghs_sleep_score' => 76.8,
            'ghs_overall_score' => 83.2,
            'ghs_color_code' => 'blue',
            'ghs_ai_suggestions' => [
                'Consider increasing sleep quality by 10% for better recovery',
                'Excellent civic engagement score - keep up the community work',
                'Mental health score could improve with stress management techniques'
            ],
            'ghs_last_updated' => now(),
            'contribution_score' => 87.5,
            'data_value_estimate' => 1250.00,
            'matches_contributed' => 45,
            'training_sessions_logged' => 120,
            'health_records_contributed' => 18,
            'injury_risk_score' => 0.15,
            'injury_risk_level' => 'low',
            'injury_risk_reason' => 'Good fitness levels, regular medical checkups, no recent injuries',
            'weekly_health_tips' => [
                'Stay hydrated during training sessions',
                'Focus on proper warm-up routines',
                'Consider yoga for flexibility improvement'
            ],
            'injury_risk_last_assessed' => now(),
            'match_availability' => true,
            'last_availability_update' => now(),
            'last_data_export' => now()->subDays(30),
            'data_export_count' => 3
        ]);

        // Create user account for the player
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@testfc.com',
            'password' => bcrypt('password123'),
            'role' => 'player',
            'club_id' => $club->id,
            'association_id' => $association->id,
            'fifa_connect_id' => $fifaConnectId->id,
            'permissions' => ['player_dashboard_access'],
            'status' => 'active',
            'phone' => '+1234567890',
            'timezone' => 'UTC',
            'language' => 'en'
        ]);

        // Create player license
        PlayerLicense::create([
            'player_id' => $player->id,
            'club_id' => $club->id,
            'fifa_connect_id' => $fifaConnectId->id,
            'license_number' => 'LIC-001',
            'license_type' => 'professional',
            'status' => 'active',
            'issue_date' => now()->subYear(),
            'expiry_date' => now()->addYear(),
            'renewal_date' => now()->addMonths(11),
            'issuing_authority' => 'Test FA',
            'license_category' => 'A',
            'registration_number' => 'REG-001',
            'transfer_status' => 'registered',
            'contract_type' => 'permanent',
            'contract_start_date' => now()->subYear(),
            'contract_end_date' => now()->addYears(2),
            'wage_agreement' => 50000.00,
            'bonus_structure' => 'Performance-based',
            'release_clause' => 75000000.00,
            'medical_clearance' => true,
            'fitness_certificate' => true,
            'disciplinary_record' => 'Clean',
            'international_clearance' => true,
            'work_permit' => true,
            'visa_status' => 'Valid',
            'documentation_status' => 'Complete',
            'approval_status' => 'approved',
            'approved_by' => 'Test FA',
            'approved_at' => now()->subYear(),
            'notes' => 'Professional player license'
        ]);

        // Create health records
        for ($i = 0; $i < 5; $i++) {
            HealthRecord::create([
                'user_id' => $user->id,
                'player_id' => $player->id,
                'blood_pressure_systolic' => 120 + rand(-10, 10),
                'blood_pressure_diastolic' => 80 + rand(-5, 5),
                'heart_rate' => 65 + rand(-10, 10),
                'temperature' => 36.5 + rand(-5, 5) / 10,
                'weight' => 75 + rand(-2, 2),
                'height' => 180,
                'bmi' => 23.1 + rand(-1, 1) / 10,
                'blood_type' => 'O+',
                'allergies' => ['None'],
                'medications' => ['Vitamin D'],
                'medical_history' => ['Minor ankle sprain 2022'],
                'symptoms' => [],
                'diagnosis' => 'Healthy',
                'treatment_plan' => 'Continue regular training',
                'risk_score' => 0.1 + (rand(0, 20) / 100),
                'prediction_confidence' => 0.85 + (rand(0, 15) / 100),
                'record_date' => now()->subDays($i * 30),
                'next_checkup_date' => now()->addDays(30),
                'status' => 'active'
            ]);
        }

        // Create medical predictions
        for ($i = 0; $i < 3; $i++) {
            MedicalPrediction::create([
                'player_id' => $player->id,
                'health_record_id' => HealthRecord::where('player_id', $player->id)->first()->id,
                'user_id' => $user->id,
                'prediction_type' => ['injury_risk', 'performance_prediction', 'health_condition'][$i],
                'prediction_date' => now()->subDays($i * 15),
                'risk_probability' => 0.1 + (rand(0, 30) / 100),
                'confidence_score' => 0.8 + (rand(0, 20) / 100),
                'predicted_outcome' => 'Positive',
                'recommended_actions' => 'Continue current training regimen',
                'notes' => 'Player shows good health indicators',
                'status' => 'active'
            ]);
        }

        // Create fitness logs
        for ($i = 0; $i < 10; $i++) {
            PlayerFitnessLog::create([
                'player_id' => $player->id,
                'log_date' => now()->subDays($i * 7),
                'fitness_score' => 80 + rand(-10, 10),
                'endurance_score' => 85 + rand(-10, 10),
                'strength_score' => 82 + rand(-10, 10),
                'flexibility_score' => 78 + rand(-10, 10),
                'speed_score' => 88 + rand(-10, 10),
                'overall_fitness' => 83 + rand(-10, 10),
                'notes' => 'Regular training session completed',
                'coach_notes' => 'Good performance, maintain current level',
                'next_session_plan' => 'Continue strength training',
                'status' => 'completed'
            ]);
        }

        // Create player documents
        $documentTypes = ['medical', 'fitness', 'contract', 'other'];
        for ($i = 0; $i < 4; $i++) {
            PlayerDocument::create([
                'player_id' => $player->id,
                'document_type' => $documentTypes[$i],
                'filename' => "document_{$i + 1}.pdf",
                'original_filename' => "Document {$i + 1}.pdf",
                'file_path' => "documents/player_{$player->id}/document_{$i + 1}.pdf",
                'file_size' => 1024 * 1024 * rand(1, 5), // 1-5 MB
                'mime_type' => 'application/pdf',
                'uploaded_at' => now()->subDays($i * 7),
                'expiry_date' => now()->addYears(1),
                'status' => 'active',
                'notes' => "Sample document {$i + 1}"
            ]);
        }

        $this->command->info('Player Dashboard test data created successfully!');
        $this->command->info("Player: {$player->first_name} {$player->last_name}");
        $this->command->info("Email: {$user->email}");
        $this->command->info("Password: password123");
        $this->command->info("FIFA ID: {$fifaConnectId->fifa_id}");
    }
} 