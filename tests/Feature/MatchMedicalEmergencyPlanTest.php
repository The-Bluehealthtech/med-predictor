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

        Route::middleware(['web'])->group(function () {
            Route::get('/_t/matches/{match}/medical-emergency-plan', [MatchMedicalEmergencyPlanController::class,'show'])->name('matches.medical-emergency-plan');
            Route::put('/_t/matches/{match}/medical-emergency-plan', [MatchMedicalEmergencyPlanController::class,'update'])->name('matches.medical-emergency-plan.update');
            Route::post('/_t/matches/{match}/medical-emergency-plan/validate', [MatchMedicalEmergencyPlanController::class,'validatePlan'])->name('matches.medical-emergency-plan.validate');
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
}
