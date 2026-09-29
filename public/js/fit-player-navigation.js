(function () {
    'use strict';
    const pagePath = window.location.pathname;
    let pending = null;

    async function navigate(playerId, addHistory = true) {
        const id = String(playerId);
        const url = new URL(window.location.href);
        if (!/^[1-9]\d*$/.test(id)) {
            showMessage('Joueur introuvable');
            return false;
        }
        url.searchParams.set('player_id', id);
        if (url.pathname !== pagePath || url.origin !== window.location.origin) return false;
        if (pending) pending.abort();
        const controller = new AbortController();
        pending = controller;
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 z-50 bg-gray-900 text-white flex items-center justify-center';
        const status = document.createElement('p');
        status.className = 'bg-white/10 rounded-lg p-4 border border-white/20 text-gray-300';
        status.textContent = 'Chargement des performances…';
        overlay.appendChild(status);
        document.body.appendChild(overlay);
        try {
            const response = await fetch(url.href, {
                credentials: 'same-origin',
                headers: {'Accept': 'text/html'},
                signal: controller.signal
            });
            if (!response.ok) {
                status.textContent = response.status === 404 ? 'Joueur introuvable' : 'Données indisponibles pour le moment';
                return false;
            }
            const html = await response.text();
            if (!html.includes('id="cockpit-joueur"')) {
                status.textContent = 'Données indisponibles pour le moment';
                return false;
            }
            if (addHistory) history.pushState({fitPlayerId:id}, '', url.href);
            await replacePage(html);
            return true;
        } catch (error) {
            if (error.name !== 'AbortError') {
                if (document.body.contains(overlay)) status.textContent = 'Données indisponibles pour le moment';
                else showMessage('Données indisponibles pour le moment');
            } else overlay.remove();
            return false;
        } finally {
            if (pending === controller) pending = null;
        }
    }
    async function replacePage(html) {
        const parsed = new DOMParser().parseFromString(html, 'text/html');
        if (!parsed.getElementById('cockpit-joueur')) throw new Error('Missing cockpit');
        if (document.readyState === 'loading') {
            await new Promise(resolve => window.addEventListener('load', resolve, {once:true}));
        }
        document.open();
        document.write('<!doctype html><html><head></head><body></body></html>');
        document.close();
        document.documentElement.lang = parsed.documentElement.lang;
        const scripts = [];
        function copyChildren(from, to) {
            for (const child of from.childNodes) {
                if (child.nodeType === Node.TEXT_NODE) {
                    to.appendChild(document.createTextNode(child.textContent));
                } else if (child.nodeType === Node.COMMENT_NODE) {
                    to.appendChild(document.createComment(child.textContent));
                } else if (child.nodeType === Node.ELEMENT_NODE) {
                    if (child.localName === 'script') {
                        const marker = document.createComment('script');
                        to.appendChild(marker);
                        scripts.push([child, marker]);
                        continue;
                    }
                    const clone = document.createElementNS(child.namespaceURI, child.localName);
                    for (const attribute of child.attributes) clone.setAttribute(attribute.name, attribute.value);
                    to.appendChild(clone);
                    copyChildren(child, clone);
                }
            }
        }
        copyChildren(parsed.head, document.head);
        copyChildren(parsed.body, document.body);
        for (const [source, marker] of scripts) {
            const script = document.createElement('script');
            script.async = false;
            for (const attribute of source.attributes) script.setAttribute(attribute.name, attribute.value);
            if (script.src) {
                const loaded = new Promise((resolve, reject) => {
                    script.addEventListener('load', resolve, {once:true});
                    script.addEventListener('error', reject, {once:true});
                });
                marker.replaceWith(script);
                await loaded;
            } else {
                script.textContent = source.textContent;
                marker.replaceWith(script);
            }
        }
        document.dispatchEvent(new Event('DOMContentLoaded', {bubbles:true}));
    }
    function showMessage(message) {
        document.body.replaceChildren();
        document.body.className = 'bg-gray-900 text-white min-h-screen flex items-center justify-center';
        const p = document.createElement('p');
        p.className = 'bg-white/10 rounded-lg p-4 border border-white/20 text-gray-300';
        p.textContent = message;
        document.body.appendChild(p);
        document.title = message + ' - FIT';
    }
    window.fitNavigatePlayer = navigate;
    window.addEventListener('popstate', () => {
        const id = new URL(window.location.href).searchParams.get('player_id');
        if (id) navigate(id, false);
        else showMessage('Joueur introuvable');
    });
    document.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link || link.closest('[aria-label="Language selector"]')) return;
        const url = new URL(link.href, window.location.href);
        const current = new URL(window.location.href);
        if (url.searchParams.get('lang') !== current.searchParams.get('lang')) return;
        if (url.origin === window.location.origin && url.pathname === pagePath
            && url.searchParams.has('player_id') && !event.metaKey && !event.ctrlKey) {
            event.preventDefault();
            navigate(url.searchParams.get('player_id'));
        }
    });
})();
