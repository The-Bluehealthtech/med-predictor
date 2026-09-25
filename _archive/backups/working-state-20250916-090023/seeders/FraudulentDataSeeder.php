<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Association;
use App\Models\Club;
use App\Models\Player;
use App\Models\PCMA;
use App\Models\HealthRecord;
use App\Models\License;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FraudulentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚨 Starting fraudulent data seeding for AI detection testing...');

        // Get existing association and clubs
        $association = Association::first();
        $club = Club::first();

        if (!$association || !$club) {
            $this->command->error('❌ No association or club found. Please run TestDataSeeder first.');
            return;
        }

        // Create fraudulent players with different types of fraud
        $this->createFraudulentPlayers($association, $club);
        
        // Create fraudulent PCMA records
        $this->createFraudulentPCMA($association, $club);
        
        // Create fraudulent health records
        $this->createFraudulentHealthRecords($association, $club);
        
        // Create fraudulent licenses
        $this->createFraudulentLicenses($association, $club);

        $this->command->info('✅ Fraudulent data seeding completed!');
    }

    /**
     * Create fraudulent players with different fraud patterns
     */
    private function createFraudulentPlayers($association, $club)
    {
        $fraudulentPlayers = [
            // 1. Age Fraud - Player claiming to be younger than actual age
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Ben Ali',
                'date_of_birth' => '2005-03-15', // Claims to be 19
                'actual_age' => 25, // Actually 25 years old
                'nationality' => 'Tunisian',
                'position' => 'Forward',
                'height' => 175,
                'weight' => 70,
                'fifa_connect_id' => 'FRAUD_AGE_001',
                'club_id' => $club->id,
                'association_id' => $association->id,
                'fraud_type' => 'age_fraud',
                'fraud_description' => 'Player submitted fake birth certificate showing 2005 instead of 1999',
                'profile_picture_url' => 'https://cdn.fit-platform.com/images/players/fraud_age_001.jpg',
                'status' => 'pending_verification'
            ],
            
            // 2. ID Fraud - Player using someone else's identity
            [
                'first_name' => 'Mohammed',
                'last_name' => 'El Amri',
                'date_of_birth' => '2000-07-22',
                'nationality' => 'Moroccan',
                'position' => 'Midfielder',
                'height' => 180,
                'weight' => 75,
                'fifa_connect_id' => 'FRAUD_ID_001',
                'club_id' => $club->id,
                'association_id' => $association->id,
                'fraud_type' => 'identity_fraud',
                'fraud_description' => 'Player using brother\'s ID documents',
                'profile_picture_url' => 'https://cdn.fit-platform.com/images/players/fraud_id_001.jpg',
                'status' => 'suspicious'
            ],
            
            // 3. Picture Fraud - Different pictures from last year
            [
                'first_name' => 'Karim',
                'last_name' => 'Benzema',
                'date_of_birth' => '1987-12-19',
                'nationality' => 'French',
                'position' => 'Forward',
                'height' => 185,
                'weight' => 81,
                'fifa_connect_id' => 'FRAUD_PIC_001',
                'club_id' => $club->id,
                'association_id' => $association->id,
                'fraud_type' => 'picture_fraud',
                'fraud_description' => 'Player submitted old photo from 2022 instead of current photo',
                'profile_picture_url' => 'https://cdn.fit-platform.com/images/players/benzema_2022.jpg', // Old photo
                'current_picture_url' => 'https://cdn.fit-platform.com/images/players/benzema_2024.jpg', // Current photo
                'status' => 'under_investigation'
            ],
            
            // 4. Mismatching Information - Inconsistent data
            [
                'first_name' => 'Salah',
                'last_name' => 'Mahmoud',
                'date_of_birth' => '1992-06-15',
                'nationality' => 'Egyptian',
                'position' => 'Forward',
                'height' => 175,
                'weight' => 71,
                'fifa_connect_id' => 'FRAUD_MISMATCH_001',
                'club_id' => $club->id,
                'association_id' => $association->id,
                'fraud_type' => 'mismatching_info',
                'fraud_description' => 'Height changed from 175cm to 180cm, weight from 71kg to 68kg',
                'profile_picture_url' => 'https://cdn.fit-platform.com/images/players/fraud_mismatch_001.jpg',
                'status' => 'flagged'
            ],
            
            // 5. Multiple Identity Fraud
            [
                'first_name' => 'Ali',
                'last_name' => 'Hassan',
                'date_of_birth' => '1998-04-10',
                'nationality' => 'Algerian',
                'position' => 'Defender',
                'height' => 182,
                'weight' => 78,
                'fifa_connect_id' => 'FRAUD_MULTI_001',
                'club_id' => $club->id,
                'association_id' => $association->id,
                'fraud_type' => 'multiple_identity',
                'fraud_description' => 'Player registered under 3 different names in different clubs',
                'profile_picture_url' => 'https://cdn.fit-platform.com/images/players/fraud_multi_001.jpg',
                'status' => 'rejected'
            ]
        ];

        foreach ($fraudulentPlayers as $playerData) {
            $player = Player::create($playerData);
            $this->command->info("🚨 Created fraudulent player: {$player->first_name} {$player->last_name} - {$playerData['fraud_type']}");
        }
    }

    /**
     * Create fraudulent PCMA records with age and ID fraud
     */
    private function createFraudulentPCMA($association, $club)
    {
        $fraudulentPCMA = [
            // 1. Age Fraud in PCMA - Player too old for youth category
            [
                'player_id' => Player::where('fifa_connect_id', 'FRAUD_AGE_001')->first()->id ?? 1,
                'assessment_date' => Carbon::now()->subDays(30),
                'assessor_name' => 'Dr. Fraud Detector',
                'age_at_assessment' => 19, // Claimed age
                'actual_age' => 25, // Real age
                'height_cm' => 175,
                'weight_kg' => 70,
                'bmi' => 22.9,
                'blood_pressure' => '120/80',
                'heart_rate' => 72,
                'vision_test' => '20/20',
                'hearing_test' => 'Normal',
                'mri_results' => 'Normal - Age inconsistent with MRI findings',
                'ecg_results' => 'Normal - Age inconsistent with ECG findings',
                'fitness_test_score' => 85,
                'medical_clearance' => 'Conditional',
                'fraud_type' => 'pCMA_age_fraud',
                'fraud_description' => 'MRI and ECG results indicate player is older than claimed age',
                'status' => 'flagged_for_review',
                'fraud_indicators' => json_encode([
                    'age_discrepancy' => '6 years difference',
                    'mri_inconsistency' => 'Bone density suggests older age',
                    'ecg_inconsistency' => 'Heart patterns suggest older age'
                ])
            ],
            
            // 2. ID Fraud in PCMA - Wrong person's medical data
            [
                'player_id' => Player::where('fifa_connect_id', 'FRAUD_ID_001')->first()->id ?? 2,
                'assessment_date' => Carbon::now()->subDays(15),
                'assessor_name' => 'Dr. Identity Checker',
                'age_at_assessment' => 24,
                'actual_age' => 24,
                'height_cm' => 180,
                'weight_kg' => 75,
                'bmi' => 23.1,
                'blood_pressure' => '118/78',
                'heart_rate' => 68,
                'vision_test' => '20/20',
                'hearing_test' => 'Normal',
                'mri_results' => 'Normal - But photo doesn\'t match patient',
                'ecg_results' => 'Normal - But photo doesn\'t match patient',
                'fitness_test_score' => 88,
                'medical_clearance' => 'Conditional',
                'fraud_type' => 'pCMA_identity_fraud',
                'fraud_description' => 'Medical records show different person than photo submitted',
                'status' => 'suspicious',
                'fraud_indicators' => json_encode([
                    'photo_mismatch' => 'Person in photo different from medical records',
                    'name_discrepancy' => 'Medical records show different name',
                    'id_verification_failed' => 'ID documents don\'t match person assessed'
                ])
            ],
            
            // 3. Multiple PCMA Fraud - Same person, different identities
            [
                'player_id' => Player::where('fifa_connect_id', 'FRAUD_MULTI_001')->first()->id ?? 3,
                'assessment_date' => Carbon::now()->subDays(7),
                'assessor_name' => 'Dr. Multi Identity',
                'age_at_assessment' => 26,
                'actual_age' => 26,
                'height_cm' => 182,
                'weight_kg' => 78,
                'bmi' => 23.5,
                'blood_pressure' => '122/82',
                'heart_rate' => 70,
                'vision_test' => '20/20',
                'hearing_test' => 'Normal',
                'mri_results' => 'Normal - Same person as previous assessment under different name',
                'ecg_results' => 'Normal - Same person as previous assessment under different name',
                'fitness_test_score' => 90,
                'medical_clearance' => 'Conditional',
                'fraud_type' => 'pCMA_multiple_identity',
                'fraud_description' => 'Same person assessed under 3 different names',
                'status' => 'rejected',
                'fraud_indicators' => json_encode([
                    'multiple_identities' => 'Same person, 3 different names',
                    'photo_similarity' => '99% match with previous assessments',
                    'medical_pattern_match' => 'Identical medical patterns across assessments'
                ])
            ]
        ];

        foreach ($fraudulentPCMA as $pCMAData) {
            $pCMA = PCMA::create($pCMAData);
            $this->command->info("🚨 Created fraudulent PCMA: {$pCMAData['fraud_type']} - {$pCMAData['fraud_description']}");
        }
    }

    /**
     * Create fraudulent health records
     */
    private function createFraudulentHealthRecords($association, $club)
    {
        $fraudulentHealthRecords = [
            // 1. Inconsistent Medical History
            [
                'player_id' => Player::where('fifa_connect_id', 'FRAUD_MISMATCH_001')->first()->id ?? 4,
                'record_date' => Carbon::now()->subDays(10),
                'record_type' => 'medical_checkup',
                'diagnosis' => 'Healthy - But height/weight inconsistent with previous records',
                'treatment' => 'None required',
                'notes' => 'Height changed from 175cm to 180cm, weight from 71kg to 68kg in 3 months',
                'fraud_type' => 'inconsistent_medical_history',
                'fraud_description' => 'Physical measurements changed dramatically in short period',
                'status' => 'flagged',
                'fraud_indicators' => json_encode([
                    'height_change' => '+5cm in 3 months',
                    'weight_change' => '-3kg in 3 months',
                    'impossible_growth' => 'Adult height cannot increase by 5cm in 3 months'
                ])
            ],
            
            // 2. Fake Medical Certificates
            [
                'player_id' => Player::where('fifa_connect_id', 'FRAUD_AGE_001')->first()->id ?? 5,
                'record_date' => Carbon::now()->subDays(5),
                'record_type' => 'medical_certificate',
                'diagnosis' => 'Medically fit for competition',
                'treatment' => 'None',
                'notes' => 'Certificate appears to be digitally altered',
                'fraud_type' => 'fake_medical_certificate',
                'fraud_description' => 'Medical certificate shows signs of digital manipulation',
                'status' => 'rejected',
                'fraud_indicators' => json_encode([
                    'digital_manipulation' => 'Certificate shows signs of editing',
                    'inconsistent_fonts' => 'Different font types in same document',
                    'date_discrepancy' => 'Certificate date doesn\'t match doctor\'s schedule'
                ])
            ]
        ];

        foreach ($fraudulentHealthRecords as $recordData) {
            $record = HealthRecord::create($recordData);
            $this->command->info("🚨 Created fraudulent health record: {$recordData['fraud_type']}");
        }
    }

    /**
     * Create fraudulent licenses
     */
    private function createFraudulentLicenses($association, $club)
    {
        $fraudulentLicenses = [
            // 1. Duplicate License Application
            [
                'player_id' => Player::where('fifa_connect_id', 'FRAUD_MULTI_001')->first()->id ?? 6,
                'license_type' => 'Professional',
                'season' => '2024-2025',
                'application_date' => Carbon::now()->subDays(20),
                'status' => 'pending',
                'fraud_type' => 'duplicate_application',
                'fraud_description' => 'Player applied for license under 3 different names',
                'fraud_indicators' => json_encode([
                    'multiple_applications' => '3 applications from same person',
                    'different_names' => 'Same photo, different names',
                    'same_fingerprints' => 'Biometric data matches across applications'
                ])
            ],
            
            // 2. Age-based License Fraud
            [
                'player_id' => Player::where('fifa_connect_id', 'FRAUD_AGE_001')->first()->id ?? 7,
                'license_type' => 'Youth',
                'season' => '2024-2025',
                'application_date' => Carbon::now()->subDays(15),
                'status' => 'rejected',
                'fraud_type' => 'age_based_fraud',
                'fraud_description' => 'Player too old for youth license category',
                'fraud_indicators' => json_encode([
                    'age_discrepancy' => 'Claims 19, actually 25',
                    'category_mismatch' => 'Too old for youth category',
                    'document_forgery' => 'Birth certificate appears forged'
                ])
            ],
            
            // 3. Identity Theft License
            [
                'player_id' => Player::where('fifa_connect_id', 'FRAUD_ID_001')->first()->id ?? 8,
                'license_type' => 'Professional',
                'season' => '2024-2025',
                'application_date' => Carbon::now()->subDays(10),
                'status' => 'suspicious',
                'fraud_type' => 'identity_theft',
                'fraud_description' => 'Player using brother\'s identity documents',
                'fraud_indicators' => json_encode([
                    'document_mismatch' => 'ID photo doesn\'t match applicant',
                    'family_identity' => 'Using brother\'s documents',
                    'photo_discrepancy' => 'Different person in photo vs documents'
                ])
            ]
        ];

        foreach ($fraudulentLicenses as $licenseData) {
            $license = License::create($licenseData);
            $this->command->info("🚨 Created fraudulent license: {$licenseData['fraud_type']}");
        }
    }
} 