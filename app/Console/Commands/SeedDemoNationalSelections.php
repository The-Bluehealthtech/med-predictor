<?php

namespace App\Console\Commands;

use App\Models\NationalSelection;
use App\Models\NationalSelectionReport;
use App\Models\Player;
use App\Services\Dtn\SelectionSnapshot;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Sélections nationales de démonstration pour l'outil DTN, à trois étapes du
 * cycle : clôturée (états de départ et de retour complets), en sélection
 * (retour attendu) et convoquée (état de départ à préparer).
 *
 * Rejouable : une sélection démo déjà présente pour le même joueur et le même
 * rassemblement n'est pas recréée. Les données de match proviennent du lot
 * démo ; les textes (y compris médicaux) sont fictifs.
 */
class SeedDemoNationalSelections extends Command
{
    protected $signature = 'dtn:demo-selections
        {--association= : Fédération (associations.id) qui convoque}
        {--players=1913,1916,1911 : Joueurs démo : clôturé, en sélection, convoqué}
        {--dtn-user= : Auteur côté DTN (users.id)}
        {--club-user= : Auteur côté club (users.id)}';

    protected $description = 'Crée trois sélections nationales de démonstration pour l\'outil DTN';

    public function handle(SelectionSnapshot $snapshots): int
    {
        $associationId = (int) ($this->option('association') ?: DB::table('associations')->orderBy('id')->value('id'));
        $playerIds = array_map('intval', explode(',', (string) $this->option('players')));
        if (count($playerIds) !== 3 || !$associationId) {
            $this->error('Il faut une fédération et trois joueurs.');

            return self::FAILURE;
        }
        $dtnUser = $this->option('dtn-user') ?: DB::table('users')->where('role', 'dtn')->value('id') ?: DB::table('users')->where('role', 'system_admin')->value('id');
        $clubUser = $this->option('club-user') ?: DB::table('users')->where('role', 'system_admin')->value('id');

        $plans = [
            [
                'player' => $playerIds[0], 'status' => NationalSelection::STATUS_CLOSED,
                'event_type' => 'qualifier', 'event_name' => 'Fenêtre internationale de fin septembre', 'opponent' => 'Deux matchs de qualification',
                'start' => '2026-09-24', 'end' => '2026-09-30', 'sent' => '2026-09-21 10:00', 'returned' => '2026-10-01 09:00',
                'departure' => [
                    'content' => ['availability' => 'available', 'contact' => 'Préparateur physique du club, joignable par la messagerie de la plateforme',
                        'load_recommendation' => 'A enchaîné 90 minutes sur 2 des 3 derniers matchs : pas plus d\'un match complet sur la fenêtre.',
                        'vigilance' => '4 cartons jaunes et 1 rouge cette saison : discipline à surveiller dans les duels.',
                        'technical_notes' => 'Défenseur central droit dans une défense à quatre. Point fort : jeu long ; point travaillé : relance sous pression.'],
                    'fitness' => 'fit',
                    'medical' => ['current_injuries' => 'Aucune blessure en cours.', 'restrictions' => 'Aucune.', 'treatments' => 'Aucun.',
                        'aut' => 'Aucune AUT en cours.', 'recommendations' => 'Légère gêne aux adducteurs le 16/09, résolue : échauffement prolongé conseillé.'],
                ],
                'return' => [
                    'content' => ['matches' => 2, 'starts' => 1, 'minutes' => 135, 'goals' => 0, 'assists' => 0, 'yellow_cards' => 1, 'red_cards' => 0,
                        'avg_rating' => 6.6, 'training_sessions' => 5, 'staff_evaluation' => 7.5,
                        'evaluation_comment' => 'Solide dans le jeu aérien, bonne relance longue. Titulaire au match aller, entré à la 45e au retour.',
                        'incidents' => 'Carton jaune au match aller (faute tactique).', 'fatigue_level' => 'medium', 'injury_risk' => 'medium',
                        'recommendations' => '48 heures de récupération active avant la reprise de l\'entraînement collectif.'],
                    'fitness' => 'fit_with_restrictions',
                    'medical' => ['injuries' => 'Contusion à la cuisse droite lors du match aller.', 'treatments_given' => 'Glace et repos 48 heures, pas de traitement médicamenteux.',
                        'followup' => 'Contrôle par le médecin du club à J+3 avant retour au contact.'],
                ],
            ],
            [
                'player' => $playerIds[1], 'status' => NationalSelection::STATUS_DEPARTURE_SENT,
                'event_type' => 'friendly', 'event_name' => 'Fenêtre internationale d\'octobre', 'opponent' => 'Deux matchs amicaux',
                'start' => '2026-09-30', 'end' => '2026-10-08', 'sent' => '2026-09-27 15:30',
                'departure' => [
                    'content' => ['availability' => 'available_limited', 'contact' => 'Entraîneur adjoint du club',
                        'load_recommendation' => 'Sorti à la 39e lors du dernier match : à ménager, 60 minutes maximum par match.',
                        'vigilance' => 'En progression : 3 passes décisives et score « Rôle et apport » passé de 49,1 à 51,8 sur la saison.',
                        'technical_notes' => 'Latéral gauche offensif ; centres et projection, à encadrer dans le repli défensif.'],
                    'fitness' => 'fit',
                    'medical' => ['current_injuries' => 'Aucune.', 'restrictions' => 'Charge limitée (voir recommandations du staff).', 'treatments' => 'Aucun.',
                        'aut' => 'Aucune AUT en cours.', 'recommendations' => 'Suivi de la fatigue musculaire après chaque match.'],
                ],
            ],
            [
                'player' => $playerIds[2], 'status' => NationalSelection::STATUS_CONVOKED,
                'event_type' => 'training_camp', 'event_name' => 'Stage de préparation de novembre', 'opponent' => null,
                'start' => '2026-11-09', 'end' => '2026-11-15',
            ],
        ];

        $created = 0;
        foreach ($plans as $plan) {
            $player = Player::withoutGlobalScopes()->find($plan['player']);
            if (!$player || !$player->club_id) {
                $this->warn("Joueur {$plan['player']} introuvable ou sans club : ignoré.");
                continue;
            }
            $exists = NationalSelection::query()->where('player_id', $player->id)->where('event_name', $plan['event_name'])->where('is_demo', true)->exists();
            if ($exists) {
                $this->line("Déjà présente : {$player->first_name} {$player->last_name} — {$plan['event_name']}");
                continue;
            }

            DB::transaction(function () use ($plan, $player, $associationId, $dtnUser, $clubUser, $snapshots, &$created) {
                $selection = NationalSelection::create([
                    'player_id' => $player->id, 'club_id' => $player->club_id, 'association_id' => $associationId,
                    'team_label' => 'Équipe nationale A', 'event_type' => $plan['event_type'], 'event_name' => $plan['event_name'],
                    'opponent' => $plan['opponent'], 'start_date' => $plan['start'], 'end_date' => $plan['end'], 'status' => $plan['status'],
                    'convocation_note' => 'Rendez-vous au centre technique national la veille du premier jour, 18 h.',
                    'created_by' => $dtnUser, 'is_demo' => true,
                ]);
                $departure = $plan['departure'] ?? null;
                $selection->reports()->create([
                    'direction' => NationalSelectionReport::DEPARTURE,
                    'status' => $departure ? NationalSelectionReport::STATUS_SENT : NationalSelectionReport::STATUS_DRAFT,
                    'snapshot' => $snapshots->forPlayer($player->id),
                    'content' => $departure['content'] ?? [],
                    'fitness_status' => $departure['fitness'] ?? null,
                    'medical' => $departure['medical'] ?? null,
                    'author_id' => $departure ? $clubUser : null,
                    'sent_at' => isset($plan['sent']) ? Carbon::parse($plan['sent']) : null,
                ]);
                if ($return = $plan['return'] ?? null) {
                    $selection->reports()->create([
                        'direction' => NationalSelectionReport::RETURN,
                        'status' => NationalSelectionReport::STATUS_ACKNOWLEDGED,
                        'content' => $return['content'], 'fitness_status' => $return['fitness'], 'medical' => $return['medical'],
                        'author_id' => $dtnUser, 'sent_at' => Carbon::parse($plan['returned']),
                        'acknowledged_by' => $clubUser, 'acknowledged_at' => Carbon::parse($plan['returned'])->addHours(3),
                    ]);
                }
                $created++;
                $this->info("Créée : {$player->first_name} {$player->last_name} — {$plan['event_name']} ({$selection->statusLabel()})");
            });
        }

        $this->info("{$created} sélection(s) démo créée(s).");

        return self::SUCCESS;
    }
}
