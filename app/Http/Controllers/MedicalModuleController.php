<?php
namespace App\Http\Controllers;

use App\Models\{Player, MedicalPrediction, PCMA};
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
        $search = trim((string) $request->query('q', ''));

        $query = $this->players()
            ->with(['club', 'latestHealthRecord'])
            ->withCount('healthRecords');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $players = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        $records = app(HealthcareController::class)->query();

        $todayVisits = (clone $records)
            ->whereDate('visit_date', now()->toDateString())
            ->count();

        $followUpsDue = (clone $records)
            ->whereNotNull('next_checkup_date')
            ->whereDate('next_checkup_date', '<=', now()->toDateString())
            ->count();

        $patientsFollowed = (clone $records)
            ->distinct('player_id')
            ->count('player_id');

        return view('modules.medical.index', compact(
            'players',
            'search',
            'todayVisits',
            'followUpsDue',
            'patientsFollowed'
        ));
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
