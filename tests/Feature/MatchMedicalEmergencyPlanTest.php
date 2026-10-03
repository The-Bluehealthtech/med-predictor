<?php

namespace Tests\Feature;

use App\Http\Controllers\MatchMedicalEmergencyPlanController;
use App\Http\Controllers\MatchMedicalIncidentController;
use App\Models\Association;
use App\Models\Club;
use App\Models\Competition;
use App\Models\MatchMedicalEmergencyPlan;
use App\Models\MatchModel;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MatchMedicalEmergencyPlanTest extends TestCase
{
    use DatabaseTransactions;

    private Association $association;
    private Club $homeClub;
    private Club $awayClub;
    private Club $otherClub;
    private MatchModel $match;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('match_medical_emergency_plans')) {
            (require base_path('database/migrations/2026_10_03_227400_create_match_medical_emergency_plans.php'))->up();
        }
        if (!Schema::hasTable('match_medical_incidents')) {
            (require base_path('database/migrations/2026_10_03_228000_create_match_medical_incidents_table.php'))->up();
        }
        if (!Schema::hasColumn('match_medical_incidents', 'protocol_code')) {
            (require base_path('database/migrations/2026_10_03_229000_add_emergency_protocol_to_match_medical_incidents.php'))->up();
        }
        if (!Schema::hasColumn('match_medical_emergency_plans', 'connect_match_fifa_id')) {
            (require base_path('database/migrations/2026_10_03_229100_add_connect_context_to_match_medical_emergency_plans.php'))->up();
        }
        if (!Schema::hasColumn('match_medical_emergency_plans', 'team_leader_person_fifa_id')) {
            (require base_path('database/migrations/2026_10_04_090000_add_team_leader_connect_id_to_match_medical_emergency_plans.php'))->up();
        }

        Route::middleware(['web'])->group(function () {
            Route::get('/_t/competition-management/matches', [\App\Http\Controllers\MatchdayPreparationController::class,'index'])->name('competition-management.matches.index');
            Route::get('/_t/matches/{match}/matchday-preparation', [\App\Http\Controllers\MatchdayPreparationController::class,'show'])->name('competition-management.matches.matchday-preparation');
            Route::get('/_t/matches/{match}/medical-emergency-plan', [MatchMedicalEmergencyPlanController::class,'show'])->name('matches.medical-emergency-plan');
            Route::put('/_t/matches/{match}/medical-emergency-plan', [MatchMedicalEmergencyPlanController::class,'update'])->name('matches.medical-emergency-plan.update');
            Route::post('/_t/matches/{match}/medical-emergency-plan/validate', [MatchMedicalEmergencyPlanController::class,'validatePlan'])->name('matches.medical-emergency-plan.validate');
            Route::post('/_t/matches/{match}/medical-emergency-plan/signatures', fn () => back())->name('matches.medical-emergency-plan.signatures.store');
            Route::post('/_t/matches/{match}/medical-emergency-plan/signatures/{signature}/sync', fn () => back())->name('matches.medical-emergency-plan.signatures.sync');
            Route::get('/_t/matches/{match}/medical-emergency-plan/signatures/{signature}/download', fn () => response('signed'))->name('matches.medical-emergency-plan.signatures.download');
            Route::post('/_t/matches/{match}/medical-incidents', [MatchMedicalIncidentController::class,'store'])->name('matches.medical-incidents.store');
            Route::get('/_t/match-sheet/{match}', fn () => response('sheet'))->name('competition-management.matches.match-sheet');
        });
        app('router')->getRoutes()->refreshNameLookups();

        $this->association = Association::factory()->create();
        $this->homeClub = Club::factory()->create(['association_id'=>$this->association->id]);
        $this->awayClub = Club::factory()->create(['association_id'=>$this->association->id]);
        $this->otherClub = Club::factory()->create();

        $homeTeam = Team::factory()->create(['club_id'=>$this->homeClub->id,'association_id'=>$this->association->id]);
        $awayTeam = Team::factory()->create(['club_id'=>$this->awayClub->id,'association_id'=>$this->association->id]);
        $competition = Competition::factory()->create(['association_id'=>$this->association->id]);

        $this->match = MatchModel::factory()->create([
            'competition_id'=>$competition->id,
            'home_team_id'=>$homeTeam->id,
            'away_team_id'=>$awayTeam->id,
            'home_club_id'=>$this->homeClub->id,
            'away_club_id'=>$this->awayClub->id,
            'stadium'=>'Stade Test',
        ]);
    }

    private function completePayload(): array
    {
        return [
            'stadium'=>'Stade Test',
            'nearest_hospital'=>'Hôpital Central',
            'nearest_hospital_phone'=>'123456',
            'ambulance_contact'=>'SAMU 15',
            'team_leader_name'=>'Dr Responsable',
            'team_leader_phone'=>'555111',
            'role_assignments'=>[
                'black'=>'Dr Noir','red'=>'Dr Rouge','orange'=>'Soignant Orange',
                'blue'=>'Soignant Bleu','green'=>'Soignant Vert','white'=>'Soignant Blanc',
            ],
            'equipment_checklist'=>[
                'evacuation_set_1'=>1,'evacuation_set_2'=>1,'aed_1'=>1,'aed_2'=>1,
                'oxygen_1'=>1,'oxygen_2'=>1,'splints'=>1,'emergency_bag'=>1,
            ],
            'timeline_checklist'=>[
                'h_minus_2_team_present'=>1,'h_minus_2_equipment_checked'=>1,
                'h_minus_90_simulation'=>1,'h_minus_60_infirmary_ready'=>1,
                'halftime_team_present'=>1,'post_match_until_last_player'=>1,
            ],
        ];
    }

    public function test_association_medical_can_prepare_and_validate_complete_plan(): void
    {
        $doctor = User::factory()->create([
            'role'=>'association_medical','association_id'=>$this->association->id,'status'=>'active','tenant_id'=>1,
        ]);

        $this->actingAs($doctor)->get('/_t/matches/'.$this->match->id.'/medical-emergency-plan')
            ->assertOk()->assertSee('Plan d’urgence médical')->assertSee('v3 - March 2025');

        $this->actingAs($doctor)->put('/_t/matches/'.$this->match->id.'/medical-emergency-plan',$this->completePayload())
            ->assertRedirect()->assertSessionHas('success');

        $plan = MatchMedicalEmergencyPlan::query()->where('match_id',$this->match->id)->firstOrFail();
        $this->assertSame('ready',$plan->status);

        $this->actingAs($doctor)->post('/_t/matches/'.$this->match->id.'/medical-emergency-plan/validate')
            ->assertRedirect()->assertSessionHas('success');

        $this->assertSame('validated',$plan->fresh()->status);

        $this->actingAs($doctor)
            ->get('/_t/matches/'.$this->match->id.'/medical-emergency-plan')
            ->assertOk()
            ->assertSee('Signature numérique du plan Medical Matchday')
            ->assertSee('Document éligible à la signature');
    }

    public function test_association_admin_can_prepare_but_cannot_medically_validate(): void
    {
        $admin = User::factory()->create([
            'role'=>'association_admin','association_id'=>$this->association->id,'status'=>'active','tenant_id'=>1,
        ]);

        $this->actingAs($admin)->put('/_t/matches/'.$this->match->id.'/medical-emergency-plan',$this->completePayload())
            ->assertRedirect();

        $this->actingAs($admin)->post('/_t/matches/'.$this->match->id.'/medical-emergency-plan/validate')
            ->assertForbidden();
    }

    public function test_participating_club_medical_can_view_but_not_edit(): void
    {
        $doctor = User::factory()->create([
            'role'=>'club_medical','club_id'=>$this->homeClub->id,'status'=>'active','tenant_id'=>1,
        ]);

        $this->actingAs($doctor)->get('/_t/matches/'.$this->match->id.'/medical-emergency-plan')->assertOk();
        $this->actingAs($doctor)->put('/_t/matches/'.$this->match->id.'/medical-emergency-plan',$this->completePayload())->assertForbidden();
    }

    public function test_unrelated_club_medical_cannot_view_plan(): void
    {
        $doctor = User::factory()->create([
            'role'=>'club_medical','club_id'=>$this->otherClub->id,'status'=>'active','tenant_id'=>1,
        ]);

        $this->actingAs($doctor)->get('/_t/matches/'.$this->match->id.'/medical-emergency-plan')->assertForbidden();
    }

    public function test_incomplete_plan_cannot_be_validated(): void
    {
        $doctor = User::factory()->create([
            'role'=>'association_medical','association_id'=>$this->association->id,'status'=>'active','tenant_id'=>1,
        ]);
        $payload=$this->completePayload();
        $payload['equipment_checklist']['aed_2']=0;

        $this->actingAs($doctor)->put('/_t/matches/'.$this->match->id.'/medical-emergency-plan',$payload)->assertRedirect();
        $plan=MatchMedicalEmergencyPlan::query()->where('match_id',$this->match->id)->firstOrFail();
        $this->assertSame('draft',$plan->status);

        $this->actingAs($doctor)->post('/_t/matches/'.$this->match->id.'/medical-emergency-plan/validate')->assertStatus(422);
    }
    public function test_cardiac_incident_activates_versioned_fifa_protocol(): void
    {
        $doctor = User::factory()->create([
            'role'=>'association_medical','association_id'=>$this->association->id,'status'=>'active','tenant_id'=>1,
        ]);

        $this->actingAs($doctor)->post('/_t/matches/'.$this->match->id.'/medical-incidents', [
            'incident_type'=>'cardiac_arrest',
            'doctor_name'=>'Dr Terrain',
            'protocol_actions'=>[
                'sca_responsiveness_breathing'=>1,
                'sca_cpr'=>1,
                'sca_aed'=>1,
            ],
        ])->assertRedirect()->assertSessionHas('success');

        $incident=\App\Models\MatchMedicalIncident::query()->where('match_id',$this->match->id)->latest('id')->firstOrFail();
        $this->assertSame('FIFA_SCA',$incident->protocol_code);
        $this->assertSame('FIFA Emergency Care Protocols v3 - March 2025',$incident->protocol_version);
        $this->assertTrue($incident->protocol_actions['sca_cpr']);
        $this->assertNotNull($incident->protocol_activated_at);
    }

    public function test_head_or_cervical_incident_activates_fifa_head_protocol(): void
    {
        $doctor = User::factory()->create([
            'role'=>'club_medical','club_id'=>$this->homeClub->id,'status'=>'active','tenant_id'=>1,
        ]);

        $this->actingAs($doctor)->post('/_t/matches/'.$this->match->id.'/medical-incidents', [
            'incident_type'=>'concussion',
            'doctor_name'=>'Dr Club',
            'protocol_actions'=>['head_cervical_control'=>1,'head_neuro'=>1],
        ])->assertRedirect();

        $incident=\App\Models\MatchMedicalIncident::query()->where('match_id',$this->match->id)->latest('id')->firstOrFail();
        $this->assertSame('FIFA_HEAD_CERVICAL',$incident->protocol_code);
        $this->assertTrue($incident->protocol_actions['head_neuro']);
    }

    public function test_association_user_can_open_matchday_preparation_cockpit(): void
    {
        $doctor = User::factory()->create([
            'role'=>'association_medical','association_id'=>$this->association->id,'status'=>'active','tenant_id'=>1,
        ]);

        $this->actingAs($doctor)
            ->get('/_t/matches/'.$this->match->id.'/matchday-preparation')
            ->assertOk()
            ->assertSee('Préparation Match Day')
            ->assertSee('Medical Matchday')
            ->assertSee('Feuille de match');
    }

    public function test_unrelated_club_cannot_open_matchday_preparation_cockpit(): void
    {
        $doctor = User::factory()->create([
            'role'=>'club_medical','club_id'=>$this->otherClub->id,'status'=>'active','tenant_id'=>1,
        ]);

        $this->actingAs($doctor)
            ->get('/_t/matches/'.$this->match->id.'/matchday-preparation')
            ->assertForbidden();
    }

    public function test_admin_sees_match_sheet_selector_before_opening_matchday_cockpit(): void
    {
        $admin = User::factory()->create([
            'role'=>'admin','status'=>'active','tenant_id'=>1,
        ]);
        \App\Models\MatchSheet::factory()->create([
            'match_id'=>$this->match->id,
            'status'=>'draft',
            'match_number'=>'FDM-001',
        ]);

        $this->actingAs($admin)
            ->get('/_t/competition-management/matches')
            ->assertOk()
            ->assertSee('Choisissez la feuille de match')
            ->assertSee('FDM #')
            ->assertSee('Ouvrir le cockpit Match Day');
    }


    public function test_matchday_plan_uses_declared_club_officials_with_connect_role_ids(): void
    {
        if (!Schema::hasTable('fifa_connect_matches') || !Schema::hasTable('club_officials')) {
            $this->markTestSkipped('FIFA Connect / club officials tables unavailable.');
        }

        $admin = User::factory()->create(['role'=>'admin','status'=>'active','tenant_id'=>1]);
        \App\Models\FifaConnect\MatchRecord::query()->create([
            'match_fifa_id'=>'MATCH-FIFA-'.$this->match->id,
            'status'=>'scheduled',
            'competition_fifa_id'=>'COMP-FIFA-TEST',
            'match_id'=>$this->match->id,
        ]);

        $official = new \App\Models\ClubOfficial;
        $official->club_id = $this->homeClub->id;
        $official->created_by = $admin->id;
        $official->fill([
            'person_fifa_id'=>'PERSON-FIFA-MED-1',
            'international_first_name'=>'Amina',
            'international_last_name'=>'Doctor',
            'gender'=>'female',
            'date_of_birth'=>'1980-01-01',
            'nationality'=>'FR',
            'registration_type'=>\App\Models\ClubOfficial::TEAM_OFFICIAL,
            'team_official_role'=>'TeamDoctor',
            'role_description'=>'Doctor',
            'status'=>'active',
            'discipline'=>'Football',
            'registration_valid_from'=>today(),
            'source'=>'FIFAConnect',
        ]);
        $official->save();

        $this->actingAs($admin)
            ->get('/_t/matches/'.$this->match->id.'/medical-emergency-plan')
            ->assertOk()
            ->assertSee('MATCH-FIFA-'.$this->match->id)
            ->assertSee('PERSON-FIFA-MED-1')
            ->assertSee('Amina Doctor')
            ->assertSee('PERSON-FIFA-MED-1');

        $payload=$this->completePayload();
        $payload['connect_role_assignments']=['black'=>'PERSON-FIFA-MED-1'];
        $this->actingAs($admin)
            ->put('/_t/matches/'.$this->match->id.'/medical-emergency-plan',$payload)
            ->assertRedirect();

        $plan=MatchMedicalEmergencyPlan::query()->where('match_id',$this->match->id)->firstOrFail();
        $this->assertSame('MATCH-FIFA-'.$this->match->id,$plan->connect_match_fifa_id);
        $this->assertSame('PERSON-FIFA-MED-1',$plan->connect_role_assignments['black']);
    }



    public function test_contacts_and_evacuation_use_identified_club_responsible(): void
    {
        $admin = User::factory()->create(['role'=>'admin','status'=>'active','tenant_id'=>1]);
        $official = new \App\Models\ClubOfficial;
        $official->club_id = $this->homeClub->id;
        $official->created_by = $admin->id;
        $official->fill([
            'person_fifa_id'=>'MED-LEADER-1',
            'international_first_name'=>'Nadia',
            'international_last_name'=>'Urgence',
            'gender'=>'female',
            'date_of_birth'=>'1981-02-03',
            'nationality'=>'FR',
            'registration_type'=>\App\Models\ClubOfficial::TEAM_OFFICIAL,
            'team_official_role'=>'TeamDoctor',
            'role_description'=>'TeamDoctor',
            'phone'=>'+687 12 34 56',
            'status'=>'active',
            'discipline'=>'Football',
            'registration_valid_from'=>today(),
            'source'=>'FIFAConnect',
        ]);
        $official->save();

        $this->actingAs($admin)
            ->get('/_t/matches/'.$this->match->id.'/medical-emergency-plan')
            ->assertOk()
            ->assertSee('Nadia Urgence')
            ->assertSee('MED-LEADER-1')
            ->assertSee('+687 12 34 56');

        $payload=$this->completePayload();
        $payload['team_leader_person_fifa_id']='MED-LEADER-1';
        $payload['team_leader_name']='Valeur remplacée';
        $payload['team_leader_phone']='Valeur remplacée';
        $this->actingAs($admin)
            ->put('/_t/matches/'.$this->match->id.'/medical-emergency-plan',$payload)
            ->assertRedirect();

        $plan=MatchMedicalEmergencyPlan::query()->where('match_id',$this->match->id)->firstOrFail();
        $this->assertSame('MED-LEADER-1',$plan->team_leader_person_fifa_id);
        $this->assertSame('Nadia Urgence',$plan->team_leader_name);
        $this->assertSame('+687 12 34 56',$plan->team_leader_phone);
    }

    public function test_player_selector_uses_fdm_identity_and_allows_multiple_incidents_for_same_player(): void
    {
        $player = \App\Models\Player::factory()->create([
            'first_name'=>'Karim',
            'last_name'=>'Testeur',
            'club_id'=>$this->homeClub->id,
        ]);
        if (Schema::hasTable('game_matches')) {
            \DB::table('game_matches')->updateOrInsert(['id'=>$this->match->id],[
                'competition_id'=>$this->match->competition_id,
                'home_team_id'=>$this->match->home_team_id,
                'away_team_id'=>$this->match->away_team_id,
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);
        }
        $roster = \App\Models\MatchRoster::query()->create([
            'match_id'=>$this->match->id,
            'team_id'=>$this->match->home_team_id,
        ]);
        \App\Models\MatchRosterPlayer::query()->create([
            'match_roster_id'=>$roster->id,
            'player_id'=>$player->id,
            'position'=>'forward',
            'is_starter'=>true,
            'jersey_number'=>9,
        ]);
        $admin = User::factory()->create(['role'=>'admin','status'=>'active','tenant_id'=>1]);

        $this->actingAs($admin)
            ->get('/_t/matches/'.$this->match->id.'/medical-emergency-plan')
            ->assertOk()
            ->assertSee('#9')
            ->assertSee('Karim Testeur')
            ->assertSee($this->homeClub->name);

        foreach ([22, 71] as $minute) {
            $this->actingAs($admin)
                ->post('/_t/matches/'.$this->match->id.'/medical-incidents',[
                    'incident_type'=>'other',
                    'player_id'=>$player->id,
                    'match_minute'=>$minute,
                    'mechanism'=>'direct_blow',
                    'doctor_name'=>'Dr Terrain',
                ])
                ->assertRedirect();
        }

        $this->assertSame(2, \App\Models\MatchMedicalIncident::query()
            ->where('match_id',$this->match->id)
            ->where('player_id',$player->id)
            ->count());

        $this->actingAs($admin)
            ->get('/_t/matches/'.$this->match->id.'/medical-emergency-plan')
            ->assertOk()
            ->assertSee('Historique des incidents (2)')
            ->assertSee('22e min')
            ->assertSee('71e min')
            ->assertSee('#9')
            ->assertSee('Karim Testeur')
            ->assertSee($this->homeClub->name);
    }

    public function test_admin_can_collect_and_persist_matchday_medical_data(): void
    {
        $admin = User::factory()->create([
            'role'=>'admin','status'=>'active','tenant_id'=>1,
        ]);

        $this->actingAs($admin)
            ->get('/_t/matches/'.$this->match->id.'/medical-emergency-plan')
            ->assertOk()
            ->assertSee('Enregistrer le plan')
            ->assertSee('Enregistrer l’incident');

        $this->actingAs($admin)
            ->put('/_t/matches/'.$this->match->id.'/medical-emergency-plan',$this->completePayload())
            ->assertRedirect()
            ->assertSessionHas('success');

        $plan = MatchMedicalEmergencyPlan::query()->where('match_id',$this->match->id)->firstOrFail();
        $this->assertSame('Hôpital Central',$plan->nearest_hospital);
        $this->assertSame('ready',$plan->status);
        $this->assertSame($admin->id,$plan->prepared_by);

        $this->actingAs($admin)
            ->post('/_t/matches/'.$this->match->id.'/medical-incidents',[
                'incident_type'=>'cardiac_arrest',
                'doctor_name'=>'Dr Admin Test',
                'match_minute'=>12,
                'protocol_actions'=>['sca_cpr'=>1,'sca_aed'=>1],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $incident=\App\Models\MatchMedicalIncident::query()->where('match_id',$this->match->id)->latest('id')->firstOrFail();
        $this->assertSame('FIFA_SCA',$incident->protocol_code);
        $this->assertSame(12,$incident->match_minute);

        $this->actingAs($admin)
            ->post('/_t/matches/'.$this->match->id.'/medical-emergency-plan/validate')
            ->assertForbidden();
    }

}
