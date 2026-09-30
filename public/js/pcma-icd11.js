'use strict';
// Chaque section conserve ses propres choix ; aucune déduction de diagnostic.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.pcma-icd11').forEach(root => {
        const input = root.querySelector('input[type=search]');
        const hidden = root.querySelector('[data-selection]');
        const results = root.querySelector('[data-results]');
        const list = root.querySelector('[data-selected-list]');
        const status = root.querySelector('[data-status]');
        const messages = JSON.parse(root.dataset.messages);
        let selected = JSON.parse(root.dataset.selected), timer, controller, sequence = 0;
        const sync = () => { hidden.value = JSON.stringify(selected.map(({id,release,language}) => ({id,release,language}))); };
        const button = (text, action) => {
            const b = document.createElement('button');
            b.type = 'button'; b.className = 'block w-full text-left px-3 py-2 border rounded-md text-sm';
            b.textContent = text; b.addEventListener('click', action); return b;
        };
        const render = () => {
            list.replaceChildren();
            selected.forEach((item, index) => {
                const row = document.createElement('div');
                const name = document.createElement('span');
                name.textContent = (item.code || item.id) + ' — ' + (item.label || messages.selected);
                row.append(name, button(messages.remove, () => { selected.splice(index,1); sync(); render(); }));
                if (item.coding_note) {
                    const note = document.createElement('p'); note.textContent = item.coding_note; row.append(note);
                }
                list.append(row);
            });
        };
        render(); sync();
        input.addEventListener('input', () => {
            clearTimeout(timer); controller?.abort(); const current = ++sequence;
            results.replaceChildren(); status.textContent = '';
            const query = input.value.trim();
            if (query.length < 2) return;
            timer = setTimeout(async () => {
                controller = new AbortController(); status.textContent = messages.loading;
                try {
                    const url = new URL(root.dataset.url, location.origin);
                    url.searchParams.set('q', query); url.searchParams.set('language', root.dataset.language);
                    const response = await fetch(url, {credentials:'same-origin',headers:{Accept:'application/json'},signal:controller.signal});
                    if (!response.ok) throw new Error(response.status === 401 || response.status === 403 ? messages.access : messages.unavailable);
                    const data = await response.json();
                    if (current !== sequence) return;
                    if (!Array.isArray(data.items)) throw new Error(messages.unavailable);
                    status.textContent = data.items.length ? messages.choose : messages.empty;
                    results.replaceChildren();
                    data.items.forEach(item => {
                        results.append(button(item.code + ' — ' + item.label, () => {
                            if (!selected.some(x => x.id === item.id && x.release === item.release)) selected.push(item);
                            sync(); render(); results.replaceChildren(); input.value = ''; status.textContent = messages.selected;
                        }));
                    });
                } catch (error) {
                    if (error.name !== 'AbortError' && current === sequence) status.textContent = error.message || messages.unavailable;
                }
            }, 300);
        });
    });
});
