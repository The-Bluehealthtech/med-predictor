<?php

namespace App\Http\Controllers\Passports;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\Passports\MedicalSummary;
use App\Services\Passports\PassportAccess;
use App\Services\Passports\TransferPassport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Module Passeports : passeport médical (résumé IPS HL7/IHE) et passeport de
 * transfert (format FIFA), en consultation et en PDF. Chaque consultation d'un
 * passeport médical est journalisée.
 */
class PassportsController extends Controller
{
    public function __construct(
        private readonly PassportAccess $access,
        private readonly MedicalSummary $medical,
        private readonly TransferPassport $transfer,
    ) {
    }

    public function medicalIndex(Request $request)
    {
        $user = $request->user();
        if ($user->isPlayer()) {
            abort_unless($user->player_id, 403);

            return redirect()->route('passports.medical.show', ['player' => $user->player_id, 'purpose' => 'player_share']);
        }
        abort_unless($user->hasAnyRole(['system_admin', 'association_medical', 'club_medical', 'doctor', 'team_doctor', 'medical_staff']), 403);

        return view('passports.index', ['kind' => 'medical'] + $this->search($request, $this->access->medicalPlayers($user)));
    }

    public function medicalShow(Request $request, int $player)
    {
        [$model, $purpose] = $this->medicalContext($request, $player);
        $summary = $this->medical->build($model, $purpose, $request->user()->name);
        $this->audit($request, $model, $purpose, 'view');

        return view('passports.medical.show', ['summary' => $summary, 'purposes' => MedicalSummary::PURPOSES, 'sections' => MedicalSummary::SECTIONS]);
    }

    public function medicalPdf(Request $request, int $player)
    {
        [$model, $purpose] = $this->medicalContext($request, $player);
        $summary = $this->medical->build($model, $purpose, $request->user()->name);
        $this->audit($request, $model, $purpose, 'pdf');

        return $this->pdf('passports.medical.pdf', ['summary' => $summary, 'sections' => MedicalSummary::SECTIONS],
            'passeport-medical-ips-' . $model->id . '.pdf');
    }

    public function transferIndex(Request $request)
    {
        $user = $request->user();
        if ($user->isPlayer()) {
            abort_unless($user->player_id, 403);

            return redirect()->route('passports.transfer.show', $user->player_id);
        }
        abort_unless($user->isSystemAdmin() || $user->isClubUser() || $user->isAssociationUser(), 403);

        return view('passports.index', ['kind' => 'transfer'] + $this->search($request, $this->access->transferPlayers($user)));
    }

    public function transferShow(Request $request, int $player)
    {
        $model = $this->player($player);
        abort_unless($this->access->canViewTransfer($request->user(), $model), 403);

        return view('passports.transfer.show', ['passport' => $this->transfer->build($model)]);
    }

    public function transferPdf(Request $request, int $player)
    {
        $model = $this->player($player);
        abort_unless($this->access->canViewTransfer($request->user(), $model), 403);

        return $this->pdf('passports.transfer.pdf', ['passport' => $this->transfer->build($model)], 'passeport-transfert-' . $model->id . '.pdf');
    }

    private function medicalContext(Request $request, int $player): array
    {
        $model = $this->player($player);
        abort_unless($this->access->canViewMedical($request->user(), $model), 403);
        $purpose = $request->validate(['purpose' => ['nullable', 'in:' . implode(',', array_keys(MedicalSummary::PURPOSES))]])['purpose']
            ?? ($request->user()->isPlayer() ? 'player_share' : 'general');

        return [$model, $purpose];
    }

    private function player(int $id): Player
    {
        return Player::withoutGlobalScopes()->with('club.association')->findOrFail($id);
    }

    private function search(Request $request, $query): array
    {
        $term = trim((string) $request->query('q', ''));
        if ($term !== '') {
            $query->where(fn ($q) => $q->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%"));
        }

        return ['players' => $query->with('club')->orderBy('last_name')->orderBy('first_name')->paginate(25)->withQueryString(), 'term' => $term];
    }

    private function pdf(string $view, array $data, string $filename)
    {
        $response = Pdf::loadView($view, $data)->setPaper('a4')->setOption('isRemoteEnabled', false)->download($filename);
        $response->headers->set('Cache-Control', 'private, no-store');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }

    private function audit(Request $request, Player $player, string $purpose, string $action): void
    {
        Log::info('passeport médical consulté', [
            'action' => $action, 'user_id' => $request->user()->id, 'role' => $request->user()->role,
            'player_id' => $player->id, 'purpose' => $purpose, 'ip' => $request->ip(),
        ]);
    }
}
