<?php

namespace App\Http\Controllers;

use App\Services\Audit\Auditor;
use App\Services\Medical\PcmaDictationParser;
use App\Services\MedicalRecordAccess;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Dictée du PCMA : l'audio enregistré dans le navigateur est transcrit par Google Cloud
 * Speech-to-Text depuis le serveur (la clé n'est jamais transmise au navigateur), puis analysé
 * en propositions de champs que le médecin accepte une à une. Ni l'audio ni le texte ne sont
 * conservés ou journalisés ; seul l'usage est tracé.
 */
class PcmaDictationController extends Controller
{
    public function transcribe(Request $request, PcmaDictationParser $parser, Auditor $auditor): JsonResponse
    {
        app(MedicalRecordAccess::class)->authorizeRole($request->user());
        $key = (string) config('services.google_speech.key');
        if ($key === '') {
            return response()->json(['success' => false, 'message' => 'Transcription indisponible : clé Google Speech-to-Text non configurée sur le serveur (GOOGLE_SPEECH_API_KEY).'], 503);
        }
        $request->validate(['audio' => 'required|file|max:9500|mimetypes:audio/webm,video/webm,audio/ogg,application/octet-stream']);
        $audio = (string) file_get_contents($request->file('audio')->getRealPath());

        try {
            $response = Http::timeout((int) config('services.google_speech.timeout', 30))->withOptions(['allow_redirects' => false])
                ->post('https://speech.googleapis.com/v1/speech:recognize?key=' . rawurlencode($key), [
                    'config' => [
                        'encoding' => 'WEBM_OPUS', 'sampleRateHertz' => 48000, 'languageCode' => config('services.google_speech.language', 'fr-FR'),
                        'model' => config('services.google_speech.model', 'latest_long'), 'enableAutomaticPunctuation' => true,
                    ],
                    'audio' => ['content' => base64_encode($audio)],
                ]);
        } catch (ConnectionException) {
            return response()->json(['success' => false, 'message' => 'Service de transcription injoignable.'], 502);
        }
        if (!$response->successful()) {
            return response()->json(['success' => false, 'message' => 'Transcription refusée par Google (HTTP ' . $response->status() . ').'], 502);
        }
        $alternatives = collect($response->json('results', []))->map(fn ($r) => $r['alternatives'][0] ?? null)->filter();
        $text = trim($alternatives->pluck('transcript')->implode(' '));
        if ($text === '') {
            return response()->json(['success' => false, 'message' => 'Aucune parole détectée : parlez plus près du micro.'], 422);
        }
        $auditor->record(['event_type' => 'data_access', 'module' => 'pcma', 'action' => 'pcma_dictation_transcribe',
            'description' => 'Dictée du PCMA transcrite (Google Speech-to-Text)', 'metadata' => ['seconds' => null, 'characters' => mb_strlen($text)]]);

        return response()->json(['success' => true, 'text' => $text, 'confidence' => (float) ($alternatives->avg('confidence') ?? 0), 'proposals' => $parser->parse($text)]);
    }

    /** Analyse d'un texte saisi ou corrigé par le médecin. */
    public function parse(Request $request, PcmaDictationParser $parser): JsonResponse
    {
        app(MedicalRecordAccess::class)->authorizeRole($request->user());
        $data = $request->validate(['text' => 'required|string|max:5000']);

        return response()->json(['success' => true, 'proposals' => $parser->parse($data['text'])]);
    }
}
