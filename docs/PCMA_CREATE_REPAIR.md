# Correction de /pcma/create — 30 septembre 2026

## Cause reproduite
La route active était une closure dans routes/web.php. Elle transmettait athletes et users à pcma.create, mais omettait teamDoctorRegistration. Le rendu levait « Undefined variable $teamDoctorRegistration ». Le contrôleur PCMA préparait déjà cette variable mais n'était pas appelé.

## Correction
La route appelle désormais PCMAController::create. Les données de secours fictives de cette route sont supprimées. La liste provient des joueurs persistés et suit le périmètre existant club/association. Le rôle réel system_admin est reconnu dans la sélection des joueurs. La vérification FIFA ID + inscription TeamDoctor du signataire est conservée. Aucun changement de vue ni de design. Aucun dossier médical enregistré lors des tests.

## Vérification
PcmaCreatePageTest charge explicitement routes/web.php car RouteServiceProvider utilise routes/testing.php dans PHPUnit. Les requêtes GET rendent la vue complète en FR et EN avec HTTP 200, même sans joueurs ni TeamDoctor. Vérification des joueurs persistés pour system_admin et de l'isolation d'un médecin de club. Base de test SQLite en mémoire, indépendante de la base opérationnelle.

Le test de résolution des routes précédent charge maintenant lui aussi les routes web actives, en complément de l'audit statique effectué hors environnement testing.

## Limite de livraison
Correction sur feature/performance-score-phase1, sans déploiement de production. La consultation distante de fit.tbhc.uk/pcma/create redirige vers /login dans le navigateur de vérification. Le résultat authentifié de production reste à vérifier après déploiement.
