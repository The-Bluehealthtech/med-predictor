# FIT - Signature documentaire transverse

La signature numérique est une capacité transverse de FIT. Tout workflow produisant un document qui nécessite une validation formelle doit pouvoir demander une signature numérique sans réimplémenter un fournisseur dans le module métier.

## Signataires pris en charge

- médecins et professionnels de santé autorisés ;
- joueurs ;
- dirigeants et officiels ;
- autres signataires explicitement autorisés par le workflow.

## Deux notions à ne pas confondre

1. Signature manuscrite/biométrique : tracé, image et, avec signotec, caractéristiques dynamiques lorsque le dispositif et la base légale le permettent.
2. Signature électronique/numérique de document : signature d'une version figée du document, avec identité du signataire, fournisseur, référence externe, date/heure, audit et, selon le fournisseur, certificat, horodatage et validation long terme.

Les deux mécanismes peuvent coexister sur le même document.

## Règles communes

- Un document est figé avant demande de signature numérique.
- La signature porte sur cette version et non sur des données modifiables après coup.
- Toute modification métier après signature produit une nouvelle version et nécessite une nouvelle signature ; l'historique signé n'est pas écrasé.
- Les secrets fournisseurs ne sont jamais stockés dans les documents ni affichés dans l'UI.
- Le fournisseur doit être configuré et activé dans `/modules/api-connectors`.
- Les demandes et résultats sont audités avec le rôle du signataire et une référence fournisseur.
- Aucun workflow ne doit simuler une signature ou un certificat externe.
- Le PDF signé est conservé via le disque privé configuré par `DOCUMENT_SIGNATURE_DISK`. Les nouveaux déploiements Go Live doivent utiliser un stockage durable ; `local` est réservé au développement/test ou à une instance disposant explicitement d'un disque persistant.
- Le disque `signature_s3` utilise uniquement les secrets `SIGNATURE_AWS_*` et ne réutilise pas implicitement les identifiants AWS Rekognition.
- Chaque résultat conserve `signed_disk`, `signed_path` et `signed_sha256`. Les anciennes signatures sans `signed_disk` restent lues depuis `local` pour compatibilité.

## PCMA

Le PCMA conserve la signature manuscrite existante du médecin. La signature numérique du PDF final est ajoutée comme couche de certification documentaire. Le médecin examinateur est le signataire principal. La co-signature du joueur est supportée par le moteur transverse mais n'est pas rendue obligatoire sans règle métier/réglementaire explicite.
