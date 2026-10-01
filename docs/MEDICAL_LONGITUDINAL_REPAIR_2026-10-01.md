# Corrections du dossier médical — 1 octobre 2026

Les observations sont conservées dans la base principale, par joueur, avec date d'examen, provenance et historique. Aucun résultat clinique absent n'est inventé.

| Section | Colonne canonique existante |
|---|---|
| Dentaire | dental_records |
| SCAT | scat_assessments |
| MAPA | mapa_results |
| Imagerie | imaging_results |
| IRM | mri_results |
| ECG d'effort | ecg_effort_results |
| Scintigraphie | scintigraphy_results |
| F-MARC traumatologie | fifa_fmarc_assessments |
| Maladies | illness_records |
| Biologie longitudinale | biological_profile |
| Laboratoire | blood_test_results |

La configuration versionnée medical_sections décrit uniquement les champs existants et les champs JSON justifiés par le document fourni (pages 30 et 32 pour F-MARC). Les champs historiques restent lisibles ; une date inconnue n'est pas reconstituée.
Les formulaires contrôlent la sélection explicite de la section, la date, les valeurs et les pièces jointes. Les anciennes et nouvelles présentations de saisie sont synchronisées. Une modification partielle n'efface plus les résultats omis.
Les résultats de laboratoire conservent analyte, résultat, laboratoire, prélèvement et référence du compte rendu ; unité, méthode et bornes de référence restent facultatives et ne sont pas inventées.
Les pièces jointes sont chiffrées dans la base principale via health_record_documents (migration enregistrée dans DeployFit). Leur téléchargement vérifie les droits, le joueur, le dossier et l'intégrité SHA-256.
Le détail médical restitue les observations structurées et datées. Le portail du joueur donne accès en lecture à ses propres dossiers ; les droits de modification médicale restent séparés.

## Vérifications et limites

- Suite isolée : 97 tests, 799 assertions, réussis. Données de test fictives uniquement ; aucune écriture clinique de production.
- Tests JavaScript : activation explicite, synchronisation, absence de faux odontogramme et template Vue/AUT : réussis.
- Contrôles de syntaxe et git diff --check : réussis.
- Tests des historiques, dates, valeurs manquantes et zéro explicite, provenance, transactions, pièces chiffrées, accès propre joueur et refus des dossiers étrangers.
- Audit en lecture seule : commande medical:audit-storage et route /medical/storage-audit réservée au system_admin. Sortie agrégée, sans données nominatives.
- Base connectée sur le Mac : SQLite, 825 joueurs, aucun dossier médical. Ce constat ne décrit pas la base du déploiement FIT. Il ne permet pas de certifier que chaque joueur de production dispose de toutes les observations.
- Le type canonique est pcma (PCMA). La migration 000003 normalise les anciennes valeurs bpma ; les observations et signatures sont conservées. Les autres types existants sont cardio, dental, neurological et orthopedic. Aucune variante standard/avancée persistée n'a été trouvée ; le document ne définit pas une distinction technique suffisante. Aucun code de variante n'a été inventé.
- F-MARC : collecte et restitution des champs explicitement visibles dans le document ; aucune certification officielle du parcours n'est affirmée.
- Transmission AUT : non démontrée ; la gestion locale d'un dossier ne constitue pas une transmission ni une autorisation.
- Interopérabilité FHIR/HIE : certification et échange réel non démontrés.
- Modèles IA : aucune validation clinique ou nouvelle prédiction n'est revendiquée.
- Le contrôle navigateur et l'audit de la base de production restent à effectuer après le déploiement ; les tests isolés ne les remplacent pas.

## Correction de nomenclature PCMA

Les formulaires, validations, filtres, exports et statistiques API utilisent pcma ; BPMA n'est pas un type médical. Les anciennes valeurs restent lisibles et filtrables jusqu'à leur migration. La migration 000003 normalise uniquement la colonne type, y compris pour les dossiers signés, sans modifier les observations, signatures ou dates. Elle est enregistrée dans le parcours de déploiement.
Vérification : 84 tests PCMA, 762 assertions réussis ; migration SQLite depuis une colonne enum historique, conservation du contenu signé et seconde exécution sans effet. Tests JavaScript/Vue et contrôle de syntaxe réussis. Les chemins PostgreSQL/MySQL n'ont pas été exécutés dans cet environnement ; le déploiement effectif et la migration de production restent à confirmer.
