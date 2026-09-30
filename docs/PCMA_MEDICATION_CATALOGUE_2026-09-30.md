# PCMA : catalogue de médicaments fourni
Source utilisateur : csv4Emd_Fr_2609A.zip. Version conservée : 2609A.
## Contenu et provenance
- 3 516 produits MP, 8 731 présentations MPP et 2 727 substances Stof.
- Liens MPP.mpcv → MP.MPcv, Sam.mppcv → MPP.mppcv et Sam.Stofcv → Stof.StofCV : aucun lien manquant dans le fichier fourni.
- Catalogue normalisé gzip, reproductible depuis les CSV MP/MPP/Sam ; identifiants source conservés comme chaînes.
- Aucun code ATC fourni : atc=null pour chaque sélection ; aucune correspondance inférée.
- Données de référence, distinctes des fixtures cliniques synthétiques et des dossiers patients.
## Fonctionnement
- Migration medication_catalogue puis import idempotent dans la connexion opérationnelle principale au démarrage fit:deploy.
- Recherche authentifiée et réservée aux rôles médicaux ; nom de produit ou substance, accents normalisés, 20 résultats maximum par recherche.
- Création/édition : recherche, sélection, présentation facultative et dose/voie/fréquence saisies par le médecin ; notes libres conservées.
- Aucune dose, voie ni fréquence déduite du conditionnement commercial.
- Sélections persistées dans pcmas.result_json.medical_history.medication_products ; références résolues côté serveur.
- API V1, autosauvegarde, enregistrement web, édition et PDF utilisent les mêmes identifiants enregistrés.
- Consultation : médicaments sélectionnés affichés ; libellés d'interface FR/EN, noms du catalogue fourni conservés.
- Retrait d'une sélection remplace réellement la liste ; autres données cliniques conservées.
## Vérifications
- 31 tests Laravel, 268 assertions réussies : import/réimport, recherche réelle sur le fichier fourni, droits, références inconnues, libellé falsifié, persistance/reprise et retrait.
- Syntaxe JavaScript et PHP vérifiée ; aucune écriture de test dans la base opérationnelle.
- La recette navigateur authentifiée et la vérification de l'import en production restent à réaliser.
- Aucun travail CIM-11 n'est inclus dans ce lot ; la correspondance ATC reste à fournir.
