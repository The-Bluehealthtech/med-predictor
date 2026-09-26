<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // NOTE (audit factice -> reel, 2026-09) : cette application n'a pas de
        // modele "Article"/"Page"/"Annonce"/"FAQ" reel (voir plus bas). Seuls
        // les medias (logos, images, documents...) sont reellement stockes,
        // dans la table content_management. Les autres compteurs restent a 0
        // car aucun contenu reel de ce type n'existe.
        $stats = [
            'total_articles' => 0,
            'total_pages' => 0,
            'total_media' => DB::table('content_management')->count(),
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

        // NOTE (audit factice -> reel, 2026-09) : aucun modele "Article" reel
        // n'existe dans l'application. Les 2 articles precedemment affiches
        // ici ("Nouvelle saison de football lancee", "Reglement des
        // competitions 2024") etaient des exemples codes en dur, identiques
        // pour tout le monde. Ils ont ete retires ; la liste est reellement
        // vide en l'absence de module d'articles.
        $articles = collect();

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

        // NOTE (audit factice -> reel, 2026-09) : aucun modele "Page" reel
        // n'existe dans l'application. Les 3 pages precedemment affichees ici
        // etaient des exemples codes en dur ("A propos de la FIT", "Contact",
        // "Politique de confidentialite"). Ils ont ete retires ; la liste est
        // reellement vide en l'absence de module de pages statiques.
        $pages = collect();

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

        // Les medias sont la seule categorie de "Content Management" a avoir
        // une vraie table (content_management). On l'utilise directement,
        // sans modele Eloquent dedie.
        $media = DB::table('content_management')
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($row) {
                $mime = $row->mime_type ?? '';
                if (str_starts_with($mime, 'image/')) {
                    $type = 'image';
                } elseif (str_starts_with($mime, 'video/')) {
                    $type = 'video';
                } else {
                    $type = 'document';
                }

                return [
                    'id' => $row->id,
                    'name' => $row->name,
                    'type' => $type,
                    'size' => $this->formatFileSize((int) $row->file_size),
                    'uploaded_at' => Carbon::parse($row->created_at),
                    'url' => $row->file_url,
                ];
            });

        return view('admin.content-management.media', compact('media'));
    }

    /**
     * Formate une taille de fichier en octets vers une chaîne lisible.
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = (int) floor(log($bytes, 1024));
        $power = max(0, min($power, count($units) - 1));

        return round($bytes / (1024 ** $power), 1) . ' ' . $units[$power];
    }

    /**
     * Afficher la liste des annonces
     */
    public function announcements()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        // NOTE (audit factice -> reel, 2026-09) : aucun modele "Annonce" reel
        // n'existe dans l'application. Les 3 annonces precedemment affichees
        // ici etaient des exemples codes en dur. Ils ont ete retires ; la
        // liste est reellement vide en l'absence de module d'annonces.
        $announcements = collect();

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

        // NOTE (audit factice -> reel, 2026-09) : aucun modele "FAQ" reel
        // n'existe dans l'application. Les 3 questions precedemment affichees
        // ici etaient des exemples codes en dur. Ils ont ete retires ; la
        // liste est reellement vide en l'absence de module de FAQ.
        $faqs = collect();

        return view('admin.content-management.faq', compact('faqs'));
    }

    /**
     * Formulaire de creation de contenu.
     *
     * NOTE (audit factice -> reel, 2026-09) : ces routes (create/store/edit/
     * update/destroy) etaient enregistrees dans routes/web.php mais leurs
     * methodes n'existaient pas du tout sur ce controleur : y acceder
     * provoquait une erreur serveur (methode inexistante). Comme aucun
     * module reel d'articles/pages/annonces/FAQ n'existe (voir ci-dessus),
     * on redirige desormais avec un message honnete plutot que de laisser
     * la page planter.
     */
    public function create(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $type = $request->query('type', 'contenu');

        return redirect()->route('admin.content-management.index')
            ->with('info', "La création de contenu (type : {$type}) n'est pas encore disponible : aucun module de gestion de ce type de contenu n'est connecté.");
    }

    /**
     * Enregistrement de contenu (non disponible, voir create()).
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        return redirect()->route('admin.content-management.index')
            ->with('info', "L'enregistrement de contenu n'est pas encore disponible.");
    }

    /**
     * Formulaire d'edition de contenu (non disponible, voir create()).
     */
    public function edit($id, Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $type = $request->query('type', 'contenu');

        return redirect()->route('admin.content-management.index')
            ->with('info', "La modification de contenu (type : {$type}) n'est pas encore disponible.");
    }

    /**
     * Mise a jour de contenu (non disponible, voir create()).
     */
    public function update($id, Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        return redirect()->route('admin.content-management.index')
            ->with('info', "La mise à jour de contenu n'est pas encore disponible.");
    }

    /**
     * Suppression de contenu (non disponible, voir create()).
     */
    public function destroy($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'content_manager'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        return redirect()->route('admin.content-management.index')
            ->with('info', "La suppression de contenu n'est pas encore disponible.");
    }
}
