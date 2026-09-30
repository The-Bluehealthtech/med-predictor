@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">{{ __('Profil') }}</h1>
    @if(session('success'))<p>{{ session('success') }}</p>@endif
    <form method="POST" action="{{ route('player-portal.update-profile') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf @method('PUT')
        <label class="block">{{ __('Adresse') }}<textarea name="address" class="block w-full border rounded">{{ old('address', $player->address) }}</textarea></label>
        <label class="block">{{ __('Email') }}<input type="email" name="contact_email" value="{{ old('contact_email', $player->contact_email) }}" class="block w-full border rounded"></label>
        <label class="block">{{ __('Téléphone') }}<input name="contact_phone" value="{{ old('contact_phone', $player->contact_phone) }}" class="block w-full border rounded"></label>
        @foreach($errors->all() as $error)<p class="text-red-700">{{ $error }}</p>@endforeach
        <button class="bg-blue-600 text-white rounded px-4 py-2">{{ __('Enregistrer') }}</button>
    </form>
</div>
@endsection
