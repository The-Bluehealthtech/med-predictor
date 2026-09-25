@extends('layouts.app')

@section('title', 'Demande de licence joueur')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Demande de licence joueur</h1>
        <p class="text-gray-600 mb-6">
            {{ $player->first_name }} {{ $player->last_name }}
            @if($player->club)
                — {{ $player->club->name }}
            @endif
        </p>

        @if(session('error'))
            <div class="mb-4 rounded bg-red-50 p-3 text-red-700">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('player-licenses.request.store', $player) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Type de licence</label>
                <select name="license_type" required class="mt-1 w-full rounded border-gray-300">
                    <option value="">Sélectionner</option>
                    @foreach([
                        'amateur' => 'Amateur',
                        'professional' => 'Professionnelle',
                        'futsal' => 'Futsal',
                        'beach_soccer' => 'Beach Soccer',
                        'youth' => 'Jeune',
                        'international' => 'Internationale',
                    ] as $value => $label)
                        <option value="{{ $value }}" @selected(old('license_type') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('license_type')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Début</label>
                    <input type="date" name="contract_start_date" value="{{ old('contract_start_date') }}" required class="mt-1 w-full rounded border-gray-300">
                    @error('contract_start_date')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Expiration</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" required class="mt-1 w-full rounded border-gray-300">
                    @error('expiry_date')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" rows="4" class="mt-1 w-full rounded border-gray-300">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                    Créer la demande
                </button>
                <a href="{{ route('modules.licenses.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
