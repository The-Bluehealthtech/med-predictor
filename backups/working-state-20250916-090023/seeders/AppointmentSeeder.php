<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Athlete;
use App\Models\User;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $athletes = Athlete::all();
        $doctors = User::where('role', 'doctor')->get();
        
        // Si pas de médecins, créer un médecin par défaut
        if ($doctors->isEmpty()) {
            $doctor = User::create([
                'name' => 'Dr. Jean Dupont',
                'email' => 'jean.dupont@med-predictor.com',
                'password' => bcrypt('password'),
                'role' => 'doctor',
            ]);
            $doctors = collect([$doctor]);
        }

        $appointmentTypes = [
            'consultation' => 'Consultation générale',
            'emergency' => 'Urgence',
            'follow_up' => 'Suivi',
            'pre_season' => 'Bilan pré-saison',
            'post_match' => 'Bilan post-match',
            'rehabilitation' => 'Rééducation',
            'routine_checkup' => 'Contrôle de routine',
            'injury_assessment' => 'Évaluation de blessure',
            'cardiac_evaluation' => 'Évaluation cardiaque',
            'concussion_assessment' => 'Évaluation commotion',
        ];

        $statuses = ['Planifié', 'Confirmé', 'Enregistré', 'Annulé'];

        // Créer des rendez-vous pour les 30 prochains jours
        for ($i = 0; $i < 15; $i++) {
            $athlete = $athletes->random();
            $doctor = $doctors->random();
            $appointmentType = array_rand($appointmentTypes);
            $status = $statuses[array_rand($statuses)];
            
            // Date aléatoire dans les 30 prochains jours
            $appointmentDate = Carbon::now()->addDays(rand(1, 30))->setHour(rand(8, 17))->setMinute(rand(0, 3) * 15);
            
            Appointment::create([
                'athlete_id' => $athlete->id,
                'doctor_id' => $doctor->id,
                'created_by' => User::first()->id,
                'appointment_date' => $appointmentDate,
                'duration_minutes' => rand(1, 4) * 15, // 15, 30, 45, ou 60 minutes
                'appointment_type' => $appointmentType,
                'status' => $status,
                'reason' => $this->getRandomReason($appointmentType),
                'notes' => $this->getRandomNotes(),
                'reminder_settings' => json_encode([
                    'email_reminder' => true,
                    'sms_reminder' => false,
                    'reminder_hours' => 24
                ]),
            ]);
        }

        $this->command->info('Appointments seeded successfully!');
    }

    private function getRandomReason(string $appointmentType): string
    {
        $reasons = [
            'consultation' => [
                'Contrôle de routine',
                'Douleur articulaire',
                'Fatigue persistante',
                'Problème de récupération'
            ],
            'emergency' => [
                'Blessure aiguë',
                'Douleur intense',
                'Traumatisme récent',
                'Symptômes inquiétants'
            ],
            'follow_up' => [
                'Suivi de blessure',
                'Contrôle post-traitement',
                'Évaluation de progression',
                'Ajustement de traitement'
            ],
            'pre_season' => [
                'Bilan pré-saison',
                'Évaluation de forme',
                'Contrôle médical obligatoire',
                'Planification d\'entraînement'
            ],
            'post_match' => [
                'Contrôle post-match',
                'Évaluation de fatigue',
                'Détection de blessures',
                'Récupération'
            ],
            'rehabilitation' => [
                'Rééducation post-blessure',
                'Récupération fonctionnelle',
                'Renforcement musculaire',
                'Retour au sport'
            ],
            'routine_checkup' => [
                'Contrôle de routine',
                'Bilan de santé',
                'Évaluation de forme',
                'Prévention'
            ],
            'injury_assessment' => [
                'Évaluation de blessure',
                'Diagnostic précis',
                'Plan de traitement',
                'Pronostic de guérison'
            ],
            'cardiac_evaluation' => [
                'Évaluation cardiaque',
                'Test d\'effort',
                'Contrôle ECG',
                'Bilan cardiovasculaire'
            ],
            'concussion_assessment' => [
                'Évaluation commotion',
                'Test cognitif',
                'Suivi neurologique',
                'Protocole de retour'
            ]
        ];

        $typeReasons = $reasons[$appointmentType] ?? ['Consultation médicale'];
        return $typeReasons[array_rand($typeReasons)];
    }

    private function getRandomNotes(): string
    {
        $notes = [
            'Patient en bonne forme générale',
            'À surveiller pour les prochaines semaines',
            'Recommandations d\'entraînement données',
            'Contrôle dans 2 semaines',
            'Pas de contre-indication à la pratique sportive',
            'Précautions particulières à respecter',
            'Bilan satisfaisant',
            'Amélioration notable depuis la dernière visite'
        ];

        return $notes[array_rand($notes)];
    }
} 