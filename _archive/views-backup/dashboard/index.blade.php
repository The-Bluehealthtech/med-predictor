@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .fit-bi-card {
        background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
        border-radius: 1.25rem;
        box-shadow: 0 6px 32px 0 rgba(60, 80, 180, 0.10), 0 1.5px 4px 0 rgba(60, 80, 180, 0.08);
        transition: box-shadow 0.2s, transform 0.2s;
        border: 1.5px solid #e0e7ff;
        margin-bottom: 2rem;
    }
    .fit-bi-card:hover {
        box-shadow: 0 12px 48px 0 rgba(60, 80, 180, 0.18), 0 2px 8px 0 rgba(60, 80, 180, 0.12);
        transform: translateY(-2px) scale(1.01);
    }
    .fit-bi-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2342a4;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .fit-bi-divider {
        border-bottom: 1.5px solid #dbeafe;
        margin: 1rem 0 1.5rem 0;
    }
    .fit-bi-tab-btn {
        border-radius: 0.75rem;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        transition: background 0.15s, color 0.15s;
        margin-bottom: 0.5rem;
    }
    .fit-bi-tab-btn.selected {
        background: linear-gradient(90deg, #3b82f6 0%, #6366f1 100%);
        color: #fff;
        box-shadow: 0 2px 8px 0 rgba(60, 80, 180, 0.10);
    }
    .fit-bi-tab-btn:not(.selected) {
        background: #f1f5f9;
        color: #2342a4;
    }
    .fit-bi-kpi-label {
        color: #64748b;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    .fit-bi-kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2563eb;
    }
    .fit-bi-kpi-tile {
        background: linear-gradient(90deg, #3b82f6 0%, #6366f1 100%);
        color: #fff;
        border-radius: 1rem;
        box-shadow: 0 2px 8px 0 rgba(60, 80, 180, 0.10);
        padding: 1.25rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 120px;
        min-height: 90px;
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 1.3rem;
        position: relative;
    }
    .fit-bi-kpi-tile .kpi-label {
        font-size: 0.9rem;
        color: #e0e7ff;
        margin-top: 0.5rem;
        font-weight: 400;
    }
    .fit-bi-mini-card {
        background: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 1px 4px 0 rgba(60, 80, 180, 0.07);
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .fit-bi-bar {
        background: #e0e7ff;
        border-radius: 0.5rem;
        height: 1.1rem;
        margin-bottom: 0.5rem;
        overflow: hidden;
        position: relative;
    }
    .fit-bi-bar-inner {
        background: linear-gradient(90deg, #3b82f6 0%, #6366f1 100%);
        height: 100%;
        border-radius: 0.5rem;
        transition: width 0.4s;
    }
    .fit-bi-badge-alert {
        background: #fee2e2;
        color: #b91c1c;
        border-radius: 0.75rem;
        padding: 0.25rem 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .fit-bi-badge-ok {
        background: #d1fae5;
        color: #047857;
        border-radius: 0.75rem;
        padding: 0.25rem 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .fit-bi-chart-wrapper {
        width: 100%;
        height: 380px;
        min-width: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .fit-bi-chart-wrapper canvas {
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        max-height: 100% !important;
        min-width: 0;
    }
</style>
<div class="flex justify-center mb-6">
    <img src="{{ asset('images/logos/fit.png') }}" alt="FIT Logo" style="height:56px;width:auto;">
</div>
<div id="fit-bi-summary" class="fit-bi-card p-6">
    <h3 class="fit-bi-title">
        <svg class="w-7 h-7 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0a2 2 0 002 2h2a2 2 0 002-2m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v8" /></svg>
        360° FIT BI Summary
    </h3>
    <div class="fit-bi-divider"></div>
    <div id="summary-kpis" class="grid grid-cols-2 md:grid-cols-5 gap-4 text-center"></div>
</div>

<div class="fit-bi-card p-6">
    <div class="mb-4">
        <div class="flex flex-wrap gap-2" id="fit-bi-tabs"></div>
    </div>
    <div class="fit-bi-divider"></div>
    <div id="fit-bi-tab-content"></div>
</div>

<script>
const MODULES = [
    {key: 'admin', label: 'Admin'},
    {key: 'club', label: 'Club Mgmt'},
    {key: 'association', label: 'Association Mgmt'},
    {key: 'fifa', label: 'FIFA'},
    {key: 'device', label: 'Device Conn.'},
    {key: 'healthcare', label: 'Healthcare'},
    {key: 'referee', label: 'Referee'},
    {key: 'players', label: 'Players'},
    {key: 'competitions', label: 'Competitions'},
    {key: 'licenses', label: 'Licenses'},
    {key: 'alerts', label: 'Alerts'},
    {key: 'performance', label: 'Performance'},
];
let fitKpis = null;
let currentTab = 'admin';

function renderSummary(summary) {
    const el = document.getElementById('summary-kpis');
    el.innerHTML = '';
    for (const [k, v] of Object.entries(summary)) {
        el.innerHTML += `<div class="flex flex-col items-center p-2">
            <div class="fit-bi-kpi-value">${v}</div>
            <div class="fit-bi-kpi-label">${k.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</div>
        </div>`;
    }
}

function renderValue(v, k = null) {
    // 1. Tuiles pour tous les scalaires principaux (hors modules déjà traités)
    if ((typeof v === 'number' || typeof v === 'string') && k && !['alerts','alert','audit_logs_10','role_distribution','performance'].includes(k)) {
        return `<div class='fit-bi-kpi-tile'>${v}<div class='kpi-label'>${k.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</div></div>`;
    }
    // 2. Top listes (top_, top) → mini-cards
    if ((k && (k.includes('top_') || k.includes('top'))) && Array.isArray(v) && v.length > 0 && typeof v[0] === 'object') {
        return v.map((row, i) => `<div class='fit-bi-mini-card'><span class='font-bold text-blue-700'>#${i+1}</span> <span>${Object.values(row).slice(1).join(' - ')}</span></div>`).join('');
    }
    // 3. Distributions (distribution, by_position, alerts_by_type) → camembert/barres
    if ((k && (k.includes('distribution') || k.includes('by_position') || k.includes('alerts_by_type'))) && (Array.isArray(v) || typeof v === 'object') && v && Object.keys(v).length) {
        const chartId = 'chart-' + k + '-' + Math.random().toString(36).substring(2, 8);
        const labels = Array.isArray(v) ? v.map(x => x[Object.keys(x)[0]]) : Object.keys(v);
        const data = Array.isArray(v) ? v.map(x => x[Object.keys(x)[1]]) : Object.values(v);
        setTimeout(() => {
            const ctx = document.getElementById(chartId);
            if (ctx && window.Chart) {
                new Chart(ctx, {
                    type: labels.length <= 8 ? 'pie' : 'bar',
                    data: { labels, datasets: [{ data, backgroundColor: ['#3b82f6','#6366f1','#f59e42','#10b981','#ef4444','#fbbf24','#a21caf','#0ea5e9','#f472b6','#facc15'] }] },
                    options: { plugins: { legend: { display: true, position: 'bottom' } }, responsive: true, maintainAspectRatio: false }
                });
            }
        }, 100);
        return `<div style='height:220px'><canvas id='${chartId}'></canvas></div>`;
    }
    // 4. Tendances (trend) → courbe
    if ((k && k.includes('trend')) && Array.isArray(v) && v.length > 0 && (v[0].date || v[0].month)) {
        const chartId = 'chart-' + k + '-' + Math.random().toString(36).substring(2, 8);
        setTimeout(() => {
            const ctx = document.getElementById(chartId);
            if (ctx && window.Chart) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: v.map(x => x.date || x.month),
                        datasets: [{
                            label: k.replace(/_/g, ' '),
                            data: v.map(x => x.count || x.compliant_players || x.avg_rating),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.2)',
                            tension: 0.3,
                            fill: true,
                        }]
                    },
                    options: { plugins: { legend: { display: false } }, responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                });
            }
        }, 100);
        return `<div style='height:180px'><canvas id='${chartId}'></canvas></div>`;
    }
    // 5. Alertes (alert, alerts) → mini-cards rouges
    if ((k && (k.includes('alert') || k === 'alerts')) && Array.isArray(v) && v.length) {
        return v.map((row, i) => `<div class='fit-bi-mini-card' style='background:#fee2e2'><span class='font-bold text-red-700'>#${i+1}</span> <span>${Object.values(row).slice(1).join(' - ')}</span></div>`).join('');
    }
    // Cas général (tableau d'objets) → mini-cards
    if (Array.isArray(v) && v.length > 0 && typeof v[0] === 'object') {
        return v.map((row, i) => `<div class='fit-bi-mini-card'><span class='font-bold text-blue-700'>#${i+1}</span> <span>${Object.values(row).slice(1).join(' - ')}</span></div>`).join('');
    }
    // Cas général (objet) → liste clé/valeur
    if (typeof v === 'object' && v !== null) {
        return '<ul class="list-none ml-0">' + Object.entries(v).map(([kk, vv]) => `<li><span class="font-semibold">${kk.replace(/_/g, ' ')}:</span> ${renderValue(vv, kk)}</li>`).join('') + '</ul>';
    }
    // Cas fallback
    return v;
}

function formatRoleLabel(role) {
    if (!role || role === 'null') return 'Autre';
    return role.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function renderTabs() {
    const tabs = document.getElementById('fit-bi-tabs');
    tabs.innerHTML = '';
    MODULES.forEach(m => {
        const btn = document.createElement('button');
        btn.textContent = m.label;
        btn.className = 'fit-bi-tab-btn' + (currentTab === m.key ? ' selected' : '');
        btn.onclick = () => { currentTab = m.key; renderTabContent(); renderTabs(); };
        tabs.appendChild(btn);
    });
}

function renderTabContent() {
    const content = document.getElementById('fit-bi-tab-content');
    if (!fitKpis) { content.innerHTML = '<div>Loading...</div>'; return; }
    const data = fitKpis[currentTab];
    if (!data) { content.innerHTML = '<div>No data for this module.</div>'; return; }
    let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
    for (const [k, v] of Object.entries(data)) {
        html += `<div class="bg-gray-50 rounded-lg p-4 mb-2">
            <div class="text-xs text-gray-500 mb-1">${k.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</div>
            <div class="text-sm">${renderValue(v, k)}</div>
        </div>`;
    }
    html += '</div>';
    content.innerHTML = html;
}

function fetchFitKpis() {
    fetch('/api/fit/kpis')
        .then(res => res.json())
        .then(data => {
            fitKpis = data;
            renderSummary(data.summary);
            renderTabs();
            renderTabContent();
        });
}
document.addEventListener('DOMContentLoaded', fetchFitKpis);
</script>
@endsection 