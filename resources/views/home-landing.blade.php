<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ app()->getLocale() === 'en' ? 'FIT Platform - Holistic Player Monitoring' : 'FIT Platform - Suivi Holistique des Joueurs' }}</title>

    <!-- Tailwind CSS (CDN script - restored 2026-09-25 after two attempts to replace it broke
         the site: the shared Vite public-build bundle is a stale (over a year old) build missing
         classes this page uses (e.g. w-8/h-8 caused an oversized header logo), and a page-specific
         tailwindcss CLI build caused an unrelated 500 server error in production. Reverted to the
         known-working CDN script. IMPORTANT: never write the literal directive name for the Vite
         asset helper inside an HTML comment in a .blade.php file - Blade scans the whole file for
         directive tokens regardless of HTML comments, so it gets compiled as a real zero-argument
         call and throws ArgumentCountError at runtime. This exact mistake caused a production
         outage on 2026-09-25; keep this note as a warning, written safely without the token. -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    <style>
        body {
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
        }
        .font-display {
            font-family: Cambria, Georgia, serif;
        }
        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px -15px rgba(15, 23, 42, 0.25);
        }
    </style>
</head>
<body class="antialiased bg-white">
    <!-- Navigation -->
    <header class="flex items-center justify-between px-6 md:px-16 py-4 border-b border-[#E2E8F0] bg-white">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/the-fit-logo.png') }}" alt="The FIT Logo" class="w-9 h-9 rounded-lg object-contain">
            <span class="font-display text-lg font-bold text-[#0F172A]">FIT Platform</span>
        </div>

        <nav class="hidden md:flex items-center gap-9">
            <a href="#fonctionnalites" class="text-sm font-semibold text-[#0F172A] hover:text-[#2563EB] transition-colors">{{ app()->getLocale() === 'en' ? 'Features' : 'Fonctionnalités' }}</a>
            <a href="#securite" class="text-sm font-semibold text-[#0F172A] hover:text-[#2563EB] transition-colors">{{ app()->getLocale() === 'en' ? 'Security' : 'Sécurité' }}</a>
        </nav>

        <div class="flex items-center gap-3">
            <x-language-switcher />

            @if (Route::has('login'))
                @auth
                    <a href="{{ route('login') }}" class="bg-[#2563EB] text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-[#3B82F6] transition-colors">
                        Connexion
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-[#0F172A] hover:text-[#2563EB] px-4 py-2 rounded-md text-sm font-semibold transition-colors">
                        Connexion
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-[#2563EB] text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-[#3B82F6] transition-colors">
                            Inscription
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-[#0F172A] px-6 md:px-16 pt-14 pb-14 md:pt-20 md:pb-20">
        <div class="max-w-5xl mx-auto flex flex-col items-start gap-6">

            <div class="inline-flex items-center gap-2 bg-[#1E293B] rounded-full pl-3.5 pr-4 py-2">
                <svg class="w-4 h-4 text-[#60A5FA]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><ellipse cx="12" cy="12" rx="4" ry="9"></ellipse><line x1="3" y1="12" x2="21" y2="12"></line></svg>
                <span class="text-[#93C5FD] text-xs font-bold tracking-wider">{{ app()->getLocale() === 'en' ? 'FIFA DATA STANDARDS' : "CONNECTÉ À L'ÉCOSYSTÈME FIFA" }}</span>
            </div>

            <h1 class="font-display text-4xl md:text-6xl leading-tight text-white max-w-3xl">
                Suivi holistique des joueurs de football.
            </h1>

            <p class="text-lg text-[#CBD5E1] max-w-2xl leading-relaxed">
                Performance, santé et gouvernance réunies sur une plateforme unique — pensée pour les clubs, associations et staffs médicaux.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 mt-2">
                @if (Route::has('login'))
                    @auth
                        <a href="/account-request" class="inline-flex items-center justify-center gap-2 bg-[#2563EB] text-white px-7 py-3.5 rounded-lg text-base font-bold hover:bg-[#3B82F6] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Demander un Compte
                        </a>
                    @else
                        <a href="/account-request" class="inline-flex items-center justify-center gap-2 bg-[#2563EB] text-white px-7 py-3.5 rounded-lg text-base font-bold hover:bg-[#3B82F6] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Demander un Compte
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-transparent text-white px-7 py-3.5 rounded-lg text-base font-bold border border-[#334155] hover:border-[#64748B] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                S'inscrire
                            </a>
                        @endif
                    @endauth
                @endif
                <a href="#fonctionnalites" class="inline-flex items-center justify-center gap-2 bg-transparent text-white px-7 py-3.5 rounded-lg text-base font-bold border border-[#334155] hover:border-[#64748B] transition-colors">
                    Découvrir les modules
                </a>
            </div>

        </div>
    </section>

    <!-- Domaines / modules -->
    <section id="fonctionnalites" class="px-6 md:px-16 py-20 md:py-24 bg-white">
        <div class="max-w-6xl mx-auto">
            <span class="text-[#2563EB] text-xs font-bold tracking-wider">ARCHITECTURE</span>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-[#0F172A] mt-2 mb-2">{{ app()->getLocale() === 'en' ? 'One platform for football operations' : 'Une plateforme pour les métiers du football' }}</h2>
            <p class="text-[#64748B] max-w-xl mb-10 md:mb-12">{{ app()->getLocale() === 'en' ? 'Explore each area according to your role and needs.' : 'Un accès organisé par domaine métier, pour que chaque profil retrouve exactement ce dont il a besoin.' }}</p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">

                <div class="card-hover bg-[#F1F5F9] rounded-xl p-6 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#0F172A] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7.5l3.2 2.3-1.2 3.7h-4l-1.2-3.7z"></path><path d="M12 3v4.5M6.2 9.3L3.5 8.5M6.2 14.7l-2 2.6M17.8 9.3l2.7-.8M17.8 14.7l2 2.6"></path></svg>
                    </span>
                    <span class="text-sm font-bold text-[#0F172A]">{{ app()->getLocale() === 'en' ? 'Football management' : 'Gestion du Football' }}</span>
                    <span class="text-xs font-bold text-[#2563EB]">{{ app()->getLocale() === 'en' ? 'Modules' : 'Modules' }}</span>
                </div>

                <div class="card-hover bg-[#F1F5F9] rounded-xl p-6 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#0F172A] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h4l2-6 4 12 2-6h4l2 3h4"></path></svg>
                    </span>
                    <span class="text-sm font-bold text-[#0F172A]">{{ app()->getLocale() === 'en' ? 'Health & medicine' : 'Santé & Médecine' }}</span>
                    <span class="text-xs font-bold text-[#2563EB]">{{ app()->getLocale() === 'en' ? 'Modules' : 'Modules' }}</span>
                </div>

                <div class="card-hover bg-[#F1F5F9] rounded-xl p-6 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#0F172A] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l5-6 4 3 7-9"></path><circle cx="8" cy="11" r="1.1" fill="#ffffff" stroke="none"></circle><circle cx="12" cy="14" r="1.1" fill="#ffffff" stroke="none"></circle><circle cx="19" cy="5" r="1.1" fill="#ffffff" stroke="none"></circle></svg>
                    </span>
                    <span class="text-sm font-bold text-[#0F172A]">{{ app()->getLocale() === 'en' ? 'Analytics & performance' : 'Analytics & Performance' }}</span>
                    <span class="text-xs font-bold text-[#2563EB]">{{ app()->getLocale() === 'en' ? 'Modules' : 'Modules' }}</span>
                </div>

                <div class="card-hover bg-[#F1F5F9] rounded-xl p-6 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#0F172A] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="6" width="12" height="12" rx="3"></rect><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"></path></svg>
                    </span>
                    <span class="text-sm font-bold text-[#0F172A]">{{ app()->getLocale() === 'en' ? 'AI & technology' : 'IA & Technologie' }}</span>
                    <span class="text-xs font-bold text-[#2563EB]">{{ app()->getLocale() === 'en' ? 'Modules' : 'Modules' }}</span>
                </div>

                <div class="card-hover bg-[#F1F5F9] rounded-xl p-6 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#0F172A] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="1"></rect><line x1="8" y1="7" x2="10" y2="7"></line><line x1="14" y1="7" x2="16" y2="7"></line><line x1="8" y1="11" x2="10" y2="11"></line><line x1="14" y1="11" x2="16" y2="11"></line><line x1="8" y1="15" x2="10" y2="15"></line><line x1="14" y1="15" x2="16" y2="15"></line></svg>
                    </span>
                    <span class="text-sm font-bold text-[#0F172A]">{{ app()->getLocale() === 'en' ? 'Organizations' : 'Organisations' }}</span>
                    <span class="text-xs font-bold text-[#2563EB]">{{ app()->getLocale() === 'en' ? 'Modules' : 'Modules' }}</span>
                </div>

                <div class="card-hover bg-[#F1F5F9] rounded-xl p-6 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#0F172A] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="12" rx="2"></rect><circle cx="8" cy="12" r="2"></circle><line x1="13" y1="10" x2="18" y2="10"></line><line x1="13" y1="14" x2="17" y2="14"></line></svg>
                    </span>
                    <span class="text-sm font-bold text-[#0F172A]">{{ app()->getLocale() === 'en' ? 'Licences & documents' : 'Licences & Documents' }}</span>
                    <span class="text-xs font-bold text-[#2563EB]">{{ app()->getLocale() === 'en' ? 'Modules' : 'Modules' }}</span>
                </div>

                <div class="card-hover bg-[#F1F5F9] rounded-xl p-6 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#0F172A] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><ellipse cx="12" cy="12" rx="4" ry="9"></ellipse><line x1="3" y1="12" x2="21" y2="12"></line></svg>
                    </span>
                    <span class="text-sm font-bold text-[#0F172A]">{{ app()->getLocale() === 'en' ? 'Portals & connectivity' : 'Portails & Connectivité' }}</span>
                    <span class="text-xs font-bold text-[#2563EB]">{{ app()->getLocale() === 'en' ? 'Modules' : 'Modules' }}</span>
                </div>

                <div class="card-hover bg-[#F1F5F9] rounded-xl p-6 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#0F172A] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="12" rx="2"></rect><path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="3" y1="13" x2="21" y2="13"></line></svg>
                    </span>
                    <span class="text-sm font-bold text-[#0F172A]">Administration</span>
                    <span class="text-xs font-bold text-[#2563EB]">{{ app()->getLocale() === 'en' ? 'Modules' : 'Modules' }}</span>
                </div>

            </div>
        </div>
    </section>

    <!-- Conformité & sécurité -->
    <section id="securite" class="px-6 md:px-16 py-20 md:py-24 bg-[#0F172A]">
        <div class="max-w-6xl mx-auto">
            <span class="text-[#93C5FD] text-xs font-bold tracking-wider">{{ app()->getLocale() === 'en' ? 'STANDARDS & SECURITY' : 'CONFORMITÉ & SÉCURITÉ' }}</span>
            <h2 class="font-display text-xl md:text-2xl font-bold text-white mt-2 mb-10 md:mb-12 max-w-xl">{{ app()->getLocale() === 'en' ? 'Designed for institutional football' : 'Pensée pour les exigences du football institutionnel' }}</h2>

            <div class="grid md:grid-cols-3 gap-6">

                <div class="bg-[#1E293B] rounded-xl p-8 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#2563EB] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><ellipse cx="12" cy="12" rx="4" ry="9"></ellipse><line x1="3" y1="12" x2="21" y2="12"></line></svg>
                    </span>
                    <span class="font-display text-base font-bold text-white">{{ app()->getLocale() === 'en' ? 'FIFA Connect standards' : 'Standards FIFA Connect' }}</span>
                    <span class="text-sm text-[#CBD5E1] leading-relaxed">{{ app()->getLocale() === 'en' ? 'Data structures for players, clubs and associations based on FIFA standards.' : 'Structure de données alignée sur les standards FIFA pour les joueurs, clubs et associations.' }}</span>
                </div>

                <div class="bg-[#1E293B] rounded-xl p-8 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#2563EB] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 5-3.5 7.5-7 9-3.5-1.5-7-4-7-9V6z"></path></svg>
                    </span>
                    <span class="font-display text-base font-bold text-white">{{ app()->getLocale() === 'en' ? 'Roles & access' : 'Gestion des rôles & accès' }}</span>
                    <span class="text-sm text-[#CBD5E1] leading-relaxed">{{ app()->getLocale() === 'en' ? 'Dedicated access for administrators, medical staff, clubs, players and referees.' : 'Comptes dédiés par profil : administrateur, staff médical, club, joueur, arbitre.' }}</span>
                </div>

                <div class="bg-[#1E293B] rounded-xl p-8 flex flex-col gap-4">
                    <span class="w-12 h-12 rounded-full bg-[#2563EB] flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
                    </span>
                    <span class="font-display text-base font-bold text-white">{{ app()->getLocale() === 'en' ? 'Protected health records' : 'Données de santé protégées' }}</span>
                    <span class="text-sm text-[#CBD5E1] leading-relaxed">{{ app()->getLocale() === 'en' ? 'Medical records are restricted to authorized profiles.' : 'Dossiers médicaux cloisonnés, accessibles uniquement aux profils autorisés.' }}</span>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="px-6 md:px-16 py-20 bg-[#2563EB]">
        <div class="max-w-2xl mx-auto flex flex-col items-center text-center gap-4">
            <h2 class="font-display text-2xl md:text-3xl font-bold text-white">{{ app()->getLocale() === 'en' ? 'Ready to support your players?' : 'Prêt à optimiser les performances de vos joueurs ?' }}</h2>
            <p class="text-[#DBEAFE] max-w-md">{{ app()->getLocale() === 'en' ? 'Use FIT Platform to manage your players in one place.' : 'Rejoignez les clubs qui font confiance à FIT Platform pour le suivi holistique de leurs joueurs.' }}</p>
            <div class="flex flex-col sm:flex-row gap-4 mt-2">
                @if (Route::has('login'))
                    @auth
                        <a href="/account-request" class="inline-flex items-center justify-center gap-2 bg-white text-[#2563EB] px-8 py-3.5 rounded-lg text-base font-bold hover:bg-[#F1F5F9] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Demander un Compte
                        </a>
                    @else
                        <a href="/account-request" class="inline-flex items-center justify-center gap-2 bg-white text-[#2563EB] px-8 py-3.5 rounded-lg text-base font-bold hover:bg-[#F1F5F9] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Demander un Compte
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-transparent text-white px-8 py-3.5 rounded-lg text-base font-bold border-2 border-white hover:bg-white/10 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                S'inscrire
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#0F172A] text-white py-16 px-6 md:px-16">
        <div class="max-w-6xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/the-fit-logo.png') }}" alt="The FIT Logo" class="w-9 h-9 rounded-lg object-contain">
                        <span class="font-display text-lg font-bold">FIT Platform</span>
                    </div>
                    <p class="text-[#94A3B8] text-sm leading-relaxed">
                        La solution complète pour le suivi holistique des joueurs de football.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-[#93C5FD] uppercase tracking-wider mb-5">{{ app()->getLocale() === 'en' ? 'Product' : 'Produit' }}</h4>
                    <ul class="space-y-3">
                        <li><a href="#fonctionnalites" class="text-[#94A3B8] hover:text-white transition-colors text-sm">{{ app()->getLocale() === 'en' ? 'Features' : 'Fonctionnalités' }}</a></li>
                        <!-- Tarifs / API : pages pas encore créées. Liste en texte neutre plutôt
                             qu'un lien mort tant que ces pages n'existent pas. -->
                        <li><span class="text-[#475569] text-sm cursor-default">Tarifs <span class="text-xs align-middle">{{ app()->getLocale() === 'en' ? '(coming soon)' : '(bientôt)' }}</span></span></li>
                        <li><span class="text-[#475569] text-sm cursor-default">API <span class="text-xs align-middle">{{ app()->getLocale() === 'en' ? '(coming soon)' : '(bientôt)' }}</span></span></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-[#93C5FD] uppercase tracking-wider mb-5">Support</h4>
                    <ul class="space-y-3">
                        <!-- Documentation / Aide / Contact : pas encore de page dédiée. -->
                        <li><span class="text-[#475569] text-sm cursor-default">Documentation <span class="text-xs align-middle">{{ app()->getLocale() === 'en' ? '(coming soon)' : '(bientôt)' }}</span></span></li>
                        <li><span class="text-[#475569] text-sm cursor-default">Aide <span class="text-xs align-middle">{{ app()->getLocale() === 'en' ? '(coming soon)' : '(bientôt)' }}</span></span></li>
                        <li><span class="text-[#475569] text-sm cursor-default">Contact <span class="text-xs align-middle">{{ app()->getLocale() === 'en' ? '(coming soon)' : '(bientôt)' }}</span></span></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-[#93C5FD] uppercase tracking-wider mb-5">{{ app()->getLocale() === 'en' ? 'Legal' : 'Légal' }}</h4>
                    <ul class="space-y-3">
                        <!-- Pages légales : à créer avant mise en avant publique (mentions
                             obligatoires) — laissées en texte neutre pour ne pas laisser croire
                             qu'elles existent déjà. -->
                        <li><span class="text-[#475569] text-sm cursor-default">Confidentialité <span class="text-xs align-middle">{{ app()->getLocale() === 'en' ? '(coming soon)' : '(bientôt)' }}</span></span></li>
                        <li><span class="text-[#475569] text-sm cursor-default">Conditions <span class="text-xs align-middle">{{ app()->getLocale() === 'en' ? '(coming soon)' : '(bientôt)' }}</span></span></li>
                        <li><span class="text-[#475569] text-sm cursor-default">Cookies <span class="text-xs align-middle">{{ app()->getLocale() === 'en' ? '(coming soon)' : '(bientôt)' }}</span></span></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-[#1E293B] mt-12 pt-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center">
                        {{-- Le fichier images/logos/the-blue-healthtech-logo.png existe mais fait
                             0 octet (image jamais uploadée) : l'<img> cassait le rendu (icône
                             d'image brisée). Retiré en attendant le vrai fichier logo ; remettre
                             <img src="{{ asset('images/logos/the-blue-healthtech-logo.png') }}"
                                  alt="The Blue Healthtech" class="h-8 mr-3">
                             dès qu'il sera fourni. --}}
                        <span class="text-[#94A3B8] text-sm">{{ app()->getLocale() === 'en' ? 'Developed by The Blue Healthtech' : 'Développé par The Blue Healthtech' }}</span>
                    </div>
                    <p class="text-[#94A3B8] text-sm">&copy; 2025 The Blue Healthtech Ltd. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
