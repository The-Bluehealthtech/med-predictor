# Odontogramme interactif FIT

Le schéma remplace les anciens widgets de création et d'édition du dossier médical. Il est également restitué en lecture seule dans l'historique dentaire et le portail médical autorisé.

- Deux arcades, 32 dents permanentes, numérotation FDI et repères droite/gauche du patient.
- Sélection à la souris et au clavier, zoom et retour à la vue complète.
- Filtres maxillaire, mandibule et dents renseignées.
- Observation de la dent entière et des faces déjà présentes dans l'ancien code : mesial, distal, occlusal, lingual, buccal. La vue des faces est schématique.
- Réutilisation des états existants : healthy, cavity, filling, crown, missing, implant, treatment. Aucun diagnostic automatique ni nouveau code médical.
- Notes, résumé, liste des dents renseignées et annulation des dernières modifications.
- Aucune dent n'est saine par défaut. Une observation vide n'est pas ajoutée.
- Les modifications sont préparées dans dental_data puis enregistrées par le formulaire médical et la chaîne existante de validation et de stockage daté dans dental_records. Aucun appel d'enregistrement séparé ni autre base.
- Le passage à une autre dent conserve la saisie préparée ; le bouton d'enregistrement médical reste nécessaire pour la persistance.
- Métadonnées de format _meta = {notation: FDI, version: 1}. Les anciennes valeurs sans notation explicite restent conservées sous _legacy lors d'une nouvelle saisie. Elles ne sont pas automatiquement réinterprétées comme FDI : leur numérotation nécessite confirmation.
- Texte clinique inséré avec textContent ou value, jamais en HTML ; CSS isolé en Shadow DOM ; aucune bibliothèque ajoutée.
- Traductions françaises et anglaises ; panneau adaptatif, défilement du schéma sur petit écran.

## Vérification

72 tests serveur, 706 assertions réussis, incluant état et face dentaire → base → détail → portail autorisé et unicité du champ de formulaire. Template Vue/AUT et synchronisation des sections médicales vérifiés.
Le test Chrome isolé utilise des observations fictives, sans API ni écriture réelle ; contrôles en français et anglais, formats ordinateur et mobile. Il teste aussi la non-réinterprétation des anciens identifiants, la protection du texte et les erreurs JavaScript.
Le déploiement Render et la vérification authentifiée en production restent distincts des tests locaux.

Référence de numérotation : American Dental Association, Universal Tooth Designation System, correspondances ISO : https://www.ada.org/-/media/project/ada-organization/ada/ada-org/files/publications/cdt/universal_tooth_designation_system_valueset_2.pdf
