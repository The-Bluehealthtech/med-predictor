# Évaluation du dossier médical FIT — 1 octobre 2026

## Périmètre et méthode
Source : PDF fourni « Sports telemedecine Conf 1.pdf », 55 pages.
Extraction complète du texte et inspection visuelle des 55 pages, avec agrandissement des pages médicales.
Comparaison des vues create/edit/show, modèle HealthRecord, migrations, contrôleurs, AUT et tests.
Les résultats ci-dessous portent sur le code et les tests isolés. Ils ne constituent pas une certification médicale, FIFA, FHIR ou antidopage.
La présence d’une colonne ou d’un formulaire ne suffit pas à démontrer un parcours de collecte et de restitution complet.
Aucune donnée personnelle de production n’a été extraite. Les fixtures servent uniquement aux tests.

## Modification livrée
- Onglet Dopage dans le détail canonique du dossier et son alias Healthcare.
- Historique des contrôles du même player_id : tests, statuts, dates, laboratoire, panel.
- Historique des demandes AUT du joueur, dates enregistrées et liens protégés.
- Bouton « Créer un dossier AUT » sur le dossier courant, sans exigence de FIFA ID.
- Affichage séparé des données AUT historiques ; aucun ancien statut converti en décision nouvelle.
- Profil biologique enregistré et alertes sur les médicaments, sans interprétation automatique.
- Absence de données explicitement affichée ; aucun résultat négatif ou feu vert déduit.
- Alertes médicamenteuses fondées sur la référence fournie 2025, indiquée comme telle.
- Traductions françaises et anglaises, styles des cartes du dossier réutilisés.
- Accès direct possible avec ?tab=doping ; paramètre limité aux onglets existants.
- Correction du stockage : doping_test_date/type/result du formulaire alimentent doping_tests.
- Ajout à l’historique dans la transaction, avec verrou sur le dossier existant.
- La seule date par défaut du formulaire ne crée pas un faux test ; les anciens tests restent conservés.

## Matrice des sections médicales du PDF
| Section du document | Pages | État constaté | Écart précis |
|---|---:|---|---|
| Identification sécurisée et profils d’accès | 19 | Présent, testé pour le périmètre médical | Droits par rôle/club ; test de refus interclub. Audit de sécurité global hors périmètre. |
| Identité et rattachement du joueur | 20 | Présent | player_id canonique ; FIFA ID facultatif selon l’instruction actuelle. Conformité complète des échanges FIFA non certifiée ici. |
| PCMA standard et avancé | 21–28 | Module PCMA présent ; couverture complète non démontrée | Dossiers PCMA liés au joueur dans show. Vérification champ par champ des deux variantes et des pièces requises restant nécessaire. |
| Dossier dentaire | 21, 29 | Partiel | Formulaires présents, mais dental_data/dental_notes/dental_treatment_plan ne correspondent pas aux attributs sauvegardables dental_records/dental_treatments. L’onglet show reste un texte de remplacement. |
| Blessures F-MARC | 23, 30 | Partiel | injury_records existe, mais le parcours structuré complet montré dans le PDF n’est pas restitué dans show ; fifa_fmarc_assessments ne possède pas de saisie spécialisée démontrée. |
| Traumatismes crâniens SCAT | 23, 31 | Partiel | scat_assessments texte est sauvegardable ; champs détaillés scat_* ne sont pas convertis vers cet objet par le contrôleur. Reprise des valeurs anciennes incomplète dans edit. |
| Maladies F-MARC | 23, 32 | Partiel | Symptômes/diagnostic/traitement présents ; formulaire normalisé avec date, cause et jours d’absence tel que montré non démontré. |
| Suivi des maladies cardiaques | 23–24 | Partiel | Champs/codes cardiaques prévus ; pas de restitution dédiée de l’historique dans show. |
| MAPA | 24 | Partiel, défaut de correspondance | Saisie détaillée mapa_date/mapa_pas_*/mapa_pad_* ; stockage attend mapa_test_date/mapa_results/mapa_24h_profile. Aucune conversion spécialisée dans le contrôleur. |
| IRM | 24, 34 | Partiel | mri_results sauvegardable ; collecte imaging_data et fichiers IRM ne bénéficient pas d’un parcours stockage privé/restitution complet démontré. |
| ECG d’effort | 24, 35 | Partiel | Plusieurs champs canoniques existent ; ecg_effort_max_fc ne correspond pas à ecg_effort_max_hr. Fichier et tableau par phase non couverts de bout en bout. |
| Scintigraphie | 24, 36 | Partiel | Date/type/résultats sauvegardables ; fichier non pris en charge par le flux HealthRecord, restitution dédiée absente. |
| Contrôles antidopage | 25 | Amélioré et testé | Collecte minimale et nouvel historique fonctionnels ; import laboratoire, unités/seuils détaillés et traçabilité réglementaire non démontrés. |
| AUT/TUE | 25, 37 | Brouillons fonctionnels, testés | Sept sections du formulaire FIFA, pièces privées chiffrées, rattachement canonique. Envoi externe et décision de l’autorité non automatisés ; brouillon jamais assimilé à une approbation. |
| Profil biologique | 25, 38 | Partiel | Structure biological_profile et lecture ajoutée. La sélection d’un code SNOMED seule ne collecte pas le tableau longitudinal du PDF. Passeport biologique réglementaire non établi. |
| Codification médicale | 33 | Partiel | CIM-11 OMS et médicaments RxNorm résolus côté serveur ; listes historiques ICD-10/SNOMED/LOINC locales ne prouvent pas une validation terminologique complète. |
| Tableau de suivi des blessures | 39 | Non démontré dans le dossier audité | Pas de tableau longitudinal équivalent montré dans la vue canonique. Nécessite dates, type, mécanisme, retour et exposition correctement collectés. |

## Fonctions transversales décrites dans la présentation
| Fonction | Pages | Conclusion |
|---|---:|---|
| Organisation prévention / suivi / antidopage | 17–18 | PCMA et antidopage accessibles ; le suivi clinique reste fragmenté entre formulaires et détail. |
| Workflow licences, extraction, FIFA Connect | 6–16 | Hors audit clinique approfondi ; ne pas en déduire une certification d’interopérabilité. |
| HIE, FHIR, IHE et standards | 13, 43–48, 54 | Export HL7 protégé connu ; conformité de profils, échange réel et certification non vérifiés ici. |
| EHR, radiologie, DICOM | 49–52 | Interfaces existent ; persistance des fichiers et parcours réel d’imagerie ne sont pas validés par leur seule présence. |
| Téléconsultation et portail patient | 51 | Portail existant ; téléconsultation complète et publication de chaque sous-section au portail non vérifiées dans cette livraison. |
| Analyses, statistiques et KPI | 17, 39–42, 53 | Ne pas assimiler tableaux de bord à validation clinique. Indicateurs fondés sur des événements médicaux réels à vérifier séparément. |
| Prédictions par IA | Hors exigence clinique précise du PDF | Aucun modèle médical validé démontré. Le détail distingue les prédictions historiques non validées et désactive la génération ; aucune prédiction inventée. |

## Priorités proposées, sans modifications implicites de ces sections
1. Corriger les correspondances saisie → validation → stockage → relecture pour dentaire, SCAT, MAPA et imagerie.
2. Ajouter la restitution structurée et datée de ces sections dans le détail médical et vérifier le portail autorisé.
3. Vérifier les variantes PCMA standard/avancée et le parcours F-MARC complet, sans créer de champs par supposition.
4. Compléter le profil biologique longitudinal et la provenance des résultats de laboratoire.
5. Traiter séparément transmission AUT, interopérabilité et modèles IA : ces sujets demandent leurs propres preuves.

## Vérifications effectuées
Suite ciblée santé/PCMA/AUT/portail/performance : 90 tests, 668 assertions, tous réussis.
Nouveaux tests : historique réel de contrôles, AUT du même joueur, routes canonique/alias, français/anglais, refus interclub, stockage et conservation des tests, absence non remplacée par résultat fictif.
Validation du template Vue du formulaire de création AUT et activation du fieldset : réussie.
Aucun nouveau schéma ou migration nécessaire.
Vérification visuelle authentifiée sur fit.tbhc.uk et état Live du déploiement Render : non confirmés.
Les lacunes listées ne sont pas déclarées réparées dans cette livraison.
