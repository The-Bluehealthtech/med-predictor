# Dossier médical : CIM-11 et AUT — 1 octobre 2026

## Source et périmètre
Source choisie par l’utilisateur : Formulaire de demande AUT_FR.pdf, annexe 2 de la politique FIFA en matière d’AUT 2024, huit pages. Original conservé dans resources/medical-forms/fifa-aut-fr-2024.pdf.
SHA-256 : 81e6a40e372ae3fec7886901c0befe43afcb1e7749815acaa4e041109bf73dec.
Le choix initial AMA a été remplacé par le PDF FIFA fourni et confirmé par l’utilisateur. Aucun formulaire voisin n’a été substitué.

## CIM-11
- Recherche authentifiée /api/health-records/icd11/search, même service WhoIcd11 et mêmes variables d’environnement que PCMA.
- Création/modification : icd11_selection ne contient que id/release/language ; le serveur demande les codes et libellés à l’OMS.
- health_records.icd11_diagnoses conserve code, libellé, version, langue, URI, source et indications de codage renvoyées par l’OMS.
- Le diagnostic clinique reste indépendant. Aucune sélection implicite ni diagnostic généré.
- Sélection absente : conserver ; liste vide : effacer ; réponse OMS indisponible : 503, aucune écriture.
- Affichage dans le dossier canonique, recherche avec texte DOM sûr et prévention des réponses obsolètes.

## AUT
Sept sections configurées selon le PDF : joueur ; demandes précédentes ; rétroactivité ; informations médicales ; traitement (trois lignes et continuation) ; médecin ; joueur/parent/tuteur.
Déclarations, note sur les preuves, instructions et confidentialité extraites du document fourni, affichées en français original. UI en français/anglais ; aucune traduction ne remplace le document source.
Les signatures ne sont pas fabriquées. Les dates sont des renseignements préparatoires, jamais une preuve de signature. Le formulaire signé et les justificatifs sont joints.
Chemin : dossier médical > Autorisation d’usage à des fins thérapeutiques > Préparer une demande AUT.
Routes : /health-records/{record}/aut, /create, /source, /{aut}/edit et /{aut}/documents/{index}.
tue_requests.player_id et health_record_id pointent vers les données principales ; athlete_id historique conservé mais désormais facultatif.
aut_form_data conserve version, empreinte et champs saisis. Aucun FIFA ID requis pour collecter un brouillon.
Les pièces sont dans medical_aut_documents, chiffrées par Laravel (APP_KEY), au sein de la base principale, sans URL publique ni disque éphémère. L’APP_KEY existante doit être conservée lors des sauvegardes/restaurations.
Contrôle médical club/fédération sur chaque consultation, écriture et téléchargement. Les statuts d’approbation historiques ne peuvent pas être modifiés ici.
Aucun envoi externe, aucune approbation, signature ou conformité FIFA déduite du brouillon.
Le PDF décrit la transmission via le dossier chiffré fourni par la FIFA ; FIT n’effectue pas cette transmission.

## Migration et tests
Schéma AUT historique adopté sans destruction ; table tue_requests explicitement déclarée dans le modèle (l’inférence cherchait t_u_e_requests).
Migrations ajoutées à la liste appliquée par fit:deploy/Render.
62 tests / 519 assertions : PCMA, Healthcare, portail, médical et nouveaux parcours.
Vérifications : codes OMS simulés, conservation/effacement, création, échec sans écriture, brouillons, version source, empreinte PDF, chiffrage effectif des justificatifs, téléchargement, refus interclubs/rôles, données forgées, formats interdits, décisions historiques verrouillées, adoption du schéma.
Contrôles de syntaxe PHP, JavaScript et git diff --check réussis.
Limites : contrats OMS testés par réponses simulées ; rendu authentifié en production non vérifié (2Key) ; aucune donnée clinique de production modifiée pendant les tests. Pas d’affirmation de compatibilité avec un autre formulaire ou de dépôt accepté par FIFA/ADAMS.
