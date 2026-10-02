@extends('layouts.app')

@section('title', 'Notifications - FIT Platform')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header title="Notifications" subtitle="Suivi des demandes et décisions qui vous concernent." eyebrow="FIT" :back-href="route('dashboard')" back-label="Retour au tableau de bord">
        @if(auth()->user()->unreadNotifications()->exists())
            <x-slot:actions>
                <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Tout marquer comme lu</button></form>
            </x-slot:actions>
        @endif
    </x-page-header>

    @if(session('success'))<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('success') }}</div>@endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse($notifications as $notification)
            <a href="{{ route('notifications.open', $notification->id) }}" class="flex items-start gap-3 border-b border-slate-100 px-5 py-3 last:border-0 hover:bg-slate-50">
                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-slate-200' : 'bg-blue-600' }}" aria-hidden="true"></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm {{ $notification->read_at ? 'text-slate-600' : 'font-semibold text-slate-900' }}">{{ $notification->data['message'] ?? 'Notification' }}</span>
                    <span class="block text-xs text-slate-500">{{ $notification->created_at->locale('fr')->diffForHumans() }}{{ $notification->read_at ? '' : ' · non lue' }}</span>
                </span>
            </a>
        @empty
            <p class="px-5 py-8 text-center text-sm text-slate-500">Aucune notification.</p>
        @endforelse
    </div>
    @if($notifications->hasPages())<div class="mt-4">{{ $notifications->links() }}</div>@endif
</div>
@endsection
