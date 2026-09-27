path = "app/Http/Controllers/CompetitionManagementController.php"
exec(open(".claude_tmp_edit.py").read())

old = """                    // Create match
                    $match = GameMatch::create([
                        'competition_id' => $competition->id,
                        'home_team_id' => $homeTeamId,
                        'away_team_id' => $awayTeamId,
                        'home_club_id' => $homeTeam->club_id,
                        'away_club_id' => $awayTeam->club_id,
                        'matchday' => $matchday,
                        'match_date' => $matchDate,
                        'kickoff_time' => $matchDate->copy()->setTime(15, 0),
                        'venue' => 'home',
                        'stadium' => $homeTeam->club->stadium,
                        'capacity' => $homeTeam->club->stadium_capacity,"""

new = """                    // Create match
                    //
                    // NOTE (audit factice -> reel, 2026-09) : 'capacity'
                    // était tiré au hasard (rand(40000, 75000)) et
                    // enregistré comme s'il s'agissait d'une vraie
                    // capacité de stade. Remplacé par la vraie colonne
                    // clubs.stadium_capacity (actuellement vide pour tous
                    // les clubs : la valeur enregistrée sera donc null
                    // jusqu'à ce que cette donnée soit renseignée).
                    $match = GameMatch::create([
                        'competition_id' => $competition->id,
                        'home_team_id' => $homeTeamId,
                        'away_team_id' => $awayTeamId,
                        'home_club_id' => $homeTeam->club_id,
                        'away_club_id' => $awayTeam->club_id,
                        'matchday' => $matchday,
                        'match_date' => $matchDate,
                        'kickoff_time' => $matchDate->copy()->setTime(15, 0),
                        'venue' => 'home',
                        'stadium' => $homeTeam->club->stadium,
                        'capacity' => $homeTeam->club->stadium_capacity,"""

apply_edit(path, old, new, "add audit comment to first fixture-generation capacity fix")
