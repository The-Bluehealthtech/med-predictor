{{-- Bouton de déconnexion des hauts de page. $variant : 'chip' (fond clair, comme la pastille
     utilisateur), 'dark' (en-tête sombre ou coloré). Styles autonomes : fonctionne aussi sur les
     pages qui ne chargent pas Tailwind. --}}
@auth
@if(Route::has('logout'))
@once
<style>
    .fit-logout-form { display: inline-flex; margin: 0; }
    .fit-logout { display: inline-flex; align-items: center; gap: 6px; min-height: 40px; padding: 0 12px; border-radius: 8px; font: 600 13px/1 system-ui, -apple-system, 'Segoe UI', sans-serif; cursor: pointer; transition: background-color .15s, color .15s, border-color .15s; white-space: nowrap; }
    .fit-logout svg { width: 16px; height: 16px; flex: none; }
    .fit-logout:focus-visible { outline: 2px solid #2563eb; outline-offset: 2px; }
    .fit-logout--chip { background: #fff; color: #374151; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,.05); }
    .fit-logout--chip:hover { color: #b91c1c; border-color: #fecaca; background: #fef2f2; }
    .fit-logout--dark { background: rgba(255,255,255,.12); color: #fff; border: 1px solid rgba(255,255,255,.35); }
    .fit-logout--dark:hover { background: rgba(255,255,255,.22); }
    @media (max-width: 640px) { .fit-logout .fit-logout-label { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; } .fit-logout { padding: 0 10px; } }
</style>
@endonce
<form method="POST" action="{{ route('logout') }}" class="fit-logout-form {{ $class ?? '' }}">
    @csrf
    <button type="submit" class="fit-logout fit-logout--{{ $variant ?? 'chip' }}" title="Se déconnecter">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
        <span class="fit-logout-label">Déconnexion</span>
    </button>
</form>
@endif
@endauth
