#!/usr/bin/env python3
"""Localize reviewed performance, administration and finance screens."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Analyse des performances et métriques": "Performance and Metrics Analysis",
"Analyse avancée des performances, métriques et suivi des athlètes": "Advanced athlete performance analysis, metrics and tracking",
"Métriques en temps réel": "Real-time Metrics",
"Métriques Globales": "Overall Metrics",
"📊 Métriques Globales": "📊 Overall Metrics",
"⚡ Temps Réel": "⚡ Real Time",
"Attaquant - Équipe A": "Forward - Team A",
"Milieu - Équipe B": "Midfielder - Team B",
"Analyse complète des performances du mois": "Complete monthly performance analysis",
"Performance détaillée par athlète": "Detailed performance by athlete",
"Rapport d'Équipe": "Team Report",
"Analyse comparative des équipes": "Team comparison",
"Évolution des performances dans le temps": "Performance trends over time",
"Gestion administrative du système FIT": "FIT System Administration",
"Gérer les comptes utilisateurs, rôles et permissions": "Manage user accounts, roles and permissions",
"Accéder →": "Open →",
"Statistiques Système": "System Statistics",
"Consulter les statistiques et métriques du système": "View system statistics and metrics",
"Approuver ou rejeter les demandes de création de comptes": "Approve or reject account requests",
"Gérer les rôles, permissions et contrôle d'accès": "Manage roles, permissions and access control",
"Paramètres Système": "System Settings",
"Configurer les paramètres et constantes du système": "Configure system settings and constants",
"Comptabilité & Finances": "Accounting & Finance",
"Comptabilité générale non disponible.": "General accounting unavailable.",
"Cette application n'a pas de module de comptabilité générale (revenus, dépenses, budgets, salaires) : aucun logiciel comptable ou compte bancaire n'y est connecté. Les seules données financières réellement enregistrées ci-dessous concernent les": "This application has no general accounting module (revenue, expenses, budgets, payroll): no accounting software or bank account is connected. The only financial data recorded below relates to",
"Dépenses Totales": "Total Expenses",
"Frais de transfert reçus": "Transfer Fees Received",
"Frais de transfert payés": "Transfer Fees Paid",
"Paiements de Transferts Récents": "Recent Transfer Payments",
"Aucun paiement de transfert enregistré pour le moment.": "No transfer payments recorded yet.",
"Voir les rapports détaillés": "View Detailed Reports",
"Gérer les budgets": "Manage Budgets",
"Intégrations": "Integrations",
"Intégrations Bancaires": "Bank Integrations",
"Intégrations API": "API Integrations",
"Intégrations Actives": "Active Integrations",
"Comptabilité professionnelle": "Professional Accounting",
"⚪ Non connecté": "⚪ Not Connected",
"Synchronisation automatique des écritures comptables et des rapports financiers.": "Automatically synchronize accounting entries and financial reports.",
"Gestion financière": "Financial Management",
"Comptabilité cloud": "Cloud Accounting",
"Intégration cloud pour la synchronisation en temps réel.": "Cloud integration for real-time synchronization.",
"Intégration avec les solutions comptables françaises.": "Integration with French accounting solutions.",
"Import et export de données via fichiers Excel et CSV.": "Import and export data using Excel and CSV files.",
"API Personnalisée": "Custom API",
"Intégration sur mesure": "Custom Integration",
"⚪ Non configuré": "⚪ Not Configured",
"Connectez votre propre système via API REST.": "Connect your own system through a REST API.",
"Éléments": "Items",
"Aucune synchronisation n'a encore eu lieu (aucun logiciel comptable n'est connecté).": "No synchronization has occurred yet (no accounting software is connected).",
}
paths = (
'performance/index.blade.php', 'modules/administration/index.blade.php',
'modules/finance/dashboard.blade.php', 'modules/finance/integrations.blade.php',
)
en_file = root / 'resources/lang/en.json'
old = en_file.read_text()
known = json.loads(old)
used = {}
for name in paths:
    file = root / 'resources/views' / name
    source = file.read_text()
    count = [0]
    def replace(match):
        raw = match.group(1)
        label = ' '.join(raw.split())
        if label not in translations or any(marker in raw for marker in ('{{', '@', '[[')):
            return match.group(0)
        used[label] = translations[label]
        count[0] += 1
        return '>' + "{{ __('" + label.replace("'", "\\'") + "') }}" + '<'
    file.write_text(re.sub(r'>([^<>]+)<', replace, source))
    print(name, count[0])
new = {key: value for key, value in used.items() if key not in known}
if new:
    items = ',\n'.join('  ' + json.dumps(key, ensure_ascii=False) + ': ' + json.dumps(value, ensure_ascii=False) for key, value in new.items())
    pos = old.rfind('}')
    en_file.write_text(old[:pos].rstrip() + ',\n' + items + '\n' + old[pos:])
print('New English translations:', len(new))
