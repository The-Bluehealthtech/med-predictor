#!/usr/bin/env python3
"""
FIT CLI - Point d'entrée principal
"""

import typer
from rich.console import Console
from rich.table import Table
from rich.panel import Panel
from rich.text import Text

from .config import Config
from .agents.patient import PatientAgent
from .agents.clinician import ClinicianAgent
from .agents.ai import AIAgent

app = typer.Typer(
    name="fit",
    help="FIT CLI - Assistant en Ligne de Commande pour FIT",
    add_completion=False,
    rich_markup_mode="rich"
)

console = Console()

@app.command()
def version():
    """Afficher la version du CLI"""
    from . import __version__
    console.print(f"FIT CLI version {__version__}")

@app.command()
def status():
    """Vérifier le statut de la connexion à FIT"""
    config = Config()
    try:
        # Test de connexion à l'API FIT
        response = config.client.get("/health")
        if response.status_code == 200:
            console.print("✅ [green]Connexion à FIT réussie[/green]")
            console.print(f"🌐 API: {config.base_url}")
            console.print(f"🔑 Authentifié: {'Oui' if config.is_authenticated() else 'Non'}")
        else:
            console.print("❌ [red]Connexion à FIT échouée[/red]")
    except Exception as e:
        console.print(f"❌ [red]Erreur de connexion: {e}[/red]")

@app.command()
def login(
    username: str = typer.Option(..., "--username", "-u", help="Nom d'utilisateur"),
    password: str = typer.Option(..., "--password", "-p", help="Mot de passe"),
    role: str = typer.Option("patient", "--role", "-r", help="Rôle (patient/clinician/admin)")
):
    """Se connecter à FIT"""
    config = Config()
    try:
        token = config.authenticate(username, password, role)
        if token:
            console.print("✅ [green]Connexion réussie[/green]")
            console.print(f"👤 Utilisateur: {username}")
            console.print(f"🎭 Rôle: {role}")
        else:
            console.print("❌ [red]Échec de la connexion[/red]")
    except Exception as e:
        console.print(f"❌ [red]Erreur: {e}[/red]")

@app.command()
def logout():
    """Se déconnecter de FIT"""
    config = Config()
    config.logout()
    console.print("👋 [yellow]Déconnexion réussie[/yellow]")

# Agents
patient_app = typer.Typer(name="patient", help="Agent Patient (Joueur)")
clinician_app = typer.Typer(name="clinician", help="Agent Clinicien")
ai_app = typer.Typer(name="agent", help="Agent IA")

app.add_typer(patient_app, name="patient")
app.add_typer(clinician_app, name="clinician")
app.add_typer(ai_app, name="agent")

# Commandes Patient
@patient_app.command("symptoms")
def patient_symptoms(
    symptoms: str = typer.Argument(..., help="Description des symptômes"),
    severity: str = typer.Option("moderate", "--severity", "-s", help="Sévérité (mild/moderate/severe)"),
    duration: str = typer.Option("", "--duration", "-d", help="Durée des symptômes")
):
    """Déclarer des symptômes"""
    agent = PatientAgent()
    result = agent.declare_symptoms(symptoms, severity, duration)
    console.print(result)

@patient_app.command("appointment")
def patient_appointment(
    action: str = typer.Argument(..., help="Action (request/list/cancel)"),
    date: str = typer.Option(None, "--date", "-d", help="Date du rendez-vous (YYYY-MM-DD)"),
    reason: str = typer.Option("", "--reason", "-r", help="Raison du rendez-vous")
):
    """Gérer les rendez-vous"""
    agent = PatientAgent()
    if action == "request":
        result = agent.request_appointment(date, reason)
    elif action == "list":
        result = agent.list_appointments()
    elif action == "cancel":
        result = agent.cancel_appointment(date)
    else:
        result = "❌ Action non reconnue. Utilisez: request, list, ou cancel"
    
    console.print(result)

@patient_app.command("followup")
def patient_followup():
    """Consulter le suivi des visites"""
    agent = PatientAgent()
    result = agent.get_followup_visits()
    console.print(result)

@patient_app.command("second-opinion")
def patient_second_opinion(
    action: str = typer.Argument(..., help="Action (share/request)")
):
    """Demander une seconde opinion"""
    agent = PatientAgent()
    if action == "share":
        result = agent.share_for_second_opinion()
    elif action == "request":
        result = agent.request_second_opinion()
    else:
        result = "❌ Action non reconnue. Utilisez: share ou request"
    
    console.print(result)

# Commandes Clinicien
@clinician_app.command("visit")
def clinician_visit(
    action: str = typer.Argument(..., help="Action (start/end/list)"),
    player_id: int = typer.Option(None, "--player", "-p", help="ID du joueur")
):
    """Gérer les visites médicales"""
    agent = ClinicianAgent()
    if action == "start":
        result = agent.start_visit(player_id)
    elif action == "end":
        result = agent.end_visit(player_id)
    elif action == "list":
        result = agent.list_visits()
    else:
        result = "❌ Action non reconnue. Utilisez: start, end, ou list"
    
    console.print(result)

@clinician_app.command("diagnosis")
def clinician_diagnosis(
    action: str = typer.Argument(..., help="Action (add/list/update)"),
    player_id: int = typer.Option(None, "--player", "-p", help="ID du joueur"),
    condition: str = typer.Option("", "--condition", "-c", help="Condition diagnostiquée")
):
    """Gérer les diagnostics"""
    agent = ClinicianAgent()
    if action == "add":
        result = agent.add_diagnosis(player_id, condition)
    elif action == "list":
        result = agent.list_diagnoses(player_id)
    elif action == "update":
        result = agent.update_diagnosis(player_id, condition)
    else:
        result = "❌ Action non reconnue. Utilisez: add, list, ou update"
    
    console.print(result)

@clinician_app.command("careplan")
def clinician_careplan(
    action: str = typer.Argument(..., help="Action (create/list/update)"),
    player_id: int = typer.Option(None, "--player", "-p", help="ID du joueur"),
    treatment: str = typer.Option("", "--treatment", "-t", help="Plan de traitement")
):
    """Gérer les plans de soins"""
    agent = ClinicianAgent()
    if action == "create":
        result = agent.create_careplan(player_id, treatment)
    elif action == "list":
        result = agent.list_careplans(player_id)
    elif action == "update":
        result = agent.update_careplan(player_id, treatment)
    else:
        result = "❌ Action non reconnue. Utilisez: create, list, ou update"
    
    console.print(result)

@clinician_app.command("pcma")
def clinician_pcma(
    action: str = typer.Argument(..., help="Action (evaluate/list/update)"),
    player_id: int = typer.Option(None, "--player", "-p", help="ID du joueur")
):
    """Évaluer l'aptitude PCMA"""
    agent = ClinicianAgent()
    if action == "evaluate":
        result = agent.evaluate_pcma(player_id)
    elif action == "list":
        result = agent.list_pcma_evaluations(player_id)
    elif action == "update":
        result = agent.update_pcma_evaluation(player_id)
    else:
        result = "❌ Action non reconnue. Utilisez: evaluate, list, ou update"
    
    console.print(result)

@clinician_app.command("injury")
def clinician_injury(
    action: str = typer.Argument(..., help="Action (report/list/update)"),
    player_id: int = typer.Option(None, "--player", "-p", help="ID du joueur"),
    injury_type: str = typer.Option("", "--type", "-t", help="Type de blessure"),
    severity: str = typer.Option("", "--severity", "-s", help="Sévérité (Grade 1/2/3)")
):
    """Gérer les blessures"""
    agent = ClinicianAgent()
    if action == "report":
        result = agent.report_injury(player_id, injury_type, severity)
    elif action == "list":
        result = agent.list_injuries(player_id)
    elif action == "update":
        result = agent.update_injury(player_id, injury_type, severity)
    else:
        result = "❌ Action non reconnue. Utilisez: report, list, ou update"
    
    console.print(result)

# Commandes Secrétaire
@clinician_app.command("secretary")
def secretary_appointment(
    action: str = typer.Argument(..., help="Action (create/list/update)"),
    player_id: int = typer.Option(None, "--player", "-p", help="ID du joueur"),
    date: str = typer.Option(None, "--date", "-d", help="Date du rendez-vous"),
    reason: str = typer.Option("", "--reason", "-r", help="Raison du rendez-vous")
):
    """Gérer les rendez-vous (Secrétaire)"""
    agent = ClinicianAgent()
    if action == "create":
        result = agent.create_appointment(player_id, date, reason)
    elif action == "list":
        result = agent.list_appointments()
    elif action == "update":
        result = agent.update_appointment(player_id, date, reason)
    else:
        result = "❌ Action non reconnue. Utilisez: create, list, ou update"
    
    console.print(result)

# Commandes Agent IA
@ai_app.command("summarize")
def ai_summarize(
    type: str = typer.Argument(..., help="Type (visit/pcma/consultation)"),
    player_id: int = typer.Option(None, "--player", "-p", help="ID du joueur")
):
    """Générer un résumé automatique"""
    agent = AIAgent()
    result = agent.summarize(type, player_id)
    console.print(result)

@ai_app.command("monitor")
def ai_monitor(
    action: str = typer.Argument(..., help="Action (recovery/caregap/alerts)"),
    player_id: int = typer.Option(None, "--player", "-p", help="ID du joueur")
):
    """Surveiller le suivi médical"""
    agent = AIAgent()
    if action == "recovery":
        result = agent.monitor_recovery(player_id)
    elif action == "caregap":
        result = agent.detect_care_gaps(player_id)
    elif action == "alerts":
        result = agent.check_alerts(player_id)
    else:
        result = "❌ Action non reconnue. Utilisez: recovery, caregap, ou alerts"
    
    console.print(result)

@ai_app.command("message")
def ai_message(
    to: str = typer.Option(..., "--to", "-t", help="Destinataire (player10, clinician, etc.)"),
    text: str = typer.Option(..., "--text", "-m", help="Message à envoyer")
):
    """Envoyer un message contextualisé"""
    agent = AIAgent()
    result = agent.send_message(to, text)
    console.print(result)

@ai_app.command("evidence")
def ai_evidence(
    condition: str = typer.Option(..., "--condition", "-c", help="Condition médicale"),
    player_id: int = typer.Option(None, "--player", "-p", help="ID du joueur")
):
    """Rechercher des preuves médicales"""
    agent = AIAgent()
    result = agent.search_evidence(condition, player_id)
    console.print(result)

if __name__ == "__main__":
    app()
