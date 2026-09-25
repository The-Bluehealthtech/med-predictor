<form method="POST" action="{{ route('language.update') }}" aria-label="{{ __('dashboard_test.language') }}">
    @csrf
    <label for="fit-language" class="sr-only">{{ __('dashboard_test.language') }}</label>
    <select id="fit-language" name="locale" onchange="this.form.submit()" class="rounded border border-gray-300 bg-white px-2 py-1 text-sm text-gray-900">
        <option value="fr" @selected(app()->getLocale() === 'fr')>Français</option>
        <option value="en" @selected(app()->getLocale() === 'en')>English</option>
    </select>
</form>
