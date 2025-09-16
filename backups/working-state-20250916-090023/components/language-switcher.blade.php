<div class="flex items-center space-x-2">
    <a href="{{ url('lang/fr') }}" 
       class="px-2 py-1 rounded border text-sm font-semibold transition-colors duration-200 {{ app()->getLocale() == 'fr' ? 'bg-blue-700 text-white' : 'bg-white text-blue-700 hover:bg-blue-50' }}">
        FR
    </a>
    <a href="{{ url('lang/en') }}" 
       class="px-2 py-1 rounded border text-sm font-semibold transition-colors duration-200 {{ app()->getLocale() == 'en' ? 'bg-blue-700 text-white' : 'bg-white text-blue-700 hover:bg-blue-50' }}">
        EN
    </a>
</div> 