<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GoogleAssistantAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $this->verifyWebhookTransport($request);

            if (!$this->isGoogleAssistantRequest($request)) {
                throw new \RuntimeException('Requête Google Assistant non reconnue.');
            }

            $this->authenticateGoogleAssistantUser($request);

            return $next($request);
        } catch (\Throwable $e) {
            Log::warning('Google Assistant webhook authentication rejected', [
                'reason' => $e->getMessage(),
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'fulfillmentText' => 'Erreur d\'authentification. Veuillez vous connecter via l\'interface web.',
                'fulfillmentMessages' => [
                    [
                        'text' => [
                            'text' => ['Erreur d\'authentification. Veuillez vous connecter via l\'interface web.']
                        ]
                    ]
                ]
            ], 401);
        }
    }

    private function verifyWebhookTransport(Request $request): void
    {
        if (!config('google-assistant.auth.enabled', true)) {
            return;
        }

        $secret = (string) config('google-assistant.webhook.secret', '');

        // Local/test environments may exercise the integration without a
        // production webhook secret. Production fails closed.
        if ($secret === '') {
            if (app()->environment(['local', 'testing'])) {
                return;
            }

            throw new \RuntimeException('Webhook secret non configuré.');
        }

        $provided = (string) $request->header('X-FIT-Webhook-Secret', '');
        if ($provided === '') {
            $authorization = (string) $request->header('Authorization', '');
            if (str_starts_with($authorization, 'Bearer ')) {
                $provided = substr($authorization, 7);
            }
        }

        if ($provided === '' || !hash_equals($secret, $provided)) {
            throw new \RuntimeException('Secret webhook invalide.');
        }
    }

    /**
     * Vérifie si la requête provient de Google Assistant
     */
    private function isGoogleAssistantRequest(Request $request): bool
    {
        $userAgent = $request->header('User-Agent', '');
        $origin = $request->header('Origin', '');
        
        // Vérifier les indicateurs Google Assistant
        $isGoogleAssistant = 
            str_contains($userAgent, 'Google-Assistant') ||
            str_contains($userAgent, 'GoogleHome') ||
            str_contains($origin, 'google.com') ||
            $request->has('queryResult') ||
            $request->has('originalDetectIntentRequest') ||
            // Mode développement : accepter les requêtes avec X-User-ID
            (config('app.env') === 'local' && $request->header('X-User-ID'));
        
        Log::info('Vérification Google Assistant', [
            'user_agent' => $userAgent,
            'origin' => $origin,
            'has_query_result' => $request->has('queryResult'),
            'is_google_assistant' => $isGoogleAssistant,
            'env' => config('app.env'),
            'has_user_id_header' => $request->header('X-User-ID')
        ]);
        
        return $isGoogleAssistant;
    }

    /**
     * Authentifie l'utilisateur via Google Assistant
     */
    private function authenticateGoogleAssistantUser(Request $request): void
    {
        // Récupérer l'ID utilisateur depuis les paramètres Google Assistant
        $userId = $this->extractUserIdFromGoogleAssistant($request);
        
        if ($userId) {
            // Authentifier l'utilisateur
            $user = \App\Models\User::find($userId);
            
            if ($user) {
                Auth::login($user);
                Log::info('Utilisateur authentifié via Google Assistant', [
                    'user_id' => $userId,
                ]);
                return;
            }
        }

        // Si pas d'utilisateur spécifique, essayer l'authentification par défaut
        if (Auth::check()) {
            Log::info('Utilisateur déjà authentifié', [
                'user_id' => Auth::id()
            ]);
            return;
        }

        // Authentification par défaut pour les tests (à retirer en production)
        if (config('app.env') === 'local' || config('app.env') === 'testing') {
            $defaultUser = \App\Models\User::first();
            if ($defaultUser) {
                Auth::login($defaultUser);
                Log::info('Utilisateur par défaut authentifié pour les tests', [
                    'user_id' => $defaultUser->id
                ]);
                return;
            }
        }

        // Si aucune authentification possible, lever une exception
        throw new \Exception('Impossible d\'authentifier l\'utilisateur via Google Assistant');
    }

    /**
     * Extrait l'ID utilisateur depuis les paramètres Google Assistant
     */
    private function extractUserIdFromGoogleAssistant(Request $request): ?int
    {
        // Essayer de récupérer l'ID utilisateur depuis différents endroits
        $possibleSources = [
            $request->input('queryResult.parameters.user_id'),
            $request->input('queryResult.parameters.userId'),
            $request->input('queryResult.outputContexts.0.parameters.user_id'),
            $request->input('originalDetectIntentRequest.payload.user.userId'),
            $request->input('originalDetectIntentRequest.payload.user.id')
        ];

        foreach ($possibleSources as $source) {
            if ($source && is_numeric($source)) {
                return (int) $source;
            }
        }

        // Essayer de récupérer depuis les headers personnalisés
        $customUserId = $request->header('X-User-ID');
        if ($customUserId && is_numeric($customUserId)) {
            return (int) $customUserId;
        }

        return null;
    }

    /**
     * Vérifie les permissions de l'utilisateur
     */
    private function checkUserPermissions(int $userId): bool
    {
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return false;
        }

        // Vérifier que l'utilisateur a les permissions pour créer des PCMA
        $allowedRoles = ['admin', 'doctor', 'medical_staff', 'association_admin'];
        
        return in_array($user->role, $allowedRoles);
    }

    /**
     * Log les informations de la requête pour le débogage
     */
    private function logRequestInfo(Request $request): void
    {
        Log::info('Requête Google Assistant reçue', [
            'method' => $request->method(),
            'url' => $request->url(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
    }
}
