<div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm" data-pcma-ecg-preview>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">ECG 12 dérivations</p>
            <h4 class="mt-1 font-semibold text-slate-900">Tracé enregistré du joueur</h4>
            <p class="mt-1 text-xs text-slate-500">Aperçu du fichier ECG chargé dans ce PCMA. Aucun tracé simulé n’est affiché.</p>
        </div>
        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600" data-ecg-state>Aucun ECG</span>
    </div>
    <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-slate-50" style="min-height:260px">
        <div class="flex min-h-[260px] items-center justify-center p-8 text-center" data-ecg-empty>
            <div><p class="font-medium text-slate-700">Chargez l’ECG dans la section Imagerie médicale</p><p class="mt-1 text-xs text-slate-500">PDF, image ou DICOM selon les formats acceptés.</p></div>
        </div>
        <img data-ecg-image class="hidden max-h-[520px] w-full object-contain bg-white" alt="ECG 12 dérivations chargé pour le PCMA">
        <iframe data-ecg-pdf class="hidden h-[520px] w-full bg-white" title="ECG PDF"></iframe>
        <div data-ecg-dicom class="hidden min-h-[260px] items-center justify-center p-8 text-center"><div><p class="font-semibold text-slate-800">ECG DICOM sélectionné</p><p class="mt-1 text-xs text-slate-500">Le fichier sera ouvert avec le viewer DICOM après enregistrement.</p></div></div>
    </div>
    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500"><span data-ecg-filename>Aucun fichier sélectionné</span><span>Référence PCMA FIFA · ECG au repos</span></div>
</div>
