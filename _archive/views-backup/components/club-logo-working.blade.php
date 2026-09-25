@php
    // Initialiser les variables
    $logoUrl = null;
    $logoAlt = null;
    $showFallback = $showFallback ?? true;
    $classes = $class ?? 'w-20 h-20';
    
    // Logique pour récupérer le logo du club
    if ($club && $club->name) {
        // Mapping des clubs FTF en Ligue 1 - Basé sur la base de données et worldsoccerpins.com
        $clubMapping = [
            // Noms exacts de la base de données
            'espérance de tunis' => 'EST',
            'étoile du sahel' => 'ESS',
            'club africain' => 'CA',
            'cs sfaxien' => 'CSS',
            'ca bizertin' => 'CAB',
            'stade tunisien' => 'ST',
            'us monastir' => 'USM',
            'olympique béja' => 'OB',
            'as gabès' => 'ASG',
            'js kairouan' => 'JSK',
            
            // Noms complets de worldsoccerpins.com
            'esperance sportive de tunis' => 'EST',
            'etoile sportive du sahel' => 'ESS',
            'club athletique bizertin' => 'CAB',
            'union sportive monastirienne' => 'USM',
            'union sportive de ben guerdane' => 'USBG',
            'avenir sportif de gabes' => 'ASG',
            'etoile sportive de metlaoui' => 'ESM',
            'etoile sportive de zarzis' => 'ESZ',
            'jeunesse sportive de el omrane' => 'JSO',
            'el gawafel sportives de gafsa' => 'EGSG',
            'association sportive de soliman' => 'ASS',
            'union sportive de tataouine' => 'UST',
            
            // Variantes et abréviations
            'est' => 'EST',
            'ess' => 'ESS',
            'ca' => 'CA',
            'css' => 'CSS',
            'cab' => 'CAB',
            'st' => 'ST',
            'usm' => 'USM',
            'usbg' => 'USBG',
            'ob' => 'OB',
            'asg' => 'ASG',
            'esm' => 'ESM',
            'esz' => 'ESZ',
            'jso' => 'JSO',
            'egsg' => 'EGSG',
            'ass' => 'ASS',
            'ust' => 'UST'
        ];
        
        // Chercher le code du club
        $clubCode = null;
        foreach ($clubMapping as $clubName => $code) {
            if (stripos($club->name, $clubName) !== false) {
                $clubCode = $code;
                break;
            }
        }
        
        // Si on a un code, vérifier si le logo WebP existe (priorité) puis PNG
        if ($clubCode) {
            $webpPath = public_path("clubs/{$clubCode}.webp");
            $pngPath = public_path("clubs/{$clubCode}.png");
            
            if (file_exists($webpPath)) {
                $logoUrl = asset("clubs/{$clubCode}.webp");
                $logoAlt = ($club->name ?? 'Club') . ' Logo Officiel';
            } elseif (file_exists($pngPath)) {
                $logoUrl = asset("clubs/{$clubCode}.png");
                $logoAlt = ($club->name ?? 'Club') . ' Logo';
            }
        }
        
        // Fallback : vérifier si le club a un logo personnalisé (avec vérification des propriétés)
        if (!$logoUrl && isset($club->logo) && $club->logo) {
            $logoUrl = asset('storage/' . $club->logo);
            $logoAlt = ($club->name ?? 'Club') . ' Logo';
        } elseif (!$logoUrl && isset($club->club_logo_url) && $club->club_logo_url) {
            $logoUrl = $club->club_logo_url;
            $logoAlt = ($club->name ?? 'Club') . ' Logo';
        }
    }
@endphp

<div class="club-logo-working inline-block">
    @if($logoUrl)
        <img src="{{ $logoUrl }}" 
             alt="{{ $logoAlt }}" 
             class="{{ $classes }}" 
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" 
             onload="this.style.opacity='1';"/>
        <div class="{{ $classes }} flex items-center justify-center bg-gray-200 text-gray-500 text-xs font-medium" style="display: none;">🏟️</div>
    @elseif($showFallback)
        <div class="{{ $classes }} flex items-center justify-center bg-gray-200 text-gray-500 text-xs font-medium">🏟️</div>
    @else
        <div class="{{ $classes }} bg-transparent"></div>
    @endif
</div>

<style>
.club-logo-working {
    position: relative;
    display: inline-block;
}

.club-logo-working img {
    transition: all 0.2s ease-in-out;
    opacity: 0;
}

.club-logo-working img:hover {
    transform: scale(1.05);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Appliquer les gestionnaires d'événements
    document.querySelectorAll('.club-logo-working img').forEach(img => {
        img.style.transition = 'opacity 0.3s ease-in-out';
    });
});
</script>
