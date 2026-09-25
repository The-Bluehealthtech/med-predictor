<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferManagementController extends Controller
{
    public function index()
    {
        $this->authorizeManagement();

        $query = $this->scopedQuery();

        $stats = [
            'total_transfers' => (clone $query)->count(),
            'pending_transfers' => (clone $query)
                ->where('transfer_status', 'pending')->count(),
            'approved_transfers' => (clone $query)
                ->where('transfer_status', 'approved')->count(),
            'rejected_transfers' => (clone $query)
                ->where('transfer_status', 'rejected')->count(),
            'fifa_tms_synced' => (clone $query)
                ->whereNotNull('fifa_transfer_id')->count(),
            'local_transfers' => (clone $query)
                ->where('is_international', false)->count(),
        ];

        $fifaTmsStatus = $this->fifaTmsStatus();

        $transferTypes = [
            'domestic' => [
                'name' => 'Transferts Nationaux',
                'description' => 'Transferts entre clubs du même pays',
                'icon' => '🏠',
                'color' => 'blue',
            ],
            'international' => [
                'name' => 'Transferts Internationaux',
                'description' => 'Transferts entre clubs de pays différents',
                'icon' => '🌍',
                'color' => 'green',
            ],
            'loan' => [
                'name' => 'Prêts',
                'description' => 'Prêts de joueurs entre clubs',
                'icon' => '🔄',
                'color' => 'yellow',
            ],
            'free_transfer' => [
                'name' => 'Transferts Libres',
                'description' => 'Transferts sans frais de transfert',
                'icon' => '🆓',
                'color' => 'purple',
            ],
        ];

        return view('admin.transfer-management.index', compact(
            'stats',
            'fifaTmsStatus',
            'transferTypes'
        ));
    }

    public function transfers(Request $request)
    {
        $this->authorizeManagement();

        $query = $this->scopedQuery()->with([
            'player',
            'clubOrigin',
            'clubDestination',
        ]);

        $type = $request->query('type');

        if ($type === 'domestic') {
            $query->where('is_international', false)
                ->where('transfer_type', '!=', 'loan');
        } elseif ($type === 'international') {
            $query->where('is_international', true);
        } elseif ($type === 'loan') {
            $query->where('transfer_type', 'loan');
        } elseif ($type === 'free_transfer') {
            $query->where('transfer_type', 'free_agent');
        }

        $transfers = $query
            ->latest()
            ->get()
            ->map(fn (Transfer $transfer) => $this->presentTransfer($transfer));

        return view('admin.transfer-management.transfers', compact('transfers'));
    }

    public function syncFifaTms()
    {
        $this->authorizeManagement();

        if (!config('services.fifa_tms.api_key')) {
            return redirect()->route('admin.transfer-management.index')
                ->with('error', 'FIFA TMS non configuré : clé API absente.');
        }

        return redirect()->route('admin.transfer-management.index')
            ->with(
                'error',
                'Synchronisation FIFA TMS live reportée jusqu’à configuration et validation des clés.'
            );
    }

    public function approve(int $id)
    {
        $this->authorizeManagement();

        $transfer = $this->scopedQuery()->findOrFail($id);

        abort_unless(
            in_array(
                $transfer->transfer_status,
                ['pending', 'submitted', 'under_review'],
                true
            ),
            422
        );

        $transfer->update([
            'transfer_status' => 'approved',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.transfer-management.transfers')
            ->with('success', 'Transfert approuvé avec succès.');
    }

    public function reject(int $id)
    {
        $this->authorizeManagement();

        $transfer = $this->scopedQuery()->findOrFail($id);

        abort_unless(
            in_array(
                $transfer->transfer_status,
                ['pending', 'submitted', 'under_review'],
                true
            ),
            422
        );

        $transfer->update([
            'transfer_status' => 'rejected',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.transfer-management.transfers')
            ->with('success', 'Transfert rejeté.');
    }

    public function export(Request $request)
    {
        $this->authorizeManagement();

        $format = $request->query('format', 'csv');

        $transfers = $this->scopedQuery()
            ->with(['player', 'clubOrigin', 'clubDestination'])
            ->latest()
            ->get()
            ->map(fn (Transfer $transfer) => $this->presentTransfer($transfer));

        if ($format === 'json') {
            return response()->json($transfers, 200, [], JSON_PRETTY_PRINT);
        }

        abort_unless($format === 'csv', 422);

        $filename = 'transfers_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->stream(function () use ($transfers) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Joueur',
                'Club Source',
                'Club Destination',
                'Type',
                'Statut',
                'Frais',
                'FIFA TMS ID',
                'Date Création',
            ]);

            foreach ($transfers as $transfer) {
                fputcsv($file, [
                    $transfer['id'],
                    $transfer['player_name'],
                    $transfer['from_club'],
                    $transfer['to_club'],
                    $transfer['transfer_type'],
                    $transfer['status'],
                    $transfer['transfer_fee'],
                    $transfer['fifa_tms_id'],
                    $transfer['created_at']?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function authorizeManagement(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && (
                $user->isSystemAdmin()
                || $user->isClubUser()
                || $user->isAssociationUser()
            ),
            403
        );
    }

    private function scopedQuery(): Builder
    {
        $user = Auth::user();
        $query = Transfer::query();

        if ($user->isSystemAdmin()) {
            return $query;
        }

        if ($user->isClubUser()) {
            abort_unless($user->club_id, 403);

            return $query->where(function ($q) use ($user) {
                $q->where('club_origin_id', $user->club_id)
                    ->orWhere('club_destination_id', $user->club_id);
            });
        }

        abort_unless($user->isAssociationUser() && $user->association_id, 403);

        return $query->where(function ($q) use ($user) {
            $q->whereHas('clubOrigin', fn ($club) =>
                $club->where('association_id', $user->association_id)
            )->orWhereHas('clubDestination', fn ($club) =>
                $club->where('association_id', $user->association_id)
            );
        });
    }

    private function fifaTmsStatus(): array
    {
        if (!config('services.fifa_tms.api_key')) {
            return [
                'status' => 'unconfigured',
                'message' => 'Clé API FIFA TMS non configurée',
                'last_sync' => null,
            ];
        }

        return [
            'status' => 'configured',
            'message' => 'Configuration FIFA TMS présente ; connexion live non testée.',
            'last_sync' => null,
        ];
    }

    private function presentTransfer(Transfer $transfer): array
    {
        $type = match (true) {
            $transfer->transfer_type === 'loan' => 'loan',
            $transfer->transfer_type === 'free_agent' => 'free_transfer',
            $transfer->is_international => 'international',
            default => 'domestic',
        };

        return [
            'id' => $transfer->id,
            'player_name' => trim(
                ($transfer->player?->first_name ?? '')
                . ' '
                . ($transfer->player?->last_name ?? '')
            ) ?: ($transfer->player?->name ?? 'N/A'),
            'from_club' => $transfer->clubOrigin?->name ?? 'N/A',
            'to_club' => $transfer->clubDestination?->name ?? 'N/A',
            'transfer_type' => $type,
            'status' => $transfer->transfer_status,
            'transfer_fee' => $transfer->formatted_transfer_fee,
            'fifa_tms_id' => $transfer->fifa_transfer_id,
            'created_at' => $transfer->created_at,
        ];
    }
}
