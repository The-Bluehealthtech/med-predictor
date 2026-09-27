# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<h3 class="text-lg font-semibold text-green-900">🏆 Conformité FIFA</h3>',
     "<h3 class=\"text-lg font-semibold text-green-900\">{{ __('pcma.fifa_compliance_title') }}</h3>", 1),
    ('<p class="text-green-700 mb-4">Informations requises pour la conformité FIFA</p>',
     "<p class=\"text-green-700 mb-4\">{{ __('pcma.fifa_compliance_desc') }}</p>", 1),
    ("                                        ID FIFA\n",
     "                                        {{ __('pcma.fifa_id_label') }}\n", 1),
    ("                                        Nom de la compétition\n",
     "                                        {{ __('pcma.competition_name_label') }}\n", 1),
    ("                                        Date de la compétition\n",
     "                                        {{ __('pcma.competition_date_label') }}\n", 1),
    ("                                        Nom de l'équipe\n",
     "                                        {{ __('pcma.team_name_label') }}\n", 1),
    ('placeholder="Équipe nationale"',
     'placeholder="{{ __(\'pcma.team_name_placeholder\') }}"', 1),
    ("                                        Poste du joueur\n",
     "                                        {{ __('pcma.player_position_label') }}\n", 1),
    ('<option value="">Sélectionner le poste</option>',
     "<option value=\"\">{{ __('pcma.select_position_placeholder') }}</option>", 1),
    (">Gardien</option>", ">{{ __('pcma.position_goalkeeper_option') }}</option>", 1),
    (">Défenseur</option>", ">{{ __('pcma.position_defender_option') }}</option>", 1),
    (">Milieu</option>", ">{{ __('pcma.position_midfielder_option') }}</option>", 1),
    (">Attaquant</option>", ">{{ __('pcma.position_forward_option') }}</option>", 1),
    ("                                             Conforme aux standards FIFA\n",
     "                                             {{ __('pcma.fifa_compliant_label') }}\n", 1),
    ('<p class="text-sm text-gray-500 mt-1">Cochez cette case si l\'évaluation respecte tous les critères FIFA</p>',
     "<p class=\"text-sm text-gray-500 mt-1\">{{ __('pcma.fifa_compliant_hint') }}</p>", 1),

    ('<h4 class="text-lg font-semibold text-purple-900">⚽ Évaluation Fitness Football Professionnel</h4>',
     "<h4 class=\"text-lg font-semibold text-purple-900\">{{ __('pcma.fitness_assessment_title') }}</h4>", 1),
    ("                        Analyse complète de l'aptitude du joueur pour le football professionnel basée sur tous les examens médicaux\n",
     "                        {{ __('pcma.fitness_assessment_desc') }}\n", 1),
    ('                        🤖 Générer Rapport Fitness Professionnel\n',
     "                        {{ __('pcma.generate_fitness_report_btn') }}\n", 1),
    ('<h5 class="font-semibold text-purple-900 mb-3"> Rapport d\'Évaluation Fitness</h5>',
     "<h5 class=\"font-semibold text-purple-900 mb-3\"> {{ __('pcma.fitness_report_title') }}</h5>", 1),

    ('<h4 class="text-lg font-semibold text-green-900">📄 Export et Impression</h4>',
     "<h4 class=\"text-lg font-semibold text-green-900\">{{ __('pcma.export_print_title') }}</h4>", 1),
    ("                        Générez des rapports PDF et imprimez les évaluations médicales\n",
     "                        {{ __('pcma.export_print_desc') }}\n", 1),
    ('                            📄 Générer PDF\n',
     "                            {{ __('pcma.generate_pdf_btn') }}\n", 1),
    ('                            🖨️ Imprimer Rapport\n',
     "                            {{ __('pcma.print_report_btn') }}\n", 1),
    ('                             Signature Médecin\n',
     "                             {{ __('pcma.doctor_signoff_btn') }}\n", 1),
    ('<span class="text-green-600">Génération en cours...</span>',
     "<span class=\"text-green-600\">{{ __('pcma.generation_in_progress') }}</span>", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH12 OK", len(replacements), "replacements applied")
