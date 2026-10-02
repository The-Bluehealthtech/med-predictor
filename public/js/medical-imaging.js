/* Private image reader: decoded frames are fetched from authorized FIT routes. */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('fi-workspace');
    if (!root) return;
    const byId = id => document.getElementById(id);
    const images = JSON.parse(byId('fi-images-data').textContent);
    const canvas = byId('fi-canvas'), ctx = canvas.getContext('2d'), wrap = byId('fi-canvas-wrap');
    const message = byId('fi-viewer-message'), refsInput = byId('fi-reference-data');
    const form = byId('fi-report-form'), editable = !form.querySelector('fieldset').disabled;
    let refs;
    try { refs = JSON.parse(refsInput.value || '[]'); } catch (_) { refs = []; }
    let current = 0, frame = 0, zoom = 1, inverted = false, image = null, measuring = false, pending = null, dirty = false;
    let aborter = null, loadNumber = 0;
    const selected = () => images[current];
    const markDirty = () => {
        if (!editable) return;
        dirty = true;
        byId('fi-save-state').textContent = 'Modifications non enregistrées — sauvegardez le brouillon.';
        if (byId('fi-validate-button')) byId('fi-validate-button').disabled = true;
    };
    form.addEventListener('input', markDirty);
    form.addEventListener('change', markDirty);
    form.addEventListener('submit', () => { dirty = false; });
    window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
    if (byId('fi-validation-form')) byId('fi-validation-form').addEventListener('submit', e => {
        if (dirty) { e.preventDefault(); byId('fi-save-state').scrollIntoView({block: 'center'}); }
    });
    const currentReferences = () => refs.filter(r => r.instance_id === selected()?.id && r.frame === frame && r.points);
    const draw = () => {
        if (!image || !selected()) return;
        const md = selected().metadata;
        const spacing = md.frame_pixel_spacing?.[frame] || md.pixel_spacing || [];
        const aspect = spacing.length === 2 && spacing[0] > 0 && spacing[1] > 0 ? spacing[0] / spacing[1] : 1;
        canvas.width = image.naturalWidth; canvas.height = Math.max(1, Math.round(image.naturalHeight * aspect));
        ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
        canvas.style.filter = inverted ? 'invert(1)' : '';
        const available = Math.max(200, wrap.clientWidth - 32);
        const fit = Math.min(available / canvas.width, (wrap.clientHeight - 32) / canvas.height);
        canvas.style.width = `${canvas.width * fit * zoom}px`; canvas.style.height = `${canvas.height * fit * zoom}px`;
        const toCanvas = (x, y) => [(x - 1) * canvas.width / Math.max(md.columns - 1, 1), (y - 1) * canvas.height / Math.max(md.rows - 1, 1)];
        currentReferences().forEach(r => {
            const a = toCanvas(r.points[0], r.points[1]), b = toCanvas(r.points[2], r.points[3]);
            ctx.strokeStyle = '#38bdf8'; ctx.lineWidth = Math.max(2, canvas.width / 350);
            ctx.beginPath(); ctx.moveTo(...a); ctx.lineTo(...b); ctx.stroke();
        });
        if (pending) { const p = toCanvas(...pending); ctx.fillStyle = '#fbbf24'; ctx.beginPath(); ctx.arc(p[0], p[1], Math.max(4, canvas.width / 160), 0, Math.PI * 2); ctx.fill(); }
    };
    const renderRefs = () => {
        refsInput.value = JSON.stringify(refs);
        const list = byId('fi-references'); list.replaceChildren();
        refs.forEach((r, index) => {
            const source = images.find(i => i.id === r.instance_id), li = document.createElement('li'), text = document.createElement('span');
            let label = `${source?.name || 'Image'} · Coupe ${r.frame + 1}`;
            if (r.points) {
                const spacing = source?.metadata.frame_pixel_spacing?.[r.frame] || source?.metadata.pixel_spacing || [];
                if (spacing.length === 2 && spacing[0] > 0 && spacing[1] > 0) {
                    const length = Math.hypot((r.points[2] - r.points[0]) * spacing[1], (r.points[3] - r.points[1]) * spacing[0]);
                    label += ` · ${length.toFixed(2)} mm`;
                } else label += ' · Annotation sans calibration';
            }
            text.textContent = label; li.append(text);
            const open = document.createElement('button'); open.type = 'button'; open.className = 'fi-button'; open.textContent = 'Voir';
            open.addEventListener('click', () => { current = images.findIndex(i => i.id === r.instance_id); frame = r.frame; byId('fi-image').value = String(current); syncImage(false); load(); }); li.append(open);
            if (editable) {
                const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'fi-button'; remove.textContent = 'Retirer';
                remove.addEventListener('click', () => { refs.splice(index, 1); renderRefs(); markDirty(); draw(); }); li.append(remove);
            }
            list.append(li);
        });
        if (!refs.length) { const li = document.createElement('li'); li.textContent = 'Aucune coupe sélectionnée. Ajoutez les images qui justifient votre compte rendu.'; list.append(li); }
    };
    const load = async () => {
        if (!selected()) return;
        if (aborter) aborter.abort(); aborter = new AbortController(); const serial = ++loadNumber;
        pending = null; image = null; ctx.clearRect(0, 0, canvas.width, canvas.height);
        message.hidden = false; message.textContent = 'Chargement de la coupe…';
        byId('fi-bookmark').disabled = true; byId('fi-measure').disabled = true;
        const spacing = selected().metadata.frame_pixel_spacing?.[frame] || selected().metadata.pixel_spacing || [];
        byId('fi-calibration').textContent = spacing.length === 2 && spacing.every(v => v > 0) ? `Calibration DICOM : ${spacing[0]} × ${spacing[1]} mm/pixel. Vérifiez sa pertinence pour l’examen.` : 'Calibration absente : annotations possibles ; aucune distance en mm ne sera inventée.';
        const url = new URL(selected().url, location.origin); url.searchParams.set('frame', String(frame));
        const center = byId('fi-center').value, width = byId('fi-width').value;
        if (center !== '' && width !== '' && Number.isFinite(Number(center)) && Number(width) >= 1) { url.searchParams.set('center', center); url.searchParams.set('width', width); }
        byId('fi-frame-label').textContent = `Coupe ${frame + 1} / ${selected().metadata.frames}`;
        byId('fi-frame').value = String(frame);
        byId('fi-prev').disabled = frame === 0 && current === 0;
        byId('fi-next').disabled = frame === selected().metadata.frames - 1 && current === images.length - 1;
        let blobUrl;
        try {
            const response = await fetch(url, {signal: aborter.signal, headers: {'Accept': 'image/png'}});
            if (!response.ok) {
                let error; try { error = await response.json(); } catch (_) { error = {}; }
                throw new Error(error.message || 'Image indisponible. Vérifiez votre session ou le format du fichier.');
            }
            blobUrl = URL.createObjectURL(await response.blob()); const loaded = new Image(); loaded.src = blobUrl; await loaded.decode();
            if (serial !== loadNumber) return;
            image = loaded; message.hidden = true; draw();
            byId('fi-bookmark').disabled = !editable; byId('fi-measure').disabled = !editable;
        } catch (error) {
            if (error.name !== 'AbortError' && serial === loadNumber) { message.hidden = false; message.textContent = error.message; }
        } finally { if (blobUrl) URL.revokeObjectURL(blobUrl); }
    };
    const syncImage = (reset = true) => {
        if (!selected()) return;
        if (reset) frame = 0;
        const md = selected().metadata;
        byId('fi-frame').max = String(md.frames - 1); byId('fi-source-download').href = selected().download;
        byId('fi-center').value = md.window_center ?? ''; byId('fi-width').value = md.window_width ?? '';
        const spacing = md.frame_pixel_spacing?.[frame] || md.pixel_spacing || [];
        byId('fi-calibration').textContent = spacing.length === 2 ? `Calibration DICOM : ${spacing[0]} × ${spacing[1]} mm/pixel. Vérifiez sa pertinence pour l’examen.` : 'Calibration absente : annotations possibles ; aucune distance en mm ne sera inventée.';
    };
    byId('fi-image').addEventListener('change', () => { current = Number(byId('fi-image').value); syncImage(); load(); });
    byId('fi-frame').addEventListener('change', () => { frame = Number(byId('fi-frame').value); load(); });
    const step = direction => {
        if (!selected()) return;
        frame += direction;
        if (frame < 0) { if (current === 0) { frame = 0; return; } current--; syncImage(); frame = selected().metadata.frames - 1; }
        else if (frame >= selected().metadata.frames) { if (current === images.length - 1) { frame--; return; } current++; syncImage(); }
        byId('fi-image').value = String(current); load();
    };
    byId('fi-prev').addEventListener('click', () => step(-1)); byId('fi-next').addEventListener('click', () => step(1));
    byId('fi-zoom-in').addEventListener('click', () => { zoom = Math.min(zoom * 1.3, 8); draw(); });
    byId('fi-zoom-out').addEventListener('click', () => { zoom = Math.max(zoom / 1.3, .5); draw(); });
    byId('fi-reset').addEventListener('click', () => { zoom = 1; inverted = false; measuring = false; pending = null; canvas.classList.remove('fi-measuring'); syncImage(false); load(); });
    byId('fi-invert').addEventListener('click', () => { inverted = !inverted; draw(); });
    byId('fi-window').addEventListener('click', load);
    byId('fi-measure').addEventListener('click', () => { measuring = !measuring; pending = null; canvas.classList.toggle('fi-measuring', measuring); byId('fi-measure').textContent = measuring ? 'Mesure active · cliquer 2 points' : 'Mesurer · 2 points'; });
    canvas.addEventListener('click', e => {
        if (!measuring || !image || !editable) return;
        const rect = canvas.getBoundingClientRect(), md = selected().metadata;
        const point = [1 + Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width)) * (md.columns - 1), 1 + Math.max(0, Math.min(1, (e.clientY - rect.top) / rect.height)) * (md.rows - 1)];
        if (!pending) pending = point;
        else { refs.push({instance_id: selected().id, frame, points: [...pending, ...point]}); pending = null; renderRefs(); markDirty(); }
        draw();
    });
    byId('fi-bookmark').addEventListener('click', () => { if (image && editable && !refs.some(r => r.instance_id === selected().id && r.frame === frame && !r.points)) { refs.push({instance_id: selected().id, frame}); renderRefs(); markDirty(); } });
    if (byId('fi-population')) {
        const updateGrade = () => { const population = byId('fi-population').value, grade = byId('fi-grade'); const applicable = population === 'male' && form.querySelector('[name=quality]').value !== 'uninterpretable'; [grade, byId('fi-second-grade')].forEach(select => { select.disabled = !editable || !applicable; if (!applicable && editable) select.value = ''; }); byId('fi-grade-notice').textContent = grade.value === '6' ? 'Grade VI : revue spécialisée et documentaire nécessaire. Ce résultat ne prouve pas un âge supérieur à 17 ans.' : 'Sélectionnez les coupes justificatives. Aucun âge chiffré n’est calculé.'; };
        form.querySelector('[name=quality]').addEventListener('change', updateGrade); byId('fi-population').addEventListener('change', updateGrade); byId('fi-grade').addEventListener('change', updateGrade); updateGrade();
    }
    window.addEventListener('resize', draw);
    renderRefs();
    if (images.length) { syncImage(); load(); }
    else { byId('fi-source-download').hidden = true; ['fi-bookmark','fi-measure','fi-prev','fi-next','fi-window','fi-reset'].forEach(id => { byId(id).disabled = true; }); }
});
