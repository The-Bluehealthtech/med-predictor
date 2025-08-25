<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Med Predictor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Med Predictor</h1>
            <p class="text-blue-200">Connexion sécurisée</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/20 border border-red-500/50 rounded-lg p-4 mb-6">
                <ul class="text-red-200 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            <div>
                <label for="user_type" class="block text-sm font-medium text-blue-200 mb-2">
                    Type d'utilisateur
                </label>
                <select name="user_type" id="user_type" required 
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Sélectionner...</option>
                    <option value="player">Joueur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-blue-200 mb-2">
                    Email
                </label>
                <input type="email" name="email" id="email" required 
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="votre@email.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-blue-200 mb-2">
                    Mot de passe
                </label>
                <input type="password" name="password" id="password" required 
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="••••••••">
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200 transform hover:scale-105">
                Se connecter
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-blue-200 text-sm">
                <strong>Joueurs :</strong> Utilisez votre email et mot de passe<br>
                <strong>Admin :</strong> Accès complet au système
            </p>
        </div>

        <div class="mt-6 text-center">
            <a href="/joueur/7" class="text-blue-300 hover:text-blue-200 text-sm underline">
                Accès direct (démo)
            </a>
        </div>
    </div>

    <script>
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.style.opacity = '0';
            form.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                form.style.transition = 'all 0.6s ease-out';
                form.style.opacity = '1';
                form.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
