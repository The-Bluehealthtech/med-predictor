<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FhirPatient;
use App\Models\FhirCondition;
use App\Models\ClinicalConsultation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ClinicalWorkflowScenarioTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $patientUser;
    protected $clinicianUser;
    protected $patient;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->patientUser = User::factory()->create([
            'email' => 'patient@test.com',
            'role' => 'patient'
        ]);
        
        $this->clinicianUser = User::factory()->create([
            'email' => 'clinician@test.com',
            'role' => 'clinician'
        ]);
        
        // Create test patient
        $this->patient = FhirPatient::create([
            'fit_patient_id' => FhirPatient::generateFitPatientId(),
            'resource_type' => 'Patient',
            'name' => [
                [
                    'family' => 'TestPatient',
                    'given' => ['Jean', 'Pierre']
                ]
            ],
            'gender' => 'male',
            'birth_date' => '1980-01-15',
            'active' => true,
            'created_by' => $this->clinicianUser->id,
            'updated_by' => $this->clinicianUser->id
        ]);
    }

    /** @test */
    public function complete_clinical_workflow_scenario()
    {
        // Step 1: Patient accesses portal
        $response = $this->actingAs($this->patientUser)
                         ->get('/clinical/patient-portal');
        
        $response->assertStatus(200);
        $response->assertSee('Portail Patient');
        $response->assertSee('Saisir mes symptômes');

        // Step 2: Patient submits symptoms
        $symptomsData = [
            'patient_id' => $this->patient->id,
            'symptoms' => ['Douleur thoracique', 'Essoufflement', 'Fatigue'],
            'severity' => 'moderate',
            'duration' => 'Depuis 3 jours',
            'additional_info' => 'Symptômes aggravés par l\'effort physique'
        ];

        $response = $this->actingAs($this->patientUser)
                         ->postJson('/api/clinical/symptoms', $symptomsData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Symptômes enregistrés avec succès'
        ]);

        // Verify condition was created
        $this->assertDatabaseHas('fhir_conditions', [
            'patient_id' => $this->patient->id,
            'clinical_status' => 'active'
        ]);

        // Step 3: Clinician accesses portal
        $response = $this->actingAs($this->clinicianUser)
                         ->get('/clinical/clinician-portal');

        $response->assertStatus(200);
        $response->assertSee('Portail Clinicien');
        $response->assertSee('Nouvelle consultation');

        // Step 4: Clinician creates consultation
        $consultationData = [
            'patient_id' => $this->patient->id,
            'chief_complaint' => 'Douleur thoracique avec essoufflement',
            'history_present_illness' => 'Patient de 45 ans, symptômes depuis 3 jours, aggravés par l\'effort',
            'physical_exam' => 'TA: 140/90, FC: 95/min, Temp: 37.1°C, O2: 98%',
            'assessment' => 'Douleur thoracique d\'origine probablement musculo-squelettique',
            'plan' => 'Repos, antalgiques, ECG, réévaluation dans 48h'
        ];

        $response = $this->actingAs($this->clinicianUser)
                         ->postJson('/api/clinical/consultations', $consultationData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Consultation enregistrée avec succès'
        ]);

        // Verify consultation was created
        $this->assertDatabaseHas('clinical_consultations', [
            'patient_id' => $this->patient->id,
            'clinician_id' => $this->clinicianUser->id,
            'consultation_type' => 'initial'
        ]);

        // Step 5: Clinical decision support
        $decisionSupportData = [
            'patient_id' => $this->patient->id,
            'clinical_data' => 'Patient avec douleur thoracique, TA élevée, facteurs de risque cardiovasculaire',
            'question' => 'Quelle est la probabilité d\'un syndrome coronarien aigu ?'
        ];

        $response = $this->actingAs($this->clinicianUser)
                         ->postJson('/api/clinical/decision-support', $decisionSupportData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Support décisionnel généré avec succès'
        ]);

        // Verify AI response structure
        $response->assertJsonStructure([
            'ai_response' => [
                'evidence_summary',
                'recommendations',
                'clinical_trials',
                'risk_factors',
                'confidence_level'
            ]
        ]);

        // Step 6: Generate summary
        $summaryData = [
            'text' => 'Consultation complète du patient avec douleur thoracique...',
            'type' => 'consultation'
        ];

        $response = $this->actingAs($this->clinicianUser)
                         ->postJson('/api/clinical/summarize', $summaryData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Step 7: Search clinical trials
        $response = $this->actingAs($this->clinicianUser)
                         ->getJson('/api/clinical/clinical-trials?patient_id=' . $this->patient->id . '&condition=douleur thoracique');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Step 8: Check care gaps
        $response = $this->actingAs($this->clinicianUser)
                         ->getJson('/api/clinical/care-gaps/' . $this->patient->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify complete workflow data
        $this->assertDatabaseHas('fhir_patients', [
            'id' => $this->patient->id
        ]);

        $this->assertDatabaseHas('fhir_conditions', [
            'patient_id' => $this->patient->id
        ]);

        $this->assertDatabaseHas('clinical_consultations', [
            'patient_id' => $this->patient->id
        ]);
    }

    /** @test */
    public function patient_cannot_access_clinician_portal()
    {
        $response = $this->actingAs($this->patientUser)
                         ->get('/clinical/clinician-portal');

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthorized_user_cannot_access_any_portal()
    {
        $unauthorizedUser = User::factory()->create(['role' => 'player']);

        $response = $this->actingAs($unauthorizedUser)
                         ->get('/clinical/patient-portal');

        $response->assertStatus(403);

        $response = $this->actingAs($unauthorizedUser)
                         ->get('/clinical/clinician-portal');

        $response->assertStatus(403);
    }

    /** @test */
    public function api_endpoints_require_authentication()
    {
        $response = $this->postJson('/api/clinical/patients', [
            'name' => ['family' => 'Test'],
            'gender' => 'male',
            'birth_date' => '1990-01-01'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function api_endpoints_validate_required_fields()
    {
        $response = $this->actingAs($this->clinicianUser)
                         ->postJson('/api/clinical/patients', [
                             'gender' => 'male'
                             // Missing required fields
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'birth_date']);
    }

    /** @test */
    public function fhir_patient_model_works_correctly()
    {
        $patient = FhirPatient::create([
            'fit_patient_id' => FhirPatient::generateFitPatientId(),
            'resource_type' => 'Patient',
            'name' => [
                [
                    'family' => 'Test',
                    'given' => ['Patient']
                ]
            ],
            'gender' => 'female',
            'birth_date' => '1990-01-01',
            'active' => true,
            'created_by' => $this->clinicianUser->id,
            'updated_by' => $this->clinicianUser->id
        ]);

        $this->assertEquals('Test Patient', $patient->full_name);
        $this->assertEquals(34, $patient->age);
        $this->assertTrue($patient->active);

        $fhirJson = $patient->toFhirJson();
        $this->assertEquals('Patient', $fhirJson['resourceType']);
        $this->assertEquals('female', $fhirJson['gender']);
    }

    /** @test */
    public function clinical_consultation_workflow()
    {
        $consultation = ClinicalConsultation::create([
            'patient_id' => $this->patient->id,
            'clinician_id' => $this->clinicianUser->id,
            'consultation_type' => 'initial',
            'chief_complaint' => 'Test complaint',
            'history_present_illness' => 'Test history',
            'status' => 'scheduled'
        ]);

        // Test status transitions
        $this->assertTrue($consultation->start());
        $this->assertEquals('in_progress', $consultation->status);
        $this->assertNotNull($consultation->started_at);

        $this->assertTrue($consultation->complete());
        $this->assertEquals('completed', $consultation->status);
        $this->assertNotNull($consultation->completed_at);

        // Test summary generation
        $summary = $consultation->generateSummary();
        $this->assertStringContainsString('Test complaint', $summary);
        $this->assertStringContainsString('Test history', $summary);
    }
}
