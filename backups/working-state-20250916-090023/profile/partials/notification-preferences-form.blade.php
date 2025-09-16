<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Préférences de Notification') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Configurez vos préférences de notification.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.settings.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="flex items-center">
            <input id="notifications_email" name="notifications_email" type="checkbox" value="1" 
                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                   {{ old('notifications_email', $user->notifications_email) ? 'checked' : '' }}>
            <x-input-label for="notifications_email" :value="__('Notifications par email')" class="ml-2" />
        </div>

        <div class="flex items-center">
            <input id="notifications_sms" name="notifications_sms" type="checkbox" value="1" 
                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                   {{ old('notifications_sms', $user->notifications_sms) ? 'checked' : '' }}>
            <x-input-label for="notifications_sms" :value="__('Notifications par SMS')" class="ml-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Mettre à jour') }}</x-primary-button>
        </div>
    </form>
</section> 