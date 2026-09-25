<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LicenseType;
use App\Models\User;

class LicenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('role', 'system_admin')->first() ?? User::first();

        $licenseTypes = [
            [
                'name' => 'Professional Player License',
                'code' => 'PROF',
                'description' => 'Full professional player license for senior players in professional leagues',
                'requirements' => [
                    'fifa_connect_id' => ['required' => true, 'message' => 'FIFA Connect ID is required'],
                    'medical_clearance' => ['required' => true, 'message' => 'Medical clearance is required'],
                    'fitness_certificate' => ['required' => true, 'message' => 'Fitness certificate is required'],
                    'contract' => ['required' => true, 'message' => 'Valid contract is required'],
                    'passport' => ['required' => true, 'message' => 'Valid passport is required'],
                    'disciplinary_record' => ['required' => true, 'message' => 'Clean disciplinary record is required'],
                ],
                'validation_rules' => [
                    'min_age' => 16,
                    'max_age' => 40,
                    'min_experience_years' => 1,
                    'required_documents' => ['medical_clearance', 'fitness_certificate', 'contract', 'passport'],
                    'approval_workflow' => ['club_approval', 'association_approval', 'fifa_validation']
                ],
                'approval_process' => [
                    'steps' => [
                        ['name' => 'Club Submission', 'role' => 'club_admin', 'order' => 1],
                        ['name' => 'Association Review', 'role' => 'association_registrar', 'order' => 2],
                        ['name' => 'Medical Verification', 'role' => 'association_medical', 'order' => 3],
                        ['name' => 'Final Approval', 'role' => 'association_admin', 'order' => 4],
                    ],
                    'estimated_duration_days' => 14
                ],
                'validity_period_months' => 12,
                'fee_amount' => 500.00,
                'fee_currency' => 'USD',
                'requires_medical_clearance' => true,
                'requires_fitness_certificate' => true,
                'requires_contract' => true,
                'requires_work_permit' => false,
                'requires_international_clearance' => false,
                'age_restrictions' => ['min_age' => 16, 'max_age' => 40],
                'position_restrictions' => ['allowed_positions' => ['ST', 'CF', 'LW', 'RW', 'CAM', 'CM', 'CDM', 'LB', 'RB', 'CB', 'GK']],
                'experience_requirements' => ['min_years' => 1],
                'requires_association_approval' => true,
                'requires_club_approval' => true,
                'max_players_per_club' => 50,
                'max_players_total' => null,
                'document_templates' => [
                    'application_form' => 'templates/professional_application.pdf',
                    'medical_form' => 'templates/medical_clearance.pdf',
                    'contract_template' => 'templates/professional_contract.pdf'
                ],
                'notification_settings' => [
                    'club_admin' => ['application_submitted', 'status_changed', 'approved', 'rejected'],
                    'association_registrar' => ['application_received', 'medical_verification_needed'],
                    'association_medical' => ['medical_review_required'],
                    'association_admin' => ['final_approval_required']
                ]
            ],
            [
                'name' => 'Amateur Player License',
                'code' => 'AMAT',
                'description' => 'Amateur player license for non-professional players',
                'requirements' => [
                    'fifa_connect_id' => ['required' => true, 'message' => 'FIFA Connect ID is required'],
                    'medical_clearance' => ['required' => true, 'message' => 'Medical clearance is required'],
                    'fitness_certificate' => ['required' => true, 'message' => 'Fitness certificate is required'],
                    'amateur_declaration' => ['required' => true, 'message' => 'Amateur declaration is required'],
                ],
                'validation_rules' => [
                    'min_age' => 14,
                    'max_age' => 50,
                    'required_documents' => ['medical_clearance', 'fitness_certificate', 'amateur_declaration'],
                    'approval_workflow' => ['club_approval', 'association_approval']
                ],
                'approval_process' => [
                    'steps' => [
                        ['name' => 'Club Submission', 'role' => 'club_admin', 'order' => 1],
                        ['name' => 'Association Review', 'role' => 'association_registrar', 'order' => 2],
                        ['name' => 'Final Approval', 'role' => 'association_admin', 'order' => 3],
                    ],
                    'estimated_duration_days' => 7
                ],
                'validity_period_months' => 12,
                'fee_amount' => 100.00,
                'fee_currency' => 'USD',
                'requires_medical_clearance' => true,
                'requires_fitness_certificate' => true,
                'requires_contract' => false,
                'requires_work_permit' => false,
                'requires_international_clearance' => false,
                'age_restrictions' => ['min_age' => 14, 'max_age' => 50],
                'position_restrictions' => ['allowed_positions' => ['ST', 'CF', 'LW', 'RW', 'CAM', 'CM', 'CDM', 'LB', 'RB', 'CB', 'GK']],
                'experience_requirements' => [],
                'requires_association_approval' => true,
                'requires_club_approval' => true,
                'max_players_per_club' => 100,
                'max_players_total' => null,
                'document_templates' => [
                    'application_form' => 'templates/amateur_application.pdf',
                    'medical_form' => 'templates/medical_clearance.pdf',
                    'amateur_declaration' => 'templates/amateur_declaration.pdf'
                ],
                'notification_settings' => [
                    'club_admin' => ['application_submitted', 'status_changed', 'approved', 'rejected'],
                    'association_registrar' => ['application_received'],
                    'association_admin' => ['final_approval_required']
                ]
            ],
            [
                'name' => 'Youth Player License',
                'code' => 'YOUTH',
                'description' => 'Youth player license for players under 18 years old',
                'requirements' => [
                    'fifa_connect_id' => ['required' => true, 'message' => 'FIFA Connect ID is required'],
                    'medical_clearance' => ['required' => true, 'message' => 'Medical clearance is required'],
                    'fitness_certificate' => ['required' => true, 'message' => 'Fitness certificate is required'],
                    'parental_consent' => ['required' => true, 'message' => 'Parental consent is required'],
                    'school_attendance' => ['required' => true, 'message' => 'School attendance certificate is required'],
                ],
                'validation_rules' => [
                    'min_age' => 8,
                    'max_age' => 17,
                    'required_documents' => ['medical_clearance', 'fitness_certificate', 'parental_consent', 'school_attendance'],
                    'approval_workflow' => ['club_approval', 'parental_approval', 'association_approval']
                ],
                'approval_process' => [
                    'steps' => [
                        ['name' => 'Club Submission', 'role' => 'club_admin', 'order' => 1],
                        ['name' => 'Parental Consent', 'role' => 'parent', 'order' => 2],
                        ['name' => 'Association Review', 'role' => 'association_registrar', 'order' => 3],
                        ['name' => 'Medical Review', 'role' => 'association_medical', 'order' => 4],
                        ['name' => 'Final Approval', 'role' => 'association_admin', 'order' => 5],
                    ],
                    'estimated_duration_days' => 10
                ],
                'validity_period_months' => 12,
                'fee_amount' => 50.00,
                'fee_currency' => 'USD',
                'requires_medical_clearance' => true,
                'requires_fitness_certificate' => true,
                'requires_contract' => false,
                'requires_work_permit' => false,
                'requires_international_clearance' => false,
                'age_restrictions' => ['min_age' => 8, 'max_age' => 17],
                'position_restrictions' => ['allowed_positions' => ['ST', 'CF', 'LW', 'RW', 'CAM', 'CM', 'CDM', 'LB', 'RB', 'CB', 'GK']],
                'experience_requirements' => [],
                'requires_association_approval' => true,
                'requires_club_approval' => true,
                'max_players_per_club' => 200,
                'max_players_total' => null,
                'document_templates' => [
                    'application_form' => 'templates/youth_application.pdf',
                    'medical_form' => 'templates/medical_clearance.pdf',
                    'parental_consent' => 'templates/parental_consent.pdf',
                    'school_attendance' => 'templates/school_attendance.pdf'
                ],
                'notification_settings' => [
                    'club_admin' => ['application_submitted', 'status_changed', 'approved', 'rejected'],
                    'parent' => ['consent_required', 'application_approved'],
                    'association_registrar' => ['application_received'],
                    'association_medical' => ['medical_review_required'],
                    'association_admin' => ['final_approval_required']
                ]
            ],
            [
                'name' => 'International Player License',
                'code' => 'INTL',
                'description' => 'International player license for players representing national teams',
                'requirements' => [
                    'fifa_connect_id' => ['required' => true, 'message' => 'FIFA Connect ID is required'],
                    'medical_clearance' => ['required' => true, 'message' => 'Medical clearance is required'],
                    'fitness_certificate' => ['required' => true, 'message' => 'Fitness certificate is required'],
                    'contract' => ['required' => true, 'message' => 'Valid contract is required'],
                    'international_clearance' => ['required' => true, 'message' => 'International clearance is required'],
                    'work_permit' => ['required' => true, 'message' => 'Work permit is required'],
                    'visa' => ['required' => true, 'message' => 'Valid visa is required'],
                ],
                'validation_rules' => [
                    'min_age' => 16,
                    'max_age' => 35,
                    'min_experience_years' => 3,
                    'required_documents' => ['medical_clearance', 'fitness_certificate', 'contract', 'international_clearance', 'work_permit', 'visa'],
                    'approval_workflow' => ['club_approval', 'association_approval', 'fifa_approval', 'government_approval']
                ],
                'approval_process' => [
                    'steps' => [
                        ['name' => 'Club Submission', 'role' => 'club_admin', 'order' => 1],
                        ['name' => 'Association Review', 'role' => 'association_registrar', 'order' => 2],
                        ['name' => 'Medical Verification', 'role' => 'association_medical', 'order' => 3],
                        ['name' => 'FIFA Clearance', 'role' => 'fifa_official', 'order' => 4],
                        ['name' => 'Government Approval', 'role' => 'government_official', 'order' => 5],
                        ['name' => 'Final Approval', 'role' => 'association_admin', 'order' => 6],
                    ],
                    'estimated_duration_days' => 30
                ],
                'validity_period_months' => 24,
                'fee_amount' => 1000.00,
                'fee_currency' => 'USD',
                'requires_medical_clearance' => true,
                'requires_fitness_certificate' => true,
                'requires_contract' => true,
                'requires_work_permit' => true,
                'requires_international_clearance' => true,
                'age_restrictions' => ['min_age' => 16, 'max_age' => 35],
                'position_restrictions' => ['allowed_positions' => ['ST', 'CF', 'LW', 'RW', 'CAM', 'CM', 'CDM', 'LB', 'RB', 'CB', 'GK']],
                'experience_requirements' => ['min_years' => 3],
                'requires_association_approval' => true,
                'requires_club_approval' => true,
                'max_players_per_club' => 10,
                'max_players_total' => 500,
                'document_templates' => [
                    'application_form' => 'templates/international_application.pdf',
                    'medical_form' => 'templates/medical_clearance.pdf',
                    'contract_template' => 'templates/international_contract.pdf',
                    'international_clearance' => 'templates/international_clearance.pdf',
                    'work_permit_application' => 'templates/work_permit_application.pdf'
                ],
                'notification_settings' => [
                    'club_admin' => ['application_submitted', 'status_changed', 'approved', 'rejected'],
                    'association_registrar' => ['application_received', 'fifa_clearance_required'],
                    'association_medical' => ['medical_review_required'],
                    'fifa_official' => ['clearance_requested'],
                    'government_official' => ['work_permit_application'],
                    'association_admin' => ['final_approval_required']
                ]
            ],
            [
                'name' => 'Temporary Player License',
                'code' => 'TEMP',
                'description' => 'Temporary license for short-term player registrations',
                'requirements' => [
                    'fifa_connect_id' => ['required' => true, 'message' => 'FIFA Connect ID is required'],
                    'medical_clearance' => ['required' => true, 'message' => 'Medical clearance is required'],
                    'emergency_contract' => ['required' => true, 'message' => 'Emergency contract is required'],
                ],
                'validation_rules' => [
                    'min_age' => 16,
                    'max_age' => 40,
                    'required_documents' => ['medical_clearance', 'emergency_contract'],
                    'approval_workflow' => ['club_approval', 'association_approval']
                ],
                'approval_process' => [
                    'steps' => [
                        ['name' => 'Club Submission', 'role' => 'club_admin', 'order' => 1],
                        ['name' => 'Emergency Review', 'role' => 'association_registrar', 'order' => 2],
                        ['name' => 'Fast Approval', 'role' => 'association_admin', 'order' => 3],
                    ],
                    'estimated_duration_days' => 2
                ],
                'validity_period_months' => 3,
                'fee_amount' => 200.00,
                'fee_currency' => 'USD',
                'requires_medical_clearance' => true,
                'requires_fitness_certificate' => false,
                'requires_contract' => true,
                'requires_work_permit' => false,
                'requires_international_clearance' => false,
                'age_restrictions' => ['min_age' => 16, 'max_age' => 40],
                'position_restrictions' => ['allowed_positions' => ['ST', 'CF', 'LW', 'RW', 'CAM', 'CM', 'CDM', 'LB', 'RB', 'CB', 'GK']],
                'experience_requirements' => [],
                'requires_association_approval' => true,
                'requires_club_approval' => true,
                'max_players_per_club' => 5,
                'max_players_total' => 100,
                'document_templates' => [
                    'application_form' => 'templates/temporary_application.pdf',
                    'medical_form' => 'templates/medical_clearance.pdf',
                    'emergency_contract' => 'templates/emergency_contract.pdf'
                ],
                'notification_settings' => [
                    'club_admin' => ['application_submitted', 'status_changed', 'approved', 'rejected'],
                    'association_registrar' => ['emergency_application_received'],
                    'association_admin' => ['fast_approval_required']
                ]
            ]
        ];

        foreach ($licenseTypes as $licenseTypeData) {
            LicenseType::create(array_merge($licenseTypeData, [
                'created_by' => $adminUser->id,
                'is_active' => true
            ]));
        }

        $this->command->info('License types seeded successfully!');
    }
} 