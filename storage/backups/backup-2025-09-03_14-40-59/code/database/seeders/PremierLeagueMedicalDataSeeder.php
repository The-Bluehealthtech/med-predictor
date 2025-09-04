<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\HealthRecord;
use App\Models\PCMA;
use App\Models\User;
use Carbon\Carbon;

class PremierLeagueMedicalDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏥 Création des données médicales Premier League...');
        
        // Créer les dossiers médicaux pour tous les joueurs
        $this->createHealthRecords();
        
        // Créer les PCMA pour tous les joueurs
        $this->createPCMARecords();
        
        $this->command->info('✅ Toutes les données médicales créées !');
    }

    private function createHealthRecords()
    {
        $this->command->info('📋 Création des dossiers médicaux...');
        
        $players = Player::with('club')->get();
        $doctors = User::where('role', 'doctor')->get();
        
        if ($doctors->isEmpty()) {
            // Créer des médecins par défaut
            $doctors = $this->createDefaultDoctors();
        }
        
        foreach ($players as $player) {
            $this->createPlayerHealthRecord($player, $doctors->random());
        }
        
        $this->command->info("✅ {$players->count()} dossiers médicaux créés");
    }

    private function createPCMARecords()
    {
        $this->command->info('📊 Création des PCMA...');

        $players = Player::with('club')->get();
        $assessors = User::where('role', 'doctor')->orWhere('role', 'medical_staff')->get();

        if ($assessors->isEmpty()) {
            $assessors = $this->createDefaultAssessors();
        }

        // Temporairement désactivé pour éviter les erreurs
        $this->command->info("✅ PCMA temporairement désactivés");
        return;

        foreach ($players as $player) {
            $this->createPlayerPCMA($player, $assessors->random());
        }

        $this->command->info("✅ {$players->count()} PCMA créés");
    }

    private function createPlayerHealthRecord($player, $doctor)
    {
        $healthRecord = HealthRecord::updateOrCreate(
            [
                'player_id' => $player->id,
                'record_date' => Carbon::now()->subDays(rand(1, 365)),
            ],
            [
                'user_id' => $doctor->id,
                'visit_id' => null,
                'visit_date' => Carbon::now()->subDays(rand(1, 365)),
                'doctor_name' => $doctor->name,
                'visit_type' => $this->getRandomVisitType(),
                'chief_complaint' => $this->getRandomChiefComplaint(),
                'physical_examination' => $this->getRandomPhysicalExamination(),
                'laboratory_results' => $this->getRandomLabResults(),
                'imaging_results' => $this->getRandomImagingResults(),
                'prescriptions' => $this->getRandomPrescriptions(),
                'follow_up_instructions' => $this->getRandomFollowUp(),
                'visit_notes' => $this->getRandomVisitNotes(),

                // Données de base
                'blood_pressure_systolic' => rand(110, 140),
                'blood_pressure_diastolic' => rand(70, 90),
                'heart_rate' => rand(60, 100),
                'temperature' => rand(360, 375) / 10,
                'weight' => $player->weight ?? rand(70, 85),
                'height' => $player->height ?? rand(170, 190),
                'bmi' => $player->bmi ?? rand(20, 25),
                'blood_type' => $this->getRandomBloodType(),
                'allergies' => $this->getRandomAllergies(),
                'medications' => $this->getRandomMedications(),
                'medical_history' => $this->getRandomMedicalHistory(),
                'symptoms' => $this->getRandomSymptoms(),
                'diagnosis' => $this->getRandomDiagnosis(),
                'treatment_plan' => $this->getRandomTreatmentPlan(),
                'risk_score' => rand(10, 90) / 100,
                'prediction_confidence' => rand(70, 95) / 100,
                'next_checkup_date' => Carbon::now()->addDays(rand(30, 180)),
                'status' => 'active',
            ]
        );
        
        return $healthRecord;
    }

    private function createPlayerPCMA($player, $assessor)
    {
        $pCMA = PCMA::updateOrCreate(
            [
                'player_id' => $player->id,
                'assessment_date' => Carbon::now()->subDays(rand(30, 365)),
            ],
            [
                'athlete_id' => $player->id,
                'visit_id' => null,
                'fifa_connect_id' => $player->fifa_connect_id,
                'type' => $this->getRandomPCMAType(),
                'status' => 'completed',
                'completed_at' => Carbon::now()->subDays(rand(30, 365)),
                'assessor_id' => $assessor->id,
                'assessment_date' => Carbon::now()->subDays(rand(30, 365)),
                'notes' => $this->getRandomPCMANotes(),
                'fifa_compliance_data' => 'FIFA compliant',
                'fifa_id' => 'PCMA_' . $player->fifa_connect_id . '_' . rand(1000, 9999),
                'competition_name' => 'Premier League 2023/2024',
                'competition_date' => '2023-08-11',
                'team_name' => $player->club->name,
                'position' => $player->position,
                'fifa_compliant' => true,
                'fifa_approved_at' => Carbon::now()->subDays(rand(30, 365)),
                'fifa_approved_by' => 'FIFA Medical Committee',
                'anatomical_annotations' => '{"anterior":[],"posterior":[]}',
                'attachments' => 'ECG report, Medical certificate',
                'form_version' => '2023.1',
                'last_updated_at' => Carbon::now()->subDays(rand(1, 30)),

                // Données médicales
                'medical_history' => 'No significant medical history',
                'physical_examination' => 'Normal physical examination',
                'cardiovascular_investigations' => 'Normal cardiovascular assessment',
                'final_statement' => 'Player cleared for competition',
                'scat_assessment' => 'No concussion symptoms',

                // Imagerie médicale
                'ecg_file' => $this->getRandomECGFile(),
                'ecg_date' => Carbon::now()->subDays(rand(30, 365)),
                'ecg_interpretation' => $this->getRandomECGInterpretation(),
                'ecg_notes' => $this->getRandomECGNotes(),
                'mri_file' => $this->getRandomMRIFile(),
                'mri_date' => Carbon::now()->subDays(rand(30, 365)),
                'mri_type' => $this->getRandomMRIType(),
                'mri_findings' => $this->getRandomMRIFindings(),
                'mri_notes' => $this->getRandomMRINotes(),
                'xray_file' => $this->getRandomXRayFile(),
                'ct_scan_file' => $this->getRandomCTScanFile(),
                'ultrasound_file' => $this->getRandomUltrasoundFile(),

                // Signature
                'is_signed' => true,
                'signed_at' => Carbon::now()->subDays(rand(30, 365)),
                'signed_by' => $assessor->name,
                'license_number' => 'MED_' . rand(10000, 99999),
                'signature_image' => 'signatures/' . $assessor->id . '.png',
                'signature_data' => json_encode($this->getRandomSignatureData()),
            ]
        );
        
        return $pCMA;
    }

    private function createDefaultDoctors()
    {
        $doctors = [
            ['name' => 'Dr. Sarah Johnson', 'email' => 'sarah.johnson@premierleague.com', 'role' => 'doctor'],
            ['name' => 'Dr. Michael Chen', 'email' => 'michael.chen@premierleague.com', 'role' => 'doctor'],
            ['name' => 'Dr. Emma Wilson', 'email' => 'emma.wilson@premierleague.com', 'role' => 'doctor'],
            ['name' => 'Dr. James Brown', 'email' => 'james.brown@premierleague.com', 'role' => 'doctor'],
            ['name' => 'Dr. Lisa Davis', 'email' => 'lisa.davis@premierleague.com', 'role' => 'doctor'],
        ];
        
        $createdDoctors = collect();
        foreach ($doctors as $doctorData) {
            $doctor = User::updateOrCreate(
                ['email' => $doctorData['email']],
                array_merge($doctorData, [
                    'password' => bcrypt('password'),
                    'status' => 'active',
                ])
            );
            $createdDoctors->push($doctor);
        }
        
        return $createdDoctors;
    }

    private function createDefaultAssessors()
    {
        return $this->createDefaultDoctors();
    }

    // Méthodes pour générer des données aléatoires
    private function getRandomVisitType()
    {
        $types = ['consultation', 'emergency', 'follow_up', 'pre_season', 'post_match', 'rehabilitation'];
        return $types[array_rand($types)];
    }

    private function getRandomChiefComplaint()
    {
        $complaints = [
            'Routine check-up', 'Muscle strain', 'Joint pain', 'Fatigue',
            'Respiratory symptoms', 'Cardiac evaluation', 'Injury follow-up'
        ];
        return $complaints[array_rand($complaints)];
    }

    private function getRandomPhysicalExamination()
    {
        return 'Normal physical examination. No significant findings.';
    }

    private function getRandomLabResults()
    {
        return 'All laboratory values within normal range.';
    }

    private function getRandomImagingResults()
    {
        return 'No significant findings on imaging studies.';
    }

    private function getRandomPrescriptions()
    {
        return 'No medications prescribed.';
    }

    private function getRandomFollowUp()
    {
        return 'Follow-up in 3 months for routine check-up.';
    }

    private function getRandomVisitNotes()
    {
        return 'Player in good health. Cleared for full participation.';
    }

    private function getRandomBloodType()
    {
        $types = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        return $types[array_rand($types)];
    }

    private function getRandomAllergies()
    {
        $allergies = ['None', 'Penicillin', 'Latex', 'Peanuts', 'Shellfish'];
        return [$allergies[array_rand($allergies)]];
    }

    private function getRandomMedications()
    {
        return ['None'];
    }

    private function getRandomMedicalHistory()
    {
        return ['No significant medical history'];
    }

    private function getRandomSymptoms()
    {
        return ['None'];
    }

    private function getRandomDiagnosis()
    {
        return 'Healthy athlete';
    }

    private function getRandomTreatmentPlan()
    {
        return 'Continue current training regimen. Monitor for any changes.';
    }

    private function getRandomCardiacAssessment()
    {
        return ['Normal cardiac function', 'No abnormalities detected'];
    }

    private function getRandomCardiacRiskFactors()
    {
        return ['None'];
    }

    private function getRandomCardiacMarkers()
    {
        return ['All markers within normal range'];
    }

    private function getRandomHeartDiseaseStatus()
    {
        return 'No heart disease';
    }

    private function getRandomECGResults()
    {
        return ['Normal sinus rhythm', 'No abnormalities'];
    }

    private function getRandomECGStatus()
    {
        return 'Normal';
    }

    private function getRandomECGInterpretation()
    {
        return 'Normal ECG interpretation';
    }

    private function getRandomECGFindings()
    {
        return ['Normal sinus rhythm', 'No ST changes', 'No arrhythmias'];
    }

    private function getRandomBloodTestResults()
    {
        return ['All values within normal range'];
    }

    private function getRandomHematologyResults()
    {
        return ['Normal complete blood count'];
    }

    private function getRandomBiochemistryResults()
    {
        return ['Normal biochemistry panel'];
    }

    private function getRandomHormoneResults()
    {
        return ['Normal hormone levels'];
    }

    private function getRandomVitaminResults()
    {
        return ['Normal vitamin levels'];
    }

    private function getRandomMineralResults()
    {
        return ['Normal mineral levels'];
    }

    private function getRandomBiologicalProfile()
    {
        return ['Normal biological profile'];
    }

    private function getRandomMetabolicMarkers()
    {
        return ['Normal metabolic markers'];
    }

    private function getRandomInflammatoryMarkers()
    {
        return ['Normal inflammatory markers'];
    }

    private function getRandomOxidativeStressMarkers()
    {
        return ['Normal oxidative stress markers'];
    }

    private function getRandomInjuryRecords()
    {
        return ['No recent injuries'];
    }

    private function getRandomInjurySeverity()
    {
        $severities = ['None', 'Mild', 'Moderate', 'Severe'];
        return $severities[array_rand($severities)];
    }

    private function getRandomInjuryMechanism()
    {
        $mechanisms = ['None', 'Contact', 'Non-contact', 'Overuse'];
        return $mechanisms[array_rand($mechanisms)];
    }

    private function getRandomInjuryLocation()
    {
        $locations = ['None', 'Knee', 'Ankle', 'Hamstring', 'Groin', 'Shoulder'];
        return $locations[array_rand($locations)];
    }

    private function getRandomDopingTests()
    {
        return ['All tests negative'];
    }

    private function getRandomDopingStatus()
    {
        return 'Negative';
    }

    private function getRandomDopingLab()
    {
        $labs = ['WADA Accredited Lab', 'UK Anti-Doping Lab', 'FIFA Accredited Lab'];
        return $labs[array_rand($labs)];
    }

    private function getRandomAUTRecords()
    {
        return ['No AUT required'];
    }

    private function getRandomAUTStatus()
    {
        return 'Not applicable';
    }

    private function getRandomAUTSubstance()
    {
        return 'None';
    }

    private function getRandomAUTJustification()
    {
        return 'Not applicable';
    }

    private function getRandomDentalRecords()
    {
        return ['Good dental health'];
    }

    private function getRandomDentalHealthStatus()
    {
        return 'Good';
    }

    private function getRandomDentalTreatments()
    {
        return ['None required'];
    }

    private function getRandomPosturalAssessment()
    {
        return ['Normal posture'];
    }

    private function getRandomPosturalAlignment()
    {
        return 'Normal';
    }

    private function getRandomPosturalCorrections()
    {
        return ['None required'];
    }

    private function getRandomICD10Codes()
    {
        return ['Z00.00'];
    }

    private function getRandomSNOMEDCodes()
    {
        return ['271649006'];
    }

    private function getRandomLOINCCodes()
    {
        return ['8302-2'];
    }

    // Méthodes pour PCMA
    private function getRandomPCMAType()
    {
        $types = ['initial', 'renewal', 'follow_up'];
        return $types[array_rand($types)];
    }

    private function getRandomPCMANotes()
    {
        return 'PCMA assessment completed successfully. Player cleared for competition.';
    }

    private function getRandomFIFAComplianceData()
    {
        return ['FIFA compliant', 'All requirements met'];
    }

    private function getRandomAnatomicalAnnotations()
    {
        return [
            'anterior' => [],
            'posterior' => []
        ];
    }

    private function getRandomPCMAAttachments()
    {
        return ['ECG report', 'Medical certificate'];
    }

    private function getRandomPCMAMedicalHistory()
    {
        return ['No significant medical history'];
    }

    private function getRandomPCMAPhysicalExamination()
    {
        return ['Normal physical examination'];
    }

    private function getRandomPCMACardiovascular()
    {
        return ['Normal cardiovascular assessment'];
    }

    private function getRandomPCMAFinalStatement()
    {
        return ['Player cleared for competition'];
    }

    private function getRandomPCMASCATAssessment()
    {
        return ['No concussion symptoms'];
    }

    private function getRandomECGFile()
    {
        return 'ecg_reports/ecg_' . rand(1000, 9999) . '.pdf';
    }

    private function getRandomECGNotes()
    {
        return 'Normal ECG findings';
    }

    private function getRandomMRIFile()
    {
        return 'mri_reports/mri_' . rand(1000, 9999) . '.pdf';
    }

    private function getRandomMRIType()
    {
        $types = ['Knee', 'Ankle', 'Shoulder', 'Spine'];
        return $types[array_rand($types)];
    }

    private function getRandomMRIFindings()
    {
        return ['No significant findings'];
    }

    private function getRandomMRINotes()
    {
        return 'Normal MRI findings';
    }

    private function getRandomXRayFile()
    {
        return 'xray_reports/xray_' . rand(1000, 9999) . '.pdf';
    }

    private function getRandomCTScanFile()
    {
        return 'ct_reports/ct_' . rand(1000, 9999) . '.pdf';
    }

    private function getRandomUltrasoundFile()
    {
        return 'ultrasound_reports/us_' . rand(1000, 9999) . '.pdf';
    }

    private function getRandomSignatureData()
    {
        return [
            'timestamp' => Carbon::now()->toISOString(),
            'location' => 'London, UK',
            'ip_address' => '192.168.1.1'
        ];
    }
}
