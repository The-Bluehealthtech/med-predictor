# Dockerfile pour FIT CLI
FROM python:3.11-slim

# Métadonnées
LABEL maintainer="FIT Development Team"
LABEL description="FIT CLI - Assistant en Ligne de Commande"
LABEL version="1.0.0"

# Variables d'environnement
ENV PYTHONUNBUFFERED=1
ENV PYTHONDONTWRITEBYTECODE=1
ENV FIT_CLI_VERSION=1.0.0

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    curl \
    git \
    && rm -rf /var/lib/apt/lists/*

# Créer un utilisateur non-root
RUN useradd --create-home --shell /bin/bash fit-cli

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers de dépendances
COPY requirements.txt .

# Installer les dépendances Python
RUN pip install --no-cache-dir -r requirements.txt

# Copier le code source
COPY fit_cli/ ./fit_cli/
COPY fit ./fit
COPY .fitconfig ./.fitconfig

# Rendre le script exécutable
RUN chmod +x ./fit

# Créer le répertoire de configuration
RUN mkdir -p /home/fit-cli/.fit && \
    chown -R fit-cli:fit-cli /home/fit-cli/.fit

# Changer vers l'utilisateur non-root
USER fit-cli

# Définir le répertoire de configuration
ENV FIT_CONFIG_DIR=/home/fit-cli/.fit

# Point d'entrée
ENTRYPOINT ["./fit"]

# Commande par défaut
CMD ["--help"]
