<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Sécurité') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Paramètres de sécurité de votre compte.") }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
                <h3 class="text-sm font-medium text-gray-900">{{ __('Authentification à deux facteurs') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Ajoutez une couche de sécurité supplémentaire à votre compte.') }}</p>
            </div>
            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Configurer') }}
            </button>
        </div>

        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
                <h3 class="text-sm font-medium text-gray-900">{{ __('Sessions actives') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Gérez vos sessions actives sur différents appareils.') }}</p>
            </div>
            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-600 bg-white border-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Voir') }}
            </button>
        </div>

        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
                <h3 class="text-sm font-medium text-gray-900">{{ __('Historique de connexion') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Consultez l\'historique de vos connexions.') }}</p>
            </div>
            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-600 bg-white border-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Voir') }}
            </button>
        </div>
    </div>
</section> 