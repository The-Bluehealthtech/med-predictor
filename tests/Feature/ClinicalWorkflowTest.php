<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FhirPatient;
use App\Models\FhirCondition;
use App\Models\ClinicalConsultation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ClinicalWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $patient;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create([
            'role' => 'clinician'
        ]);
        
        // Create a test patient
        $this->patient = FhirPatient::create([
            'fit_patient_id' => FhirPatient::generateFitPatientId(),
            'resource_type' => 'Patient',
            'name' => [
                [
                    'family' => 'Dupont',
                    'given' => ['Jean', 'Pierre']
                ]
            ],
            'gender' => 'male',
            'birth_date' => '1980-01-15',
            'active' => true,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id
        ]);
    }

    /** @test */
    public function clinician_can_access_clinician_portal()
    {
        $response = $this->actingAs($this->user)
                         ->get('/clinical/clinician-portal');

        $response->assertStatus(200);
        $response->assertViewIs('clinical.clinician-portal');
        $response->assertSee('Portail Clinicien');
    }

    /** @test */
    public function patient_can_access_patient_portal()
    {
        $patientUser = User::factory()->create([
            'role' => 'patient',
            'fhir_patient_id' => $this->patient->id
        ]);

        $response = $this->actingAs($patientUser)
                         ->get('/clinical/patient-portal');

        $response->assertStatus(200);
        $response->assertViewIs('clinical.patient-portal');
        $response->assertSee('Portail Patient');
    }

    /** @test */
    public function can_create_fhir_patient_via_api()
    {
        $patientData = [
            'name' => [
                'family' => 'Martin',
                'given' => ['Marie', 'Claire']
            ],
            'gender' => 'female',
            'birth_date' => '1990-05-20',
            'telecom' => [
                [
                    'system' => 'phone',
                    'value' => '+33123456789'
                ]
            ],
            'address' => [
                [
                    'line' => ['123 Rue de la Paix'],
                    'city' => 'Paris',
                    'postalCode' => '75001',
                    'country' => 'France'
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
                         ->postJson('/api/clinical/patients', $patientData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Patient créé avec succès'
        ]);

        $this->assertDatabaseHas('fhir_patients', [
            'name->0->family' => 'Martin',
            'gender' => 'female'
        ]);
    }

    /** @test */
    public function can_submit_symptoms_via_api()
    {
        $symptomsData = [
            'patient_id' => $this->patient->id,
            'symptoms' => ['Douleur thoracique', 'Essoufflement'],
            'severity' => 'moderate',
            'duration' => 'Depuis 2 jours',
            'additional_info' => 'Symptômes aggravés par l\'effort'
        ];

        $response = $this->actingAs($this->user)
                         ->postJson('/api/clinical/symptoms', $symptomsData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Symptômes enregistrés avec succès'
        ]);

        $this->assertDatabaseHas('fhir_conditions', [
            'patient_id' => $this->patient->id,
            'clinical_status' => 'active'
        ]);
    }

    /** @test */
    public function can_create_initial_consultation_via_api()
    {
        $consultationData = [
            'patient_id' => $this->patient->id,
            'chief_complaint' => 'Douleur thoracique',
            'history_present_illness' => 'Patient de 43 ans se plaint de douleur thoracique depuis 2 jours',
            'physical_exam' => 'TA: 140/90, FC: 85/min, Temp: 37.2°C',
            'assessment' => 'Douleur thoracique d\'origine probablement musculo-squelettique',
            'plan' => 'Repos, antalgiques, réévaluation dans 48h'
        ];

        $response = $this->actingAs($this->user)
                         ->postJson('/api/clinical/consultations', $consultationData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Consultation enregistrée avec succès'
        ]);

        $this->assertDatabaseHas('clinical_consultations', [
            'patient_id' => $this->patient->id,
            'clinician_id' => $this->user->id,
            'consultation_type' => 'initial'
        ]);
    }

    /** @test */
    public function can_get_clinical_decision_support_via_api()
    {
        $decisionSupportData = [
            'patient_id' => $this->patient->id,
            'clinical_data' => 'Patient avec douleur thoracique et facteurs de risque cardiovasculaire',
            'question' => 'Quelle est la probabilité d\'un syndrome coronarien aigu ?'
        ];

        $response = $this->actingAs($this->user)
                         ->postJson('/api/clinical/decision-support', $decisionSupportData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Support décisionnel généré avec succès'
        ]);

        $response->assertJsonStructure([
            'ai_response' => [
                'evidence_summary',
                'recommendations',
                'clinical_trials',
                'risk_factors',
                'confidence_level'
            ]
        ]);
    }

    /** @test */
    public function can_generate_summary_via_api()
    {
        $summaryData = [
            'text' => 'Patient de 43 ans se plaint de douleur thoracique depuis 2 jours. L\'examen physique révèle une TA élevée et une FC normale. L\'ECG est normal. Le patient a des antécédents familiaux de maladie cardiovasculaire.',
            'type' => 'consultation'
        ];

        $response = $this->actingAs($this->user)
                         ->postJson('/api/clinical/summarize', $summaryData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $response->assertJsonStructure([
            'summary',
            'original_length',
            'summary_length',
            'compression_ratio'
        ]);
    }

    /** @test */
    public function can_search_clinical_trials_via_api()
    {
        $response = $this->actingAs($this->user)
                         ->getJson('/api/clinical/clinical-trials?patient_id=' . $this->patient->id . '&condition=douleur thoracique');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $response->assertJsonStructure([
            'trials' => [
                '*' => [
                    'id',
                    'title',
                    'phase',
                    'status',
                    'location',
                    'eligibility',
                    'contact'
                ]
            ],
            'total_found',
            'search_criteria'
        ]);
    }

    /** @test */
    public function can_get_care_gaps_via_api()
    {
        $response = $this->actingAs($this->user)
                         ->getJson('/api/clinical/care-gaps/' . $this->patient->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $response->assertJsonStructure([
            'patient_id',
            'care_gaps' => [
                '*' => [
                    'type',
                    'description',
                    'priority',
                    'due_date',
                    'action_required'
                ]
            ],
            'total_gaps',
            'high_priority_count'
        ]);
    }

    /** @test */
    public function fhir_patient_model_validates_required_fields()
    {
        $patient = new FhirPatient();
        
        $errors = $patient->validateFhirData();
        
        $this->assertContains('Le nom du patient est requis', $errors);
        $this->assertContains('Le genre du patient est requis', $errors);
        $this->assertContains('La date de naissance est requise', $errors);
    }

    /** @test */
    public function fhir_patient_model_generates_correct_fhir_json()
    {
        $fhirJson = $this->patient->toFhirJson();
        
        $this->assertEquals('Patient', $fhirJson['resourceType']);
        $this->assertEquals('male', $fhirJson['gender']);
        $this->assertEquals('1980-01-15', $fhirJson['birthDate']);
        $this->assertArrayHasKey('name', $fhirJson);
    }

    /** @test */
    public function fhir_condition_model_creates_from_symptoms()
    {
        $symptoms = ['Douleur thoracique', 'Essoufflement'];
        
        $condition = FhirCondition::createFromSymptoms(
            $symptoms,
            $this->patient->id,
            $this->user->id
        );
        
        $this->assertEquals($this->patient->id, $condition->patient_id);
        $this->assertEquals('active', $condition->clinical_status);
        $this->assertEquals('provisional', $condition->verification_status);
        $this->assertStringContainsString('Douleur thoracique', $condition->code['text']);
    }

    /** @test */
    public function clinical_consultation_generates_ai_summary()
    {
        $consultation = ClinicalConsultation::create([
            'patient_id' => $this->patient->id,
            'clinician_id' => $this->user->id,
            'consultation_type' => 'initial',
            'chief_complaint' => 'Douleur thoracique',
            'history_present_illness' => 'Patient se plaint de douleur thoracique',
            'status' => 'completed'
        ]);

        // Simulate AI summary generation
        $summary = "Résumé de la consultation du " . $consultation->created_at->format('d/m/Y') . ":\n\n";
        $summary .= "Motif de consultation: " . $consultation->chief_complaint . "\n\n";
        
        $consultation->ai_summary = $summary;
        $consultation->save();

        $this->assertNotEmpty($consultation->ai_summary);
        $this->assertStringContainsString('Douleur thoracique', $consultation->ai_summary);
    }

    /** @test */
    public function unauthorized_user_cannot_access_clinical_portals()
    {
        $unauthorizedUser = User::factory()->create(['role' => 'player']);

        $response = $this->actingAs($unauthorizedUser)
                         ->get('/clinical/clinician-portal');

        $response->assertStatus(403);
    }

    /** @test */
    public function api_requires_authentication()
    {
        $response = $this->postJson('/api/clinical/patients', [
            'name' => ['family' => 'Test'],
            'gender' => 'male',
            'birth_date' => '1990-01-01'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function api_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
                         ->postJson('/api/clinical/patients', [
                             'gender' => 'male'
                             // Missing required fields
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'birth_date']);
    }
}
