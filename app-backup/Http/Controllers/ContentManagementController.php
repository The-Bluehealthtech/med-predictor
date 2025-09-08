<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentManagementController extends Controller
{
    /**
     * Afficher le tableau de bord du Content Management
     */
    public function index()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        // Statistiques du contenu
        $stats = [
            'total_articles' => 0,
            'total_pages' => 0,
            'total_media' => 0,
            'total_categories' => 0,
            'pending_reviews' => 0,
            'published_content' => 0
        ];

        // Types de contenu disponibles
        $contentTypes = [
            'articles' => [
                'name' => 'Articles',
                'description' => 'Articles de presse, actualités, communiqués',
                'icon' => '📰',
                'color' => 'blue'
            ],
            'pages' => [
                'name' => 'Pages',
                'description' => 'Pages statiques, à propos, contact',
                'icon' => '📄',
                'color' => 'green'
            ],
            'media' => [
                'name' => 'Médias',
                'description' => 'Images, vidéos, documents',
                'icon' => '🎬',
                'color' => 'purple'
            ],
            'announcements' => [
                'name' => 'Annonces',
                'description' => 'Annonces officielles, règlements',
                'icon' => '📢',
                'color' => 'orange'
            ],
            'faq' => [
                'name' => 'FAQ',
                'description' => 'Questions fréquemment posées',
                'icon' => '❓',
                'color' => 'indigo'
            ]
        ];

        return view('admin.content-management.index', compact('stats', 'contentTypes'));
    }

    /**
     * Afficher la liste des articles
     */
    public function articles()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $articles = collect([
            [
                'id' => 1,
                'title' => 'Nouvelle saison de football lancée',
                'excerpt' => 'La nouvelle saison de football débute avec de nombreuses équipes participantes...',
                'status' => 'published',
                'author' => 'Admin FIT',
                'created_at' => now()->subDays(2),
                'views' => 1250
            ],
            [
                'id' => 2,
                'title' => 'Règlement des compétitions 2024',
                'excerpt' => 'Découvrez les nouveaux règlements pour les compétitions de cette année...',
                'status' => 'draft',
                'author' => 'Admin FIT',
                'created_at' => now()->subDays(5),
                'views' => 0
            ]
        ]);

        return view('admin.content-management.articles', compact('articles'));
    }

    /**
     * Afficher le guide utilisateur
     */
    public function userGuide()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        // Sections du guide utilisateur
        $guideSections = [
            'introduction' => [
                'title' => 'Introduction au Content Management',
                'description' => 'Découvrez les bases du système de gestion de contenu',
                'icon' => '📚',
                'steps' => [
                    'Accès au Content Management',
                    'Navigation dans l\'interface',
                    'Comprendre les types de contenu'
                ]
            ],
            'articles' => [
                'title' => 'Gestion des Articles',
                'description' => 'Créer, modifier et publier des articles',
                'icon' => '📰',
                'steps' => [
                    'Créer un nouvel article',
                    'Rédiger le contenu',
                    'Définir le statut de publication',
                    'Gérer les articles existants'
                ]
            ],
            'pages' => [
                'title' => 'Gestion des Pages',
                'description' => 'Créer et modifier les pages statiques',
                'icon' => '📄',
                'steps' => [
                    'Créer une nouvelle page',
                    'Définir l\'URL de la page',
                    'Structurer le contenu',
                    'Publier la page'
                ]
            ],
            'media' => [
                'title' => 'Gestion des Médias',
                'description' => 'Uploader et organiser les fichiers multimédias',
                'icon' => '🎬',
                'steps' => [
                    'Uploader des fichiers',
                    'Organiser par catégories',
                    'Optimiser les images',
                    'Gérer l\'espace de stockage'
                ]
            ],
            'announcements' => [
                'title' => 'Gestion des Annonces',
                'description' => 'Créer et diffuser des annonces officielles',
                'icon' => '📢',
                'steps' => [
                    'Créer une annonce',
                    'Définir la priorité',
                    'Programmer la diffusion',
                    'Suivre les statistiques'
                ]
            ],
            'faq' => [
                'title' => 'Gestion de la FAQ',
                'description' => 'Créer et organiser les questions fréquentes',
                'icon' => '❓',
                'steps' => [
                    'Ajouter une question',
                    'Rédiger la réponse',
                    'Catégoriser la FAQ',
                    'Optimiser pour le SEO'
                ]
            ]
        ];

        return view('admin.content-management.user-guide', compact('guideSections'));
    }

    /**
     * Afficher la liste des pages
     */
    public function pages()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        // Simulation de données de pages
        $pages = collect([
            [
                'id' => 1,
                'title' => 'À propos de la FIT',
                'slug' => 'about',
                'status' => 'published',
                'updated_at' => now()->subDays(1)
            ],
            [
                'id' => 2,
                'title' => 'Contact',
                'slug' => 'contact',
                'status' => 'published',
                'updated_at' => now()->subDays(3)
            ],
            [
                'id' => 3,
                'title' => 'Politique de confidentialité',
                'slug' => 'privacy',
                'status' => 'draft',
                'updated_at' => now()->subWeek()
            ]
        ]);

        return view('admin.content-management.pages', compact('pages'));
    }

    /**
     * Afficher la liste des médias
     */
    public function media()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        // Simulation de données de médias
        $media = collect([
            [
                'id' => 1,
                'name' => 'logo-fit.png',
                'type' => 'image',
                'size' => '2.5 MB',
                'uploaded_at' => now()->subDays(1),
                'url' => '/storage/media/logo-fit.png'
            ],
            [
                'id' => 2,
                'name' => 'presentation-video.mp4',
                'type' => 'video',
                'size' => '45.2 MB',
                'uploaded_at' => now()->subDays(3),
                'url' => '/storage/media/presentation-video.mp4'
            ],
            [
                'id' => 3,
                'name' => 'reglement-2024.pdf',
                'type' => 'document',
                'size' => '1.8 MB',
                'uploaded_at' => now()->subWeek(),
                'url' => '/storage/media/reglement-2024.pdf'
            ]
        ]);

        return view('admin.content-management.media', compact('media'));
    }

    /**
     * Afficher la liste des annonces
     */
    public function announcements()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        // Simulation de données d'annonces
        $announcements = collect([
            [
                'id' => 1,
                'title' => 'Ouverture des inscriptions',
                'content' => 'Les inscriptions pour la nouvelle saison sont maintenant ouvertes...',
                'priority' => 'high',
                'status' => 'active',
                'created_at' => now()->subDays(1)
            ],
            [
                'id' => 2,
                'title' => 'Maintenance programmée',
                'content' => 'Une maintenance du système est prévue ce weekend...',
                'priority' => 'medium',
                'status' => 'active',
                'created_at' => now()->subDays(2)
            ],
            [
                'id' => 3,
                'title' => 'Nouveau règlement',
                'content' => 'Le nouveau règlement des compétitions est disponible...',
                'priority' => 'high',
                'status' => 'expired',
                'created_at' => now()->subWeek()
            ]
        ]);

        return view('admin.content-management.announcements', compact('announcements'));
    }

    /**
     * Afficher la liste des FAQ
     */
    public function faq()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        // Simulation de données de FAQ
        $faqs = collect([
            [
                'id' => 1,
                'question' => 'Comment s\'inscrire à une compétition ?',
                'answer' => 'Pour vous inscrire à une compétition, connectez-vous à votre compte et allez dans la section "Compétitions"...',
                'category' => 'inscriptions',
                'status' => 'published',
                'updated_at' => now()->subDays(1)
            ],
            [
                'id' => 2,
                'question' => 'Quels sont les documents requis ?',
                'answer' => 'Les documents requis incluent le certificat médical, la licence de joueur, et une pièce d\'identité...',
                'category' => 'documents',
                'status' => 'published',
                'updated_at' => now()->subDays(3)
            ],
            [
                'id' => 3,
                'question' => 'Comment contacter le support ?',
                'answer' => 'Vous pouvez contacter le support via email à support@fitplatform.com ou par téléphone...',
                'category' => 'support',
                'status' => 'draft',
                'updated_at' => now()->subWeek()
            ]
        ]);

        return view('admin.content-management.faq', compact('faqs'));
    }
}
