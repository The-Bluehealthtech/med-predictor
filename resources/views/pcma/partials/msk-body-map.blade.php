<div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
  <div class="flex items-start justify-between"><div><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Examen musculo-squelettique</p><h4 class="mt-1 font-semibold text-slate-900">Body map anatomique</h4></div><span class="text-xs text-slate-500">Antérieur / postérieur · D/G</span></div>
  <svg viewBox="0 0 620 420" class="mt-3 w-full" role="img" aria-label="Body map musculo-squelettique antérieure et postérieure">
    <defs><style>.body{fill:#fff7ed;stroke:#9a3412;stroke-width:2}.joint{fill:#fed7aa;stroke:#c2410c;stroke-width:1.5}.axis{stroke:#cbd5e1;stroke-dasharray:4 4}</style></defs>
    <line x1="310" y1="25" x2="310" y2="395" class="axis"/>
    @foreach([['x'=>155,'label'=>'ANTÉRIEUR'],['x'=>465,'label'=>'POSTÉRIEUR']] as $figure)
      <g transform="translate({{ $figure['x']-100 }},42)">
        <circle cx="100" cy="35" r="26" class="body"/><path d="M91 61h18l8 20 35 16-8 18-29-10-4 77-11 44H83l-11-44-4-77-29 10-8-18 35-16 8-20h17z" class="body"/>
        <path d="M82 226l-9 91-12 55h22l17-107 17 107h22l-12-55-9-91z" class="body"/>
        <circle cx="44" cy="105" r="7" class="joint"/><circle cx="156" cy="105" r="7" class="joint"/><circle cx="31" cy="168" r="7" class="joint"/><circle cx="169" cy="168" r="7" class="joint"/><circle cx="82" cy="232" r="7" class="joint"/><circle cx="118" cy="232" r="7" class="joint"/><circle cx="77" cy="303" r="7" class="joint"/><circle cx="123" cy="303" r="7" class="joint"/><circle cx="66" cy="366" r="7" class="joint"/><circle cx="134" cy="366" r="7" class="joint"/>
      </g>
      <text x="{{ $figure['x'] }}" y="20" text-anchor="middle" fill="#334155" font-size="12" font-weight="700">{{ $figure['label'] }}</text>
    @endforeach
    <g fill="#64748b" font-size="11" font-family="sans-serif"><text x="38" y="407">D</text><text x="262" y="407">G</text><text x="348" y="407">G</text><text x="572" y="407">D</text></g>
  </svg>
  <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-slate-600 sm:grid-cols-4"><span>Rachis / bassin</span><span>Épaule / coude / poignet</span><span>Hanche / aine / cuisse</span><span>Genou / cheville / pied</span></div>
  <p class="mt-2 text-xs text-slate-500">Support de localisation clinique conforme à la logique régionale du PCMA ; les tests articulaires restent documentés séparément.</p>
</div>
