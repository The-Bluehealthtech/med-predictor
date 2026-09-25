<!-- Logout Button Component -->
<div class="relative">
    @auth
        <form method="POST" action="{{ route('logout') }}" class="inline-block">
            @csrf
            <button type="submit" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105 shadow-lg flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>{{ __('Déconnexion') }}</span>
            </button>
        </form>
    @endauth
</div>

