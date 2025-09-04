<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = [
            [
                'name' => 'Jean Dupont',
                'birth_date' => '1985-03-15',
                'gender' => 'male',
                'phone' => '01 23 45 67 89',
                'email' => 'jean.dupont@email.com',
                'address' => '123 Rue de la Paix, 75001 Paris',
                'medical_history' => 'Hypertension, diabète type 2',
                'allergies' => 'Pénicilline',
                'medications' => 'Métoprolol, Metformine',
            ],
            [
                'name' => 'Marie Martin',
                'birth_date' => '1990-07-22',
                'gender' => 'female',
                'phone' => '01 98 76 54 32',
                'email' => 'marie.martin@email.com',
                'address' => '456 Avenue des Champs, 75008 Paris',
                'medical_history' => 'Asthme',
                'allergies' => 'Aucune',
                'medications' => 'Ventoline',
            ],
            [
                'name' => 'Pierre Durand',
                'birth_date' => '1978-11-08',
                'gender' => 'male',
                'phone' => '01 55 44 33 22',
                'email' => 'pierre.durand@email.com',
                'address' => '789 Boulevard Saint-Germain, 75006 Paris',
                'medical_history' => 'Cholestérol élevé',
                'allergies' => 'Aucune',
                'medications' => 'Simvastatine',
            ],
            [
                'name' => 'Sophie Bernard',
                'birth_date' => '1992-04-12',
                'gender' => 'female',
                'phone' => '01 11 22 33 44',
                'email' => 'sophie.bernard@email.com',
                'address' => '321 Rue de Rivoli, 75001 Paris',
                'medical_history' => 'Migraines',
                'allergies' => 'Sulfamides',
                'medications' => 'Sumatriptan',
            ],
            [
                'name' => 'Lucas Petit',
                'birth_date' => '2000-09-30',
                'gender' => 'male',
                'phone' => '01 66 77 88 99',
                'email' => 'lucas.petit@email.com',
                'address' => '654 Rue du Commerce, 75015 Paris',
                'medical_history' => 'Aucun',
                'allergies' => 'Aucune',
                'medications' => 'Aucun',
            ],
        ];

        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }
    }
}
