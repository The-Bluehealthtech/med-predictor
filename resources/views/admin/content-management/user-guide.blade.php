@extends('layouts.app')

@section('title', 'Guide Utilisateur - Content Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">📚</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Guide Utilisateur
                                </h1>
                                <p class="text-sm text-gray-600">Guide complet pour utiliser le Content Management</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.content-management.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Content Management</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Table des matières -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Table des Matières</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($guideSections as $key => $section)
                        <a href="#{{ $key }}" class="block p-4 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all">
                            <div class="flex items-center mb-2">
                                <span class="text-2xl mr-3">{{ $section['icon'] }}</span>
                                <h3 class="font-medium text-gray-900">{{ $section['title'] }}</h3>
                            </div>
                            <p class="text-sm text-gray-600">{{ $section['description'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Section Introduction -->
        <div id="introduction" class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">📚</span>
                    <h2 class="text-xl font-bold text-gray-900">Introduction au Content Management</h2>
                </div>
                
                <div class="prose max-w-none">
                    <p class="text-gray-600 mb-4">
                        Le système de Content Management de la FIT Platform vous permet de gérer facilement tous les contenus de votre site web. 
                        Ce guide vous accompagnera dans l'utilisation de chaque fonctionnalité.
                    </p>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Accès au Content Management</h3>
                    <p class="text-gray-600 mb-4">
                        Pour accéder au Content Management, suivez ces étapes :
                    </p>
                    
                    <!-- Copie d'écran simulée -->
                    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6">
                        <div class="text-center">
                            <div class="text-6xl mb-4">🖥️</div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Copie d'écran : Page d'Administration</h4>
                            <p class="text-sm text-gray-500">
                                1. Connectez-vous à votre compte administrateur<br>
                                2. Accédez au module "Administration"<br>
                                3. Cliquez sur la card "Content Management"
                            </p>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Navigation dans l'interface</h3>
                    <p class="text-gray-600 mb-4">
                        L'interface du Content Management est organisée en sections claires :
                    </p>
                    
                    <!-- Copie d'écran simulée -->
                    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6">
                        <div class="text-center">
                            <div class="text-6xl mb-4">📊</div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Copie d'écran : Dashboard Content Management</h4>
                            <p class="text-sm text-gray-500">
                                • Statistiques en temps réel<br>
                                • Types de contenu disponibles<br>
                                • Actions rapides
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Articles -->
        <div id="articles" class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">📰</span>
                    <h2 class="text-xl font-bold text-gray-900">Gestion des Articles</h2>
                </div>
                
                <div class="prose max-w-none">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Créer un nouvel article</h3>
                    <p class="text-gray-600 mb-4">
                        Pour créer un nouvel article, suivez ces étapes :
                    </p>
                    
                    <ol class="list-decimal list-inside text-gray-600 mb-4 space-y-2">
                        <li>Cliquez sur "Nouvel Article" dans le dashboard</li>
                        <li>Remplissez le titre de l'article</li>
                        <li>Rédigez le contenu dans l'éditeur</li>
                        <li>Ajoutez un extrait (résumé)</li>
                        <li>Définissez le statut de publication</li>
                        <li>Sauvegardez l'article</li>
                    </ol>
                    
                    <!-- Copie d'écran simulée -->
                    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6">
                        <div class="text-center">
                            <div class="text-6xl mb-4">✍️</div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Copie d'écran : Formulaire de création d'article</h4>
                            <p class="text-sm text-gray-500">
                                Interface de rédaction avec éditeur de texte riche<br>
                                Champs : Titre, Contenu, Extrait, Statut
                            </p>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Gérer les articles existants</h3>
                    <p class="text-gray-600 mb-4">
                        Dans la liste des articles, vous pouvez :
                    </p>
                    
                    <!-- Copie d'écran simulée -->
                    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6">
                        <div class="text-center">
                            <div class="text-6xl mb-4">📋</div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Copie d'écran : Liste des articles</h4>
                            <p class="text-sm text-gray-500">
                                • Voir tous les articles avec leur statut<br>
                                • Modifier un article existant<br>
                                • Supprimer un article<br>
                                • Voir les statistiques (vues, auteur, date)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Pages -->
        <div id="pages" class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">📄</span>
                    <h2 class="text-xl font-bold text-gray-900">Gestion des Pages</h2>
                </div>
                
                <div class="prose max-w-none">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Créer une nouvelle page</h3>
                    <p class="text-gray-600 mb-4">
                        Les pages statiques sont idéales pour le contenu permanent comme "À propos", "Contact", etc.
                    </p>
                    
                    <!-- Copie d'écran simulée -->
                    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6">
                        <div class="text-center">
                            <div class="text-6xl mb-4">📝</div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Copie d'écran : Création de page</h4>
                            <p class="text-sm text-gray-500">
                                • Définir l'URL de la page (slug)<br>
                                • Structurer le contenu avec des sections<br>
                                • Ajouter des métadonnées SEO
                            </p>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Types de pages courantes</h3>
                    <ul class="list-disc list-inside text-gray-600 mb-4 space-y-1">
                        <li>Page d'accueil</li>
                        <li>À propos de la FIT</li>
                        <li>Contact et informations</li>
                        <li>Politique de confidentialité</li>
                        <li>Conditions d'utilisation</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section Médias -->
        <div id="media" class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">🎬</span>
                    <h2 class="text-xl font-bold text-gray-900">Gestion des Médias</h2>
                </div>
                
                <div class="prose max-w-none">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Uploader des fichiers</h3>
                    <p class="text-gray-600 mb-4">
                        Le système supporte différents types de fichiers multimédias :
                    </p>
                    
                    <!-- Copie d'écran simulée -->
                    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6">
                        <div class="text-center">
                            <div class="text-6xl mb-4">📁</div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Copie d'écran : Upload de médias</h4>
                            <p class="text-sm text-gray-500">
                                • Glisser-déposer des fichiers<br>
                                • Types supportés : Images, Vidéos, Documents PDF<br>
                                • Compression automatique des images
                            </p>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Organiser les médias</h3>
                    <p class="text-gray-600 mb-4">
                        Organisez vos fichiers par catégories pour faciliter la gestion :
                    </p>
                    
                    <ul class="list-disc list-inside text-gray-600 mb-4 space-y-1">
                        <li>Images de compétitions</li>
                        <li>Logos des clubs</li>
                        <li>Documents officiels</li>
                        <li>Vidéos de formation</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section Annonces -->
        <div id="announcements" class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">📢</span>
                    <h2 class="text-xl font-bold text-gray-900">Gestion des Annonces</h2>
                </div>
                
                <div class="prose max-w-none">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Créer une annonce</h3>
                    <p class="text-gray-600 mb-4">
                        Les annonces permettent de communiquer des informations importantes aux utilisateurs.
                    </p>
                    
                    <!-- Copie d'écran simulée -->
                    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6">
                        <div class="text-center">
                            <div class="text-6xl mb-4">📢</div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Copie d'écran : Création d'annonce</h4>
                            <p class="text-sm text-gray-500">
                                • Définir la priorité (Haute, Moyenne, Basse)<br>
                                • Programmer la diffusion<br>
                                • Ajouter des liens d'action
                            </p>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Types d'annonces</h3>
                    <ul class="list-disc list-inside text-gray-600 mb-4 space-y-1">
                        <li>Ouverture des inscriptions</li>
                        <li>Maintenance programmée</li>
                        <li>Nouveaux règlements</li>
                        <li>Événements spéciaux</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section FAQ -->
        <div id="faq" class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">❓</span>
                    <h2 class="text-xl font-bold text-gray-900">Gestion de la FAQ</h2>
                </div>
                
                <div class="prose max-w-none">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Ajouter une question</h3>
                    <p class="text-gray-600 mb-4">
                        La FAQ aide les utilisateurs à trouver rapidement les réponses à leurs questions.
                    </p>
                    
                    <!-- Copie d'écran simulée -->
                    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6">
                        <div class="text-center">
                            <div class="text-6xl mb-4">❓</div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Copie d'écran : Gestion FAQ</h4>
                            <p class="text-sm text-gray-500">
                                • Formuler des questions claires<br>
                                • Rédiger des réponses détaillées<br>
                                • Catégoriser par thème
                            </p>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Catégories de FAQ</h3>
                    <ul class="list-disc list-inside text-gray-600 mb-4 space-y-1">
                        <li>Inscriptions et licences</li>
                        <li>Compétitions et matchs</li>
                        <li>Documents requis</li>
                        <li>Support technique</li>
                        <li>Paiements et facturation</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Conseils et bonnes pratiques -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="text-2xl">💡</span>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-blue-900 mb-2">Conseils et Bonnes Pratiques</h3>
                    <ul class="list-disc list-inside text-blue-800 space-y-1">
                        <li>Rédigez des titres clairs et descriptifs</li>
                        <li>Utilisez des images optimisées pour le web</li>
                        <li>Vérifiez toujours le contenu avant publication</li>
                        <li>Organisez vos médias par catégories</li>
                        <li>Mettez à jour régulièrement la FAQ</li>
                        <li>Sauvegardez vos modifications fréquemment</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Guide HTML statique -->
        <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-3xl mr-4">📖</span>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Guide Complet avec Copies d'Écran</h3>
                            <p class="text-sm text-gray-600">Version HTML statique avec captures d'écran détaillées et instructions pas à pas</p>
                        </div>
                    </div>
                    <a href="/content-management-guide.html" target="_blank" 
                       class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                        📖 Ouvrir le Guide Complet
                    </a>
                </div>
            </div>
        </div>

        <!-- Support -->
        <div class="bg-white shadow rounded-lg mt-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="text-center">
                    <span class="text-4xl mb-4 block">🆘</span>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Besoin d'aide supplémentaire ?</h3>
                    <p class="text-gray-600 mb-4">
                        Si vous rencontrez des difficultés ou avez des questions, n'hésitez pas à contacter le support technique.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="mailto:support@fitplatform.com" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            📧 Contacter le Support
                        </a>
                        <a href="{{ route('admin.content-management.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            🏠 Retour au Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Smooth scrolling pour les ancres
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endsection
