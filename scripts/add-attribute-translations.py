#!/usr/bin/env python3
"""Reviewed placeholder, title, aria label and image alt translations."""
import json
import pathlib
root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Fonctionnalité à venir : aucune donnée réelle disponible pour le moment": "Coming soon: no recorded data available yet",
"Nom du joueur...": "Player name...",
"Nom de l'arbitre principal": "Main referee's name",
"Nom de l'assistant 1": "First assistant's name",
"Nom de l'assistant 2": "Second assistant's name",
"Nom du 4ème arbitre": "Fourth official's name",
"Nom de l'arbitre VAR": "VAR referee's name",
"Nom de l'assistant VAR": "Assistant VAR referee's name",
"Décrivez les incidents disciplinaires...": "Describe disciplinary incidents...",
"Décrivez les incidents avec le public...": "Describe incidents involving spectators...",
"Décrivez les problèmes de sécurité...": "Describe security issues...",
"Décrivez le déroulement général du match, l'ambiance, le comportement des équipes...": "Describe the match, atmosphere and teams' behavior...",
"Évaluez la qualité technique du match, le niveau de jeu...": "Assess the match's technical quality and level of play...",
"Description de la compétition...": "Competition description...",
"Règlement spécifique...": "Specific regulations...",
"Nom du joueur, email ou club...": "Player name, email or club...",
"Notes sur cette dent...": "Notes on this tooth...",
"Rechercher un joueur...": "Search for a player...",
"Ex: Aucun, Vitamines...": "E.g. None, Vitamins...",
"Photo de Cristiano Ronaldo": "Photo of Cristiano Ronaldo",
"Notes sur cette dent": "Notes on this tooth",
"La synchronisation FIFA TMS sera activée après configuration et validation des clés API.": "FIFA TMS synchronization will be enabled after API keys are configured and validated.",
};
translations.update({
"ex: Nouvelle fonctionnalité activée": "E.g. New feature enabled",
"Description détaillée du paramètre": "Detailed setting description",
"Valeur du paramètre": "Setting value",
"Valeur par défaut (optionnel)": "Default value (optional)",
"Recommandations pour le patient (mode de vie, suivi...)...": "Recommendations for the patient (lifestyle, follow-up...)...",
"Nom du laboratoire": "Laboratory name",
"Nom du patient...": "Patient name...",
"Ex: Afrique, Europe, Amérique du Sud": "E.g. Africa, Europe, South America",
"Dossier Médical": "Medical Record",
"Modifier": "Edit",
"Modifier l'équipe": "Edit team",
"Voir les détails": "View details",
"Supprimer l'équipe": "Delete team",
"Ex: Équipe Première, Réserve, U-17...": "E.g. First Team, Reserves, U-17...",
"Description de la transaction": "Transaction description",
"Notes supplémentaires...": "Additional notes...",
"Auto-généré si vide": "Automatically generated if blank",
"Décrivez les antécédents médicaux...": "Describe the medical history...",
"Listez les allergies...": "List allergies...",
"Listez les médicaments actuels...": "List current medications...",
"Décrivez les conditions spéciales...": "Describe special conditions...",
})
en_file = root / 'resources/lang/en.json'
old = en_file.read_text()
known = json.loads(old)
new = {k:v for k,v in translations.items() if k not in known}
if new:
    items = ',\n'.join('  '+json.dumps(k,ensure_ascii=False)+': '+json.dumps(v,ensure_ascii=False) for k,v in new.items())
    pos = old.rfind('}')
    en_file.write_text(old[:pos].rstrip()+',\n'+items+'\n'+old[pos:])
print('Added',len(new),'English translations')
