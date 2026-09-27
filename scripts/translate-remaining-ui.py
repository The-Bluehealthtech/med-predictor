#!/usr/bin/env python3
"""Translate static text specific to registration, referee and content guides."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
english = {
    'Guide Utilisateur': 'User Guide',
    'Guide complet pour utiliser le Content Management': 'Complete guide to using Content Management',
    '← Retour au Content Management': '← Back to Content Management',
    'Table des Matières': 'Table of Contents',
    "Rapport d'Arbitre": 'Referee Report',
    '⚽ Nouveau Joueur': '⚽ New Player',
    'Créer un nouveau joueur manuellement': 'Create a new player manually',
    'Photo du Joueur': 'Player Photo',
    'Ajouter une photo': 'Add a photo',
    'Informations de base': 'Basic Information',
    'Prénom *': 'First Name *',
    'Nationalité *': 'Nationality *',
    'Sélectionner une nationalité': 'Select a nationality',
    'Sélectionner une position': 'Select a position',
    'Adresse complète *': 'Full Address *',
    'Téléphone de contact': 'Contact Phone',
    'Tuteur légal (si mineur)': 'Legal Guardian (if underage)',
    'Étudiant': 'Student',
    'Employé': 'Employed',
    'Retraité': 'Retired',
    'Accordé': 'Granted',
    'Données Sportives': 'Sporting Information',
    'Catégorie (calculée automatiquement)': 'Category (calculated automatically)',
    "Calculée automatiquement selon l'âge": 'Calculated automatically based on age',
    'Clubs précédents': 'Previous Clubs',
    'Numéro de licence antérieure': 'Previous Licence Number',
    "Cette inscription sera envoyée à l'association pour approbation.": 'This registration will be sent to the association for approval.',
    'Sélectionner un club': 'Select a club',
    'Sélectionner une équipe': 'Select a team',
    'Sélectionner une association': 'Select an association',
    'Vérification PCMA': 'PCMA Verification',
    'PCMA Validé ✓': 'PCMA Approved ✓',
    'Accéder aux modules PCMA →': 'Go to PCMA modules →',
    'Vérification du statut PCMA en cours...': 'Checking PCMA status...',
    "Pièce d'identité (PDF, JPG, PNG)": 'Identity Document (PDF, JPG, PNG)',
    "Justificatif d'âge (PDF, JPG, PNG)": 'Proof of Age (PDF, JPG, PNG)',
    'Vérifier le statut PCMA →': 'Check PCMA status →',
    'Créer le joueur': 'Create Player',
    'Équipe Domicile': 'Home Team',
    'Équipe Extérieur': 'Away Team',
    'Météo': 'Weather',
    'Ensoleillé': 'Sunny',
    'État du Terrain': 'Pitch Condition',
    'Très mauvais': 'Very Poor',
    '4ème Arbitre *': 'Fourth Official *',
    'Événements': 'Events',
    'Timeline des Événements': 'Event Timeline',
    'Ajouter un Événement': 'Add an Event',
    'Discipline & Santé': 'Discipline & Health',
    'Problèmes de Sécurité': 'Security Issues',
    'Observations Générales *': 'General Observations *',
    'Évaluation de la Qualité du Match': 'Match Quality Assessment',
    'Précédent': 'Previous',
}

en_file = root / 'resources/lang/en.json'
existing = json.loads(en_file.read_text())
paths = [
    'resources/views/modules/player-registration/create.blade.php',
    'resources/views/referee/create-report-form.blade.php',
    'resources/views/admin/content-management/user-guide.blade.php',
]
pattern = re.compile(r'>([^<>]+)<')
for name in paths:
    file = root / name
    source = file.read_text()
    changes = [0]

    def replace(match):
        original = match.group(1)
        label = original.strip()
        if label not in english or '{{' in label or '@' in label:
            return match.group(0)
        existing[label] = english[label]
        changes[0] += 1
        return '>' + original.replace(label, "{{ __('" + label.replace("'", "\\'") + "') }}", 1) + '<'

    source = pattern.sub(replace, source)
    file.write_text(source)
    print(name, changes[0])

original = en_file.read_text()
new = {key: value for key, value in existing.items() if key not in json.loads(original)}
if new:
    items = ',\n'.join('  ' + json.dumps(key, ensure_ascii=False) + ': ' + json.dumps(value, ensure_ascii=False) for key, value in new.items())
    last = original.rfind('}')
    en_file.write_text(original[:last].rstrip() + ',\n' + items + '\n' + original[last:])
