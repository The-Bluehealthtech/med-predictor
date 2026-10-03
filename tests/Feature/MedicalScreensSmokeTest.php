<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Module médical : chaque page GET (dossier, modules cliniques, PCMA, secrétariat, passeport,
 * imagerie, données des établissements) s'ouvre sans erreur serveur pour un médecin du club,
 * avec des données réalistes. Écran exclu : /medical-tabs (formulaire à onglets historique).
 */
class MedicalScreensSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_every_medical_page_opens_without_server_error(): void
    {
        Route::middleware('web')->group(base_path('routes/web.php'));
        Route::middleware('web')->group(base_path('routes/auth.php'));
        app('router')->getRoutes()->refreshNameLookups();
        app('router')->getRoutes()->refreshActionLookups();

        $now = now();
        $assoc = DB::table('associations')->insertGetId(['name' => 'Fédération Audit', 'country' => 'Tunisie', 'created_at' => $now, 'updated_at' => $now]);
        $club = DB::table('clubs')->insertGetId(['name' => 'Club Audit', 'association_id' => $assoc, 'created_at' => $now, 'updated_at' => $now]);
        $team = DB::table('teams')->insertGetId(['name' => 'Équipe Audit', 'club_id' => $club, 'created_at' => $now, 'updated_at' => $now]);
        $player = DB::table('players')->insertGetId(['name' => 'Samir Audit', 'first_name' => 'Samir', 'last_name' => 'Audit', 'gender' => 'male', 'date_of_birth' => '2000-05-14',
            'nationality' => 'Tunisie', 'club_id' => $club, 'association_id' => $assoc, 'created_at' => $now, 'updated_at' => $now]);
        $athlete = DB::table('athletes')->insertGetId(['name' => 'Samir Audit', 'dob' => '2000-05-14', 'nationality' => 'TN', 'team_id' => $team, 'player_id' => $player, 'created_at' => $now, 'updated_at' => $now]);
        $doctor = User::factory()->create(['role' => 'club_medical', 'club_id' => $club, 'association_id' => $assoc, 'status' => 'active', 'tenant_id' => 1]);
        $hr = DB::table('health_records')->insertGetId(['user_id' => $doctor->id, 'player_id' => $player, 'record_date' => $now, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);
        $pcma = DB::table('pcmas')->insertGetId(['athlete_id' => $athlete, 'player_id' => $player, 'assessor_id' => $doctor->id, 'type' => 'pcma', 'status' => 'completed',
            'result_json' => '{}', 'final_statement' => json_encode(['overall_decision' => 'FIT']), 'assessment_date' => $now->toDateString(), 'created_at' => $now, 'updated_at' => $now]);
        $appointment = DB::table('appointments')->insertGetId(['athlete_id' => $athlete, 'created_by' => $doctor->id, 'doctor_id' => $doctor->id, 'appointment_date' => $now,
            'appointment_type' => 'consultation', 'status' => 'Enregistré', 'created_at' => $now, 'updated_at' => $now]);
        $visit = DB::table('visits')->insertGetId(['athlete_id' => $athlete, 'appointment_id' => $appointment, 'doctor_id' => $doctor->id, 'visit_date' => $now, 'visit_type' => 'consultation',
            'status' => 'Enregistré', 'created_at' => $now, 'updated_at' => $now]);
        $ids = ['healthRecord' => $hr, 'health_record' => $hr, 'record' => $hr, 'pcma' => $pcma, 'player' => $player, 'appointment' => $appointment, 'visit' => $visit, 'athlete' => $athlete];
        foreach ([
            'aut' => fn () => DB::table('tue_requests')->insertGetId(['player_id' => $player, 'health_record_id' => $hr, 'physician_id' => $doctor->id, 'request_date' => $now->toDateString(), 'status' => 'pending', 'created_at' => $now, 'updated_at' => $now]),
            'immunisation' => fn () => DB::table('immunisations')->insertGetId(['athlete_id' => $athlete, 'vaccine_code' => '03', 'vaccine_name' => 'ROR', 'date_administered' => $now, 'created_at' => $now, 'updated_at' => $now]),
            'study' => fn () => DB::table('fit_imaging_studies')->insertGetId(['health_record_id' => $hr, 'player_id' => $player, 'exam_date' => $now->toDateString(), 'modality' => 'MR', 'body_region' => 'Genou', 'source' => 'upload', 'study_uid' => '2.25.1', 'created_by' => $doctor->id, 'created_at' => $now, 'updated_at' => $now]),
            'injury' => fn () => DB::table('injuries')->insertGetId(['athlete_id' => $athlete, 'date' => $now->toDateString(), 'type' => 'Entorse', 'body_zone' => 'Cheville', 'description' => 'Entorse', 'diagnosed_by' => $doctor->id, 'created_at' => $now, 'updated_at' => $now]),
        ] as $key => $make) {
            $ids[$key] = $make();
        }
        $ids['tue'] = $ids['aut'] ?? null;

        $this->actingAs($doctor);
        $prefix = '#^(health-records|healthcare|medical|pcma|clinical|secretary|passports/medical|modules/medical|modules/healthcare|medical-files|injuries|immunisations|athletes/\{athlete\}/(medical|immun))#';
        $results = [];
        foreach (app('router')->getRoutes() as $route) {
            if (!in_array('GET', $route->methods(), true) || !preg_match($prefix, $route->uri())) {
                continue;
            }
            $uri = $route->uri();
            $missing = false;
            $path = preg_replace_callback('/\{(\w+)\??\}/', function ($m) use ($ids, &$missing) {
                if (!isset($ids[$m[1]])) { $missing = true; return 'x'; }
                return (string) $ids[$m[1]];
            }, $uri);
            if ($missing) {
                $results[] = ['SKIP', $uri, ''];
                continue;
            }
            try {
                $response = $this->get('/' . $path);
                $status = $response->baseResponse->getStatusCode();
                $ex = $response->baseResponse->exception ?? null;
                $results[] = [$status, $uri, $ex ? get_class($ex) . ': ' . mb_substr($ex->getMessage(), 0, 160) : ($response->headers->get('Location') ?? '')];
            } catch (\Throwable $e) {
                $results[] = ['EXC', $uri, mb_substr($e->getMessage(), 0, 160)];
            }
        }
        foreach (array_keys(app(\App\Services\HealthRecordSections::class)->definitions()) as $module) {
            $response = $this->get("/health-records/{$hr}/modules/{$module}");
            $ex = $response->baseResponse->exception ?? null;
            $results[] = [$response->status(), "module:{$module}", $ex ? get_class($ex) . ': ' . mb_substr($ex->getMessage(), 0, 160) : ''];
        }
        foreach (['/modules/medical/athlete/' . $athlete, '/modules/medical/athlete/' . $athlete . '/edit', "/health-records/{$hr}/modules/imaging?legacy=1"] as $extra) {
            $response = $this->get($extra);
            $ex = $response->baseResponse->exception ?? null;
            $results[] = [$response->status(), $extra, $ex ? get_class($ex) . ': ' . mb_substr($ex->getMessage(), 0, 160) : ''];
        }
        $failures = collect($results)->filter(fn ($r) => ($r[0] === 'EXC' || (is_int($r[0]) && $r[0] >= 500)) && $r[1] !== 'medical-tabs')
            ->map(fn ($r) => $r[0] . ' ' . $r[1] . ' — ' . $r[2])->values()->all();
        $this->assertSame([], $failures);
        $this->assertGreaterThan(40, collect($results)->where(0, 200)->count(), 'pages médicales effectivement ouvertes');
    }
}
