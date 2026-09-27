#!/usr/bin/env python3
"""Localize the Content Management user guide with reviewable English strings."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Introduction au Content Management": "Introduction to Content Management",
"Le système de Content Management de la FIT Platform vous permet de gérer facilement tous les contenus de votre site web. Ce guide vous accompagnera dans l'utilisation de chaque fonctionnalité.": "The FIT Platform Content Management system lets you manage all the content on your website. This guide walks you through each feature.",
"Accès au Content Management": "Accessing Content Management",
"Pour accéder au Content Management, suivez ces étapes :": "To access Content Management, follow these steps:",
"Copie d'écran : Page d'Administration": "Screenshot: Administration Page",
"1. Connectez-vous à votre compte administrateur": "1. Sign in to your administrator account",
'2. Accédez au module "Administration"': '2. Open the "Administration" module',
'3. Cliquez sur la card "Content Management"': '3. Click the "Content Management" card',
"Navigation dans l'interface": "Navigating the interface",
"L'interface du Content Management est organisée en sections claires :": "The Content Management interface is organized into clear sections:",
"Copie d'écran : Dashboard Content Management": "Screenshot: Content Management Dashboard",
"• Statistiques en temps réel": "• Real-time statistics",
"• Types de contenu disponibles": "• Available content types",
"• Actions rapides": "• Quick actions",
"Gestion des Articles": "Article Management",
"Créer un nouvel article": "Create a new article",
"Pour créer un nouvel article, suivez ces étapes :": "To create a new article, follow these steps:",
'Cliquez sur "Nouvel Article" dans le dashboard': 'Click "New Article" on the dashboard',
"Remplissez le titre de l'article": "Enter the article title",
"Rédigez le contenu dans l'éditeur": "Write the content in the editor",
"Ajoutez un extrait (résumé)": "Add an excerpt (summary)",
"Définissez le statut de publication": "Set the publication status",
"Sauvegardez l'article": "Save the article",
"Copie d'écran : Formulaire de création d'article": "Screenshot: Article Creation Form",
"Interface de rédaction avec éditeur de texte riche": "Writing interface with rich text editor",
"Champs : Titre, Contenu, Extrait, Statut": "Fields: Title, Content, Excerpt, Status",
"Gérer les articles existants": "Manage existing articles",
"Dans la liste des articles, vous pouvez :": "In the article list, you can:",
"Copie d'écran : Liste des articles": "Screenshot: Article List",
"• Voir tous les articles avec leur statut": "• View all articles and their status",
"• Modifier un article existant": "• Edit an existing article",
"• Supprimer un article": "• Delete an article",
"• Voir les statistiques (vues, auteur, date)": "• View statistics (views, author, date)",
"Gestion des Pages": "Page Management",
"Créer une nouvelle page": "Create a new page",
'Les pages statiques sont idéales pour le contenu permanent comme "À propos", "Contact", etc.': 'Static pages are ideal for permanent content such as "About" and "Contact".',
"Copie d'écran : Création de page": "Screenshot: Page Creation",
"• Définir l'URL de la page (slug)": "• Set the page URL (slug)",
"• Structurer le contenu avec des sections": "• Organize the content into sections",
"• Ajouter des métadonnées SEO": "• Add SEO metadata",
"Types de pages courantes": "Common page types",
"Page d'accueil": "Home page",
"À propos de la FIT": "About FIT",
"Contact et informations": "Contact and information",
"Politique de confidentialité": "Privacy policy",
"Conditions d'utilisation": "Terms of use",
"Gestion des Médias": "Media Management",
"Uploader des fichiers": "Upload files",
"Le système supporte différents types de fichiers multimédias :": "The system supports different types of media files:",
"Copie d'écran : Upload de médias": "Screenshot: Media Upload",
"• Glisser-déposer des fichiers": "• Drag and drop files",
"• Types supportés : Images, Vidéos, Documents PDF": "• Supported types: Images, Videos, PDF documents",
"• Compression automatique des images": "• Automatic image compression",
"Organiser les médias": "Organize media",
"Organisez vos fichiers par catégories pour faciliter la gestion :": "Organize your files by category to make them easier to manage:",
"Images de compétitions": "Competition images",
"Logos des clubs": "Club logos",
"Documents officiels": "Official documents",
"Vidéos de formation": "Training videos",
"Gestion des Annonces": "Announcement Management",
"Créer une annonce": "Create an announcement",
"Les annonces permettent de communiquer des informations importantes aux utilisateurs.": "Announcements share important information with users.",
"Copie d'écran : Création d'annonce": "Screenshot: Announcement Creation",
"• Définir la priorité (Haute, Moyenne, Basse)": "• Set the priority (High, Medium, Low)",
"• Programmer la diffusion": "• Schedule publication",
"• Ajouter des liens d'action": "• Add action links",
"Types d'annonces": "Announcement types",
"Ouverture des inscriptions": "Registration opening",
"Maintenance programmée": "Scheduled maintenance",
"Nouveaux règlements": "New regulations",
"Événements spéciaux": "Special events",
"Gestion de la FAQ": "FAQ Management",
"Ajouter une question": "Add a question",
"La FAQ aide les utilisateurs à trouver rapidement les réponses à leurs questions.": "The FAQ helps users find answers to their questions quickly.",
"Copie d'écran : Gestion FAQ": "Screenshot: FAQ Management",
"• Formuler des questions claires": "• Write clear questions",
"• Rédiger des réponses détaillées": "• Write detailed answers",
"• Catégoriser par thème": "• Categorize by topic",
"Catégories de FAQ": "FAQ categories",
"Inscriptions et licences": "Registrations and licences",
"Compétitions et matchs": "Competitions and matches",
"Documents requis": "Required documents",
"Support technique": "Technical support",
"Paiements et facturation": "Payments and billing",
"Conseils et Bonnes Pratiques": "Tips and Best Practices",
"Rédigez des titres clairs et descriptifs": "Write clear, descriptive titles",
"Utilisez des images optimisées pour le web": "Use images optimized for the web",
"Vérifiez toujours le contenu avant publication": "Always review content before publication",
"Organisez vos médias par catégories": "Organize your media by category",
"Mettez à jour régulièrement la FAQ": "Update the FAQ regularly",
"Sauvegardez vos modifications fréquemment": "Save your changes frequently",
"Guide Complet avec Copies d'Écran": "Complete Guide with Screenshots",
"Version HTML statique avec captures d'écran détaillées et instructions pas à pas": "Static HTML version with detailed screenshots and step-by-step instructions",
"📖 Ouvrir le Guide Complet": "📖 Open the Complete Guide",
"Besoin d'aide supplémentaire ?": "Need more help?",
"Si vous rencontrez des difficultés ou avez des questions, n'hésitez pas à contacter le support technique.": "If you encounter difficulties or have questions, please contact technical support.",
"📧 Contacter le Support": "📧 Contact Support",
"🏠 Retour au Dashboard": "🏠 Back to Dashboard",
}
file = root / 'resources/views/admin/content-management/user-guide.blade.php'
source = file.read_text()
counts = [0]
def replace(match):
    original = match.group(1)
    label = ' '.join(original.split())
    if label not in translations or '{{' in original or '@' in original:
        return match.group(0)
    counts[0] += 1
    key = label.replace("'", "\\'")
    return '>' + "{{ __('" + key + "') }}" + '<'
source = re.sub(r'>([^<>]+)<', replace, source)
file.write_text(source)
en_file = root / 'resources/lang/en.json'
old = en_file.read_text()
known = json.loads(old)
new = {key: value for key, value in translations.items() if key not in known}
if new:
    items = ',\n'.join('  ' + json.dumps(key, ensure_ascii=False) + ': ' + json.dumps(value, ensure_ascii=False) for key, value in new.items())
    pos = old.rfind('}')
    en_file.write_text(old[:pos].rstrip() + ',\n' + items + '\n' + old[pos:])
print('Guide replacements:', counts[0], 'new English translations:', len(new))
