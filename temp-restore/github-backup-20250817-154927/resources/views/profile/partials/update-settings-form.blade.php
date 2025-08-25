<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Paramètres Généraux') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Configurez vos paramètres généraux et préférences.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.settings.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="timezone" :value="__('Fuseau horaire')" />
            <select id="timezone" name="timezone" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">Sélectionner un fuseau horaire</option>
                <option value="Europe/Paris" {{ old('timezone', $user->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                <option value="UTC" {{ old('timezone', $user->timezone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                <option value="America/New_York" {{ old('timezone', $user->timezone) == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                <option value="Asia/Tokyo" {{ old('timezone', $user->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
        </div>

        <div>
            <x-input-label for="language" :value="__('Langue')" />
            <select id="language" name="language" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="fr" {{ old('language', $user->language) == 'fr' ? 'selected' : '' }}>Français</option>
                <option value="en" {{ old('language', $user->language) == 'en' ? 'selected' : '' }}>English</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('language')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Sauvegarder') }}</x-primary-button>

            @if (session('status') === 'settings-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Sauvegardé.') }}</p>
            @endif
        </div>
    </form>
</section> 