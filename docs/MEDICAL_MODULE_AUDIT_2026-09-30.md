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

## Correctif du module Medical

- Contrôleur MedicalModuleController : autorisation médicale et périmètre club/fédération identiques à Healthcare/PCMA.
- Compteurs désormais factuels : dossiers de santé, dossiers PCMA, PCMA en attente. Aucun risque prédit assimilé à une suspension ou une autorisation.
- Activités issues des dossiers de santé accessibles ; données cliniques étrangères exclues.
- Recherche serveur paginée sur tous les joueurs autorisés. Le sélecteur JavaScript utilisant l'API globale et innerHTML est remplacé par cette liste navigable.
- Profils : diagnosis/notes réels, date nullable, liens vers le dossier complet, modification redirigée vers le dossier existant ou sa création. Plus de statut Active inventé.
- Filtre player_id de health-records effectivement appliqué après contrôle d'accès.
- Routes des prédictions : historique protégé et métadonnées seulement ; création/édition expliquent l'absence de modèle médical validé. POST/PUT/DELETE renvoient 503 au lieu d'un faux succès, sans mutation. Objet étranger ou inexistant : 404. Aucun modèle prédictif inventé.
- Traductions françaises et anglaises ; styles du layout partagé et cartes existantes.
- Joueur sans FIFA ID pris en charge, sans modification de la signature médicale PCMA.

Vérification : 56 tests, 446 assertions réussies (PCMA, Healthcare, portail et module Medical), base SQLite isolée. Tests supplémentaires : rôles, propriété, compteurs, date absente, recherche, liens, langues, historique, mutations refusées et texte joueur échappé.
La reproduction 500 du profil sans date est corrigée localement. Aucun test n'a écrit en production.
Limites : pas de contrôle visuel authentifié en production (2Key) ; API générale /api/players non modifiée et désormais non utilisée par ce module. Aucun moteur médical n'est déclaré validé.
