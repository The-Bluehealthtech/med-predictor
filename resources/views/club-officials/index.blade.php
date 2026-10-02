@extends('layouts.app')

@section('title', 'Dirigeants et staff des clubs')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">L'administration</p>
            <h1 class="text-2xl font-bold text-gray-900">Dirigeants et staff des clubs</h1>
            <p class="text-sm text-gray-600">Fiches des officiels de chaque club au format FIFA Connect : staff technique (TeamOfficial) et dirigeants (OrganisationOfficial).</p>
        </div>
        <a href="{{ route('modules.index') }}" class="text-sm text-blue-600 hover:text-blue-800">← Modules</a>
    </div>
    <section class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500"><tr><th class="px-5 py-2 text-left">Club</th><th class="px-5 py-2 text-left">OrganisationFIFAId</th><th class="px-5 py-2 text-right">Officiels actifs</th><th class="px-5 py-2"></th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($clubs as $club)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ str_replace(' (Démo)', '', $club->name) }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $club->fifa_connect_id ?: '—' }}</td>
                        <td class="px-5 py-3 text-right">{{ (int) $club->officials_count }}</td>
                        <td class="px-5 py-3 text-right"><a href="{{ route('club-officials.club', $club) }}" class="font-medium text-slate-700 hover:text-slate-900">Ouvrir</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-6 text-gray-500">Aucun club dans votre périmètre.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t">{{ $clubs->links() }}</div>
    </section>
</div>
@endsection
