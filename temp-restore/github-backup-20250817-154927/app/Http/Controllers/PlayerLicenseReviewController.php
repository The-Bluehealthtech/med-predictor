<?php

namespace App\Http\Controllers;

use App\Models\PlayerLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlayerLicenseReviewController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'system_admin') {
            $licenses = PlayerLicense::whereIn('status', ['pending', 'justification_requested'])->with(['player', 'club'])->paginate(20);
        } else {
            $licenses = PlayerLicense::whereHas('club', function($q) use ($user) {
                $q->where('association_id', $user->association_id);
            })->whereIn('status', ['pending', 'justification_requested'])->with(['player', 'club'])->paginate(20);
        }
        return view('player-licenses.index', compact('licenses'));
    }

    public function show(PlayerLicense $license)
    {
        $license->load(['player', 'club', 'player.documents']);
        // Check if analysis exists, otherwise run AI check (stub)
        $analysis = $license->fraudAnalysis;
        if (!$analysis) {
            // Call AI microservice (replace with real API call)
            $aiResult = $this->runFraudAnalysis($license);
            $analysis = $license->fraudAnalysis()->create($aiResult);
        }
        return view('player-licenses.show', compact('license', 'analysis'));
    }

    protected function runFraudAnalysis(PlayerLicense $license)
    {
        $player = $license->player;
        $documents = $player->documents;

        $payload = [
            'player' => [
                'id' => $player->id,
                'fifa_connect_id' => $player->fifa_connect_id,
                'full_name' => $player->full_name,
                'date_of_birth' => $player->date_of_birth?->format('Y-m-d'),
                'club_id' => $player->club_id,
            ],
            'documents' => $documents->map(function($doc) {
                return [
                    'type' => $doc->document_type,
                    'url' => asset('storage/' . $doc->file_path),
                    'file_name' => $doc->file_name,
                    'mime_type' => $doc->mime_type,
                ];
            })->toArray(),
        ];

        try {
            $response = Http::timeout(30)->post('http://ai-service.local/api/fraud-check', $payload);
            if ($response->successful()) {
                $result = $response->json();
                // Store the full response in anomalies for audit
                $result['anomalies'] = $result['anomalies'] ?? [];
                $result['ai_raw_response'] = $response->body();
                return $result;
            } else {
                Log::error('AI service error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('AI service exception: ' . $e->getMessage());
        }
        // Fallback if AI service fails
        return [
            'identity_score' => null,
            'age_score' => null,
            'anomalies' => [['type' => 'system', 'description' => 'AI service unavailable', 'score' => 100]],
            'status' => 'pending',
            'ai_raw_response' => null,
        ];
    }

    public function approve(Request $request, PlayerLicense $license)
    {
        $user = Auth::user();
        if ($user->role !== 'system_admin' && !in_array($license->status, ['pending', 'justification_requested'])) {
            return redirect()->route('player-licenses.index')->with('error', 'Only pending or explanation requested licenses can be approved.');
        }
        $license->approve($user->id);
        return redirect()->route('player-licenses.index')->with('success', 'License approved.');
    }

    public function reject(Request $request, PlayerLicense $license)
    {
        $user = Auth::user();
        $request->validate(['reason' => 'required|string|max:255']);
        if ($user->role !== 'system_admin' && !in_array($license->status, ['pending', 'justification_requested'])) {
            return redirect()->route('player-licenses.index')->with('error', 'Only pending or explanation requested licenses can be rejected.');
        }
        $license->reject($request->reason, $user->id);
        return redirect()->route('player-licenses.index')->with('success', 'License rejected.');
    }

    public function requestExplanation(Request $request, PlayerLicense $license)
    {
        $request->validate(['explanation_request' => 'required|string|max:500']);
        $license->requestExplanation($request->explanation_request, Auth::id());
        return redirect()->route('player-licenses.index')->with('success', 'Explanation requested from club.');
    }

    public function reanalyze(PlayerLicense $license)
    {
        // Delete old analysis if exists
        $license->fraudAnalysis()?->delete();
        // Run new analysis
        $aiResult = $this->runFraudAnalysis($license);
        $license->fraudAnalysis()->create($aiResult);
        return redirect()->route('player-licenses.show', $license->id)->with('success', 'AI fraud analysis re-run successfully.');
    }
} 