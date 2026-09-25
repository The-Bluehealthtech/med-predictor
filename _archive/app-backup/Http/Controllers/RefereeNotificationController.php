<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GameMatch;
use App\Models\MatchOfficial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class RefereeNotificationController extends Controller
{
    /**
     * Afficher les notifications d'un arbitre
     */
    public function index(Request $request)
    {
        $refereeId = auth()->id();
        
        // Récupérer les notifications récentes
        $notifications = $this->getRefereeNotifications($refereeId);
        
        // Récupérer les assignations récentes
        $recentAssignments = $this->getRecentAssignments($refereeId);
        
        // Récupérer les matchs à venir
        $upcomingMatches = $this->getUpcomingMatches($refereeId);
        
        return view('referee.notifications', compact(
            'notifications',
            'recentAssignments',
            'upcomingMatches'
        ));
    }

    /**
     * Envoyer une notification d'assignation
     */
    public function sendAssignmentNotification(Request $request)
    {
        $request->validate([
            'referee_id' => 'required|exists:users,id',
            'match_id' => 'required|exists:matches,id',
            'role' => 'required|in:referee,assistant_referee_1,assistant_referee_2,fourth_official,var_referee',
            'notification_type' => 'required|in:assignment,reminder,cancellation,change'
        ]);

        try {
            $referee = User::findOrFail($request->referee_id);
            $match = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])->findOrFail($request->match_id);
            
            $notificationData = $this->buildNotificationData($request, $match);
            
            // Envoyer la notification
            $this->sendNotification($referee, $notificationData);
            
            // Enregistrer dans la base de données
            $this->saveNotification($referee->id, $notificationData);
            
            Log::info('Notification d\'assignation envoyée', [
                'referee_id' => $referee->id,
                'match_id' => $match->id,
                'type' => $request->notification_type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification envoyée avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur envoi notification', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de la notification'
            ], 500);
        }
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id'
        ]);

        try {
            $notification = DB::table('notifications')
                ->where('id', $request->notification_id)
                ->where('notifiable_id', auth()->id())
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }

    /**
     * Obtenir les notifications d'un arbitre
     */
    private function getRefereeNotifications($refereeId)
    {
        return DB::table('notifications')
            ->where('notifiable_id', $refereeId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'info',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at
                ];
            });
    }

    /**
     * Obtenir les assignations récentes
     */
    private function getRecentAssignments($refereeId)
    {
        return MatchOfficial::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->where('user_id', $refereeId)
            ->whereHas('match', function ($query) {
                $query->where('match_date', '>=', now()->subDays(7));
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtenir les matchs à venir
     */
    private function getUpcomingMatches($refereeId)
    {
        return MatchOfficial::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->where('user_id', $refereeId)
            ->whereHas('match', function ($query) {
                $query->where('match_date', '>=', now())
                      ->where('match_date', '<=', now()->addDays(30));
            })
            ->orderBy('match_date', 'asc')
            ->get();
    }

    /**
     * Construire les données de notification
     */
    private function buildNotificationData(Request $request, GameMatch $match)
    {
        $roleLabels = [
            'referee' => 'Arbitre Principal',
            'assistant_referee_1' => 'Assistant 1',
            'assistant_referee_2' => 'Assistant 2',
            'fourth_official' => '4ème Arbitre',
            'var_referee' => 'Arbitre VAR'
        ];

        $typeLabels = [
            'assignment' => 'Nouvelle Assignation',
            'reminder' => 'Rappel de Match',
            'cancellation' => 'Annulation de Match',
            'change' => 'Modification d\'Assignation'
        ];

        $matchInfo = $match->homeTeam->name . ' vs ' . $match->awayTeam->name;
        $matchDate = Carbon::parse($match->match_date)->format('d/m/Y à H:i');
        $role = $roleLabels[$request->role] ?? $request->role;
        $type = $typeLabels[$request->notification_type] ?? $request->notification_type;

        return [
            'type' => $request->notification_type,
            'title' => $type,
            'message' => "Vous avez été assigné comme {$role} pour le match {$matchInfo} le {$matchDate}",
            'match_id' => $match->id,
            'role' => $request->role,
            'match_info' => [
                'home_team' => $match->homeTeam->name,
                'away_team' => $match->awayTeam->name,
                'competition' => $match->competition->name,
                'date' => $match->match_date,
                'venue' => $match->venue
            ]
        ];
    }

    /**
     * Envoyer la notification
     */
    private function sendNotification(User $referee, array $notificationData)
    {
        // Ici, on pourrait intégrer différents canaux de notification :
        // - Email
        // - SMS
        // - Push notification (pour l'app mobile future)
        // - Notification in-app
        
        // Pour l'instant, on utilise le système de notifications Laravel
        $referee->notify(new \App\Notifications\RefereeAssignmentNotification($notificationData));
    }

    /**
     * Sauvegarder la notification dans la base
     */
    private function saveNotification($refereeId, array $notificationData)
    {
        DB::table('notifications')->insert([
            'id' => \Str::uuid(),
            'type' => 'App\\Notifications\\RefereeAssignmentNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $refereeId,
            'data' => json_encode($notificationData),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * API pour les notifications mobiles (préparation future)
     */
    public function getMobileNotifications(Request $request)
    {
        $refereeId = auth()->id();
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $notifications = DB::table('notifications')
            ->where('notifiable_id', $refereeId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'info',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'match_info' => $data['match_info'] ?? null
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'has_more' => $notifications->count() === $limit
        ]);
    }

    /**
     * API pour les assignations mobiles
     */
    public function getMobileAssignments(Request $request)
    {
        $refereeId = auth()->id();
        $period = $request->get('period', 'upcoming'); // upcoming, past, all

        $query = MatchOfficial::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->where('user_id', $refereeId);

        switch ($period) {
            case 'upcoming':
                $query->whereHas('match', function ($q) {
                    $q->where('match_date', '>=', now());
                });
                break;
            case 'past':
                $query->whereHas('match', function ($q) {
                    $q->where('match_date', '<', now());
                });
                break;
        }

        $assignments = $query->orderBy('match_date', 'asc')->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'role' => $assignment->role,
                    'match' => [
                        'id' => $assignment->match->id,
                        'home_team' => $assignment->match->homeTeam->name,
                        'away_team' => $assignment->match->awayTeam->name,
                        'competition' => $assignment->match->competition->name,
                        'date' => $assignment->match->match_date,
                        'venue' => $assignment->match->venue,
                        'status' => $assignment->match->status
                    ]
                ];
            });

        return response()->json([
            'assignments' => $assignments,
            'period' => $period
        ]);
    }
}
use Carbon\Carbon;

class RefereeNotificationController extends Controller
{
    /**
     * Afficher les notifications d'un arbitre
     */
    public function index(Request $request)
    {
        $refereeId = auth()->id();
        
        // Récupérer les notifications récentes
        $notifications = $this->getRefereeNotifications($refereeId);
        
        // Récupérer les assignations récentes
        $recentAssignments = $this->getRecentAssignments($refereeId);
        
        // Récupérer les matchs à venir
        $upcomingMatches = $this->getUpcomingMatches($refereeId);
        
        return view('referee.notifications', compact(
            'notifications',
            'recentAssignments',
            'upcomingMatches'
        ));
    }

    /**
     * Envoyer une notification d'assignation
     */
    public function sendAssignmentNotification(Request $request)
    {
        $request->validate([
            'referee_id' => 'required|exists:users,id',
            'match_id' => 'required|exists:matches,id',
            'role' => 'required|in:referee,assistant_referee_1,assistant_referee_2,fourth_official,var_referee',
            'notification_type' => 'required|in:assignment,reminder,cancellation,change'
        ]);

        try {
            $referee = User::findOrFail($request->referee_id);
            $match = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])->findOrFail($request->match_id);
            
            $notificationData = $this->buildNotificationData($request, $match);
            
            // Envoyer la notification
            $this->sendNotification($referee, $notificationData);
            
            // Enregistrer dans la base de données
            $this->saveNotification($referee->id, $notificationData);
            
            Log::info('Notification d\'assignation envoyée', [
                'referee_id' => $referee->id,
                'match_id' => $match->id,
                'type' => $request->notification_type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification envoyée avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur envoi notification', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de la notification'
            ], 500);
        }
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id'
        ]);

        try {
            $notification = DB::table('notifications')
                ->where('id', $request->notification_id)
                ->where('notifiable_id', auth()->id())
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }

    /**
     * Obtenir les notifications d'un arbitre
     */
    private function getRefereeNotifications($refereeId)
    {
        return DB::table('notifications')
            ->where('notifiable_id', $refereeId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'info',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at
                ];
            });
    }

    /**
     * Obtenir les assignations récentes
     */
    private function getRecentAssignments($refereeId)
    {
        return MatchOfficial::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->where('user_id', $refereeId)
            ->whereHas('match', function ($query) {
                $query->where('match_date', '>=', now()->subDays(7));
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtenir les matchs à venir
     */
    private function getUpcomingMatches($refereeId)
    {
        return MatchOfficial::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->where('user_id', $refereeId)
            ->whereHas('match', function ($query) {
                $query->where('match_date', '>=', now())
                      ->where('match_date', '<=', now()->addDays(30));
            })
            ->orderBy('match_date', 'asc')
            ->get();
    }

    /**
     * Construire les données de notification
     */
    private function buildNotificationData(Request $request, GameMatch $match)
    {
        $roleLabels = [
            'referee' => 'Arbitre Principal',
            'assistant_referee_1' => 'Assistant 1',
            'assistant_referee_2' => 'Assistant 2',
            'fourth_official' => '4ème Arbitre',
            'var_referee' => 'Arbitre VAR'
        ];

        $typeLabels = [
            'assignment' => 'Nouvelle Assignation',
            'reminder' => 'Rappel de Match',
            'cancellation' => 'Annulation de Match',
            'change' => 'Modification d\'Assignation'
        ];

        $matchInfo = $match->homeTeam->name . ' vs ' . $match->awayTeam->name;
        $matchDate = Carbon::parse($match->match_date)->format('d/m/Y à H:i');
        $role = $roleLabels[$request->role] ?? $request->role;
        $type = $typeLabels[$request->notification_type] ?? $request->notification_type;

        return [
            'type' => $request->notification_type,
            'title' => $type,
            'message' => "Vous avez été assigné comme {$role} pour le match {$matchInfo} le {$matchDate}",
            'match_id' => $match->id,
            'role' => $request->role,
            'match_info' => [
                'home_team' => $match->homeTeam->name,
                'away_team' => $match->awayTeam->name,
                'competition' => $match->competition->name,
                'date' => $match->match_date,
                'venue' => $match->venue
            ]
        ];
    }

    /**
     * Envoyer la notification
     */
    private function sendNotification(User $referee, array $notificationData)
    {
        // Ici, on pourrait intégrer différents canaux de notification :
        // - Email
        // - SMS
        // - Push notification (pour l'app mobile future)
        // - Notification in-app
        
        // Pour l'instant, on utilise le système de notifications Laravel
        $referee->notify(new \App\Notifications\RefereeAssignmentNotification($notificationData));
    }

    /**
     * Sauvegarder la notification dans la base
     */
    private function saveNotification($refereeId, array $notificationData)
    {
        DB::table('notifications')->insert([
            'id' => \Str::uuid(),
            'type' => 'App\\Notifications\\RefereeAssignmentNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $refereeId,
            'data' => json_encode($notificationData),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * API pour les notifications mobiles (préparation future)
     */
    public function getMobileNotifications(Request $request)
    {
        $refereeId = auth()->id();
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $notifications = DB::table('notifications')
            ->where('notifiable_id', $refereeId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'info',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'match_info' => $data['match_info'] ?? null
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'has_more' => $notifications->count() === $limit
        ]);
    }

    /**
     * API pour les assignations mobiles
     */
    public function getMobileAssignments(Request $request)
    {
        $refereeId = auth()->id();
        $period = $request->get('period', 'upcoming'); // upcoming, past, all

        $query = MatchOfficial::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->where('user_id', $refereeId);

        switch ($period) {
            case 'upcoming':
                $query->whereHas('match', function ($q) {
                    $q->where('match_date', '>=', now());
                });
                break;
            case 'past':
                $query->whereHas('match', function ($q) {
                    $q->where('match_date', '<', now());
                });
                break;
        }
        $assignments = $query->orderBy('match_date', 'asc')->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'role' => $assignment->role,
                    'match' => [
                        'id' => $assignment->match->id,
                        'home_team' => $assignment->match->homeTeam->name,
                        'away_team' => $assignment->match->awayTeam->name,
                        'competition' => $assignment->match->competition->name,
                        'date' => $assignment->match->match_date,
                        'venue' => $assignment->match->venue,
                        'status' => $assignment->match->status
                    ]
                ];
            });

        return response()->json([
            'assignments' => $assignments,
            'period' => $period
        ]);
    }
}
