# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("alert('❌ Erreur lors de l\\'ouverture de la fenêtre d\\'impression: ' + error.message);",
     "alert(@json(__('pcma.err_print_window')) + error.message);", 1),
    ("const athleteName = selectedAthlete ? selectedAthlete.text : 'Athlète non spécifié';",
     "const athleteName = selectedAthlete ? selectedAthlete.text : @json(__('pcma.athlete_not_specified'));", 1),
    ("alert('❌ Erreur lors de l\\'ouverture de la signature médecin: ' + error.message);",
     "alert(@json(__('pcma.err_signoff_open')) + error.message);", 1),
    ("alert('❌ Erreur lors du traitement de la signature: ' + error.message);",
     "alert(@json(__('pcma.err_signature_processing')) + error.message);", 1),
    ("actionStatus.textContent = 'Signature indisponible : numéro professionnel vérifié requis';",
     "actionStatus.textContent = @json(__('pcma.signature_unavailable_professional_number'));", 1),
    ("alert('Signature indisponible : identité, numéro professionnel et décision médicale vérifiés requis.');",
     "alert(@json(__('pcma.signature_unavailable_full')));", 1),
    ("alert('❌ Erreur lors de la sauvegarde de la signature. Veuillez réessayer.');",
     "alert(@json(__('pcma.err_signature_save')));", 1),
    ("alert('❌ Erreur de connexion lors de la sauvegarde. Veuillez réessayer.');",
     "alert(@json(__('pcma.err_signature_connection')));", 1),
    ("alert('Renseignez le joueur, le type, le médecin et la date avant la signature.');",
     "alert(@json(__('pcma.fill_required_before_signing')));", 1),
    ("<strong>⚠️ Document signé</strong>",
     "<strong>{{ __('pcma.document_signed_label') }}</strong>", 1),
    ("<p class=\"mt-1\">Ce PCMA a été signé et ne peut plus être modifié. Seule l'impression est autorisée.</p>",
     "<p class=\"mt-1\">{{ __('pcma.document_signed_notice') }}</p>", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH17 OK", len(replacements), "replacements applied")
