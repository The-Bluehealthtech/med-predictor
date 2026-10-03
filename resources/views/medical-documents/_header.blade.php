<table class="head"><tr>
    <td class="org">{{ $player->club?->name ?? 'Service médical' }}<br><span class="muted">Service médical — FIT</span></td>
    <td class="doc"><strong>Dr {{ $doctor->name }}</strong>@if($doctor->license_number ?? null)<br><span class="muted">N° d’ordre {{ $doctor->license_number }}</span>@endif
        <br><span class="muted">Le {{ $issuedAt->format('d/m/Y') }}</span></td>
</tr></table>
<h1>{{ $title }}</h1>
<p class="patient">Patient : <strong>{{ trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? '')) ?: $player->name }}</strong>
    @if($player->date_of_birth), né(e) le {{ \Illuminate\Support\Carbon::parse($player->date_of_birth)->format('d/m/Y') }}@endif
    — consultation du {{ $visit->visit_date?->format('d/m/Y') }}</p>
