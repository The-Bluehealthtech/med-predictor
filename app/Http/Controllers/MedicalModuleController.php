<?php
namespace App\Http\Controllers;

use App\Models\{Player, MedicalPrediction, PCMA, Appointment};
use App\Services\MedicalRecordAccess;
use Illuminate\Http\Request;

final class MedicalModuleController extends Controller
{
    // Le même périmètre s'applique aux listes, profils et actions.
    private function players()
    {
        return app(MedicalRecordAccess::class)->scopePlayers(auth()->user(), Player::query());
    }
    public function index(Request $request)
    {
        $allowedPlayerIds = $this->players()->select('players.id');

        $waitingAppointments = Appointment::with([
                'athlete.player.club',
                'athlete.player.baseHealthRecord',
                'visit.documents',
                'doctor',
            ])
            ->whereHas('athlete', fn ($athlete) => $athlete->whereIn('player_id', $allowedPlayerIds))
            ->where('status', 'Enregistré')
            ->orderBy('appointment_date')
            ->get()
            ->filter(fn ($appointment) => $appointment->athlete?->player !== null)
            ->values();

        return view('modules.medical.index', compact('waitingAppointments'));
    }

    public function show($id)
    {
        $player=$this->players()->with('club')->findOrFail($id);
        $records=app(HealthcareController::class)->query()->where('player_id',$player->id)
            ->orderByDesc('record_date')->orderByDesc('id')->get();
        return view('modules.medical.athlete',compact('player','records'));
    }
    public function edit($id)
    {
        $player=$this->players()->findOrFail($id);
        $record=app(HealthcareController::class)->query()->where('player_id',$player->id)
            ->orderByDesc('record_date')->orderByDesc('id')->first();
        return $record ? redirect()->route('health-records.edit',$record)
            : redirect()->route('health-records.create',['player_id'=>$player->id]);
    }
    public function prediction($prediction)
    {
        $ids=$this->players()->select('players.id');
        $item=MedicalPrediction::whereIn('player_id',$ids)->findOrFail($prediction);
        return view('modules.medical.prediction',compact('item'));
    }
    public function unavailable(Request $request, $prediction=null)
    {
        $this->players();
        if($prediction!==null) {
            MedicalPrediction::whereIn('player_id',$this->players()->select('players.id'))->findOrFail($prediction);
        }
        if($request->filled('player_id')) $this->players()->findOrFail($request->input('player_id'));
        if(!$request->isMethod('get')) abort(503,__('healthcare_repair.unvalidated'));
        return view('modules.medical.unavailable');
    }
}
