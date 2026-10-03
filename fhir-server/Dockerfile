# Serveur HL7 FHIR R4 dédié à FIT (entrepôt clinique alimenté par les EMR, LIS, RIS, PACS).
# Image officielle HAPI FHIR JPA Server, version figée ; configuration dans application.yaml.
# Déploiement Render : service privé (aucune URL publique), base Postgres dédiée,
# variables HAPI_DB_HOST, HAPI_DB_PORT, HAPI_DB_DATABASE, HAPI_DB_USERNAME, HAPI_DB_PASSWORD, HAPI_HOSTPORT
# (fichier de déploiement : fhir-server/render.yaml).
FROM hapiproject/hapi:v8.12.0-2

COPY application.yaml /app/config/application.yaml
