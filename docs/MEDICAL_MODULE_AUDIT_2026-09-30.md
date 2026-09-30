# Medical — audit du 30 septembre 2026

Audit du code au commit 0c44a64 et requêtes HTTP dans SQLite isolée. Aucun correctif applicatif réalisé dans cet audit ; aucune mutation de production.

| Parcours | Résultat local |
| --- | --- |
| /modules/medical, rôle club_medical | 200, mais activités cliniques d'un autre club affichées |
| /modules/medical, rôle player | 200, sans refus médical |
| /modules/medical/athlete/20, joueur lié au joueur 10 | 200 : fiche médicale d'un autre joueur accessible |
| /modules/medical/athlete/10, dossier sans record_date | 500 reproduite |
| /modules/medical/athlete/10/edit | 404, aucune route correspondante |
| /medical-predictions/create | 200 ; POST associé = redirection avec succès sans écriture |
| /modules/medical?lang=fr et lang=en | 200, textes de la vue toujours anglais |

Les joueurs et dossiers proviennent de players et health_records dans la base principale. Les statistiques et activités sont lues directement dans medical_predictions sans périmètre médical explicite. Un statut verified est compté comme autorisation médicale ; un risque >= 0.7 comme suspension. Ces équivalences sont des proxies écrits dans la route, pas des décisions médicales PCMA signées. Une prédiction d'un autre club a compté dans chacun des indicateurs avec les données de test.

La vue athlete affiche Active en dur, même sans état clinique disponible. Le lien Health Report utilise player_id mais HealthRecordController::index ne filtre pas ce paramètre. Aucun lien ne permet d'ouvrir directement chaque dossier dans la liste récente. Les champs title/description utilisés ne correspondent pas aux champs cliniques diagnosis/notes.

Le sélecteur appelle /api/players : requête DB globale authentifiée, sans périmètre médical, ni pagination, avec une requête club par joueur. Les noms/clubs reçus sont interpolés dans innerHTML : risque d'injection HTML. La réponse 401/403 n'est pas distinguée d'une liste vide. La recherche principale filtre seulement les 25 joueurs de la page courante.

Priorités : droits médicaux et propriété des dossiers ; rendre les indicateurs cohérents avec des décisions signées et sources validées ; corriger la date nullable et le lien Modifier ; remplacer les actions de prédiction fictives ; sélecteur sûr et filtré ; traduction et recherche globale.

Limite : pas de session de production 2Key. Les reproductions sont locales et ne démontrent pas un accès effectif à des données de production. Le rendu visuel authentifié et les outils IA réels ne sont pas vérifiés.
