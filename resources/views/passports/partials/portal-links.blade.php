{{-- Liens du portail joueur vers ses passeports, affichés seulement si le compte connecté y a droit. --}}
@php
    $passportAccess = app(\App\Services\Passports\PassportAccess::class);
    $portalUser = auth()->user();
    $portalPlayer = isset($player) && $player instanceof \App\Models\Player ? $player : null;
@endphp
@if($portalUser && $portalPlayer)
    <div class="flex flex-wrap items-center gap-2" data-passport-links>
        @if($passportAccess->canViewMedical($portalUser, $portalPlayer))
            <a href="{{ route('passports.medical.show', ['player' => $portalPlayer->id, 'purpose' => $portalUser->isPlayer() ? 'player_share' : 'general']) }}"
               class="inline-flex items-center gap-2 rounded-lg bg-red-700 px-3 py-2 text-sm font-semibold text-white hover:bg-red-800">
                @include('modules.partials.icon', ['name' => 'passport', 'class' => 'w-4 h-4'])
                {{ $portalUser->isPlayer() ? 'Mon passeport médical' : 'Passeport médical (IPS)' }}
            </a>
        @endif
        @if($passportAccess->canViewTransfer($portalUser, $portalPlayer))
            <a href="{{ route('passports.transfer.show', $portalPlayer->id) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-600 px-3 py-2 text-sm font-semibold text-gray-200 hover:bg-gray-700">
                @include('modules.partials.icon', ['name' => 'passport', 'class' => 'w-4 h-4'])
                {{ $portalUser->isPlayer() ? 'Mon passeport de transfert' : 'Passeport de transfert' }}
            </a>
        @endif
    </div>
@endif
