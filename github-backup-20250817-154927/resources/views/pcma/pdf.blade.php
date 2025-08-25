<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport d'Évaluation Médicale PCMA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 10px 0 0 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .fitness-decision {
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            margin: 5px 0;
        }
        .decision-fit {
            background-color: #d4edda;
            color: #155724;
        }
        .decision-not-fit {
            background-color: #f8d7da;
            color: #721c24;
        }
        .decision-conditional {
            background-color: #fff3cd;
            color: #856404;
        }
        .signature-section {
            margin-top: 40px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 40px;
            margin: 10px 0;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport d'Évaluation Médicale PCMA</h1>
        <p>Date de génération: {{ $generatedAt->format('d/m/Y H:i') }}</p>
    </div>



    <div class="section">
        <h2>Informations du Patient</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Nom de l'Athlète:</span>
                <span class="info-value">{{ $athlete->name ?? 'Non spécifié' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">FIFA Connect ID:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['fifa_connect_id'] ?? 'Non spécifié') : 'Non spécifié' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date d'Évaluation:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['assessment_date'] ?? 'Non spécifié') : 'Non spécifié' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Type d'Évaluation:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['type'] ?? 'Non spécifié') : 'Non spécifié' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Statut:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['status'] ?? 'Non spécifié') : 'Non spécifié' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">ID d'Évaluation:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['assessment_id'] ?? 'Non spécifié') : 'Non spécifié' }}</span>
            </div>
        </div>
        

    </div>

    <div class="section">
        <h2>Signes Vitaux</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Tension Artérielle:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['blood_pressure'] ?? 'Non mesuré') : 'Non mesuré' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Fréquence Cardiaque:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['heart_rate'] ?? 'Non mesuré') : 'Non mesuré' }} bpm</span>
            </div>
            <div class="info-item">
                <span class="info-label">Température:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['temperature'] ?? 'Non mesuré') : 'Non mesuré' }}°C</span>
            </div>
            <div class="info-item">
                <span class="info-label">Saturation en Oxygène:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['oxygen_saturation'] ?? 'Non mesuré') : 'Non mesuré' }}%</span>
            </div>
            <div class="info-item">
                <span class="info-label">Fréquence Respiratoire:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['respiratory_rate'] ?? 'Non mesuré') : 'Non mesuré' }} /min</span>
            </div>
            <div class="info-item">
                <span class="info-label">Poids:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['weight'] ?? 'Non mesuré') : 'Non mesuré' }} kg</span>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Antécédents Médicaux</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Antécédents Cardiovasculaires:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['cardiovascular_history'] ?? 'Aucun') : 'Aucun' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Antécédents Chirurgicaux:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['surgical_history'] ?? 'Aucun') : 'Aucun' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Médicaments Actuels:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['medications'] ?? 'Aucun') : 'Aucun' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Allergies:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['allergies'] ?? 'Aucune') : 'Aucune' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Examen Physique</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Apparence Générale:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['general_appearance'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Examen Cutané:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['skin_examination'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Ganglions Lymphatiques:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['lymph_nodes'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Examen Abdominal:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['abdomen_examination'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Évaluation Cardiovasculaire</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Rythme Cardiaque:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['cardiac_rhythm'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Souffle Cardiaque:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['heart_murmur'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tension Artérielle au Repos:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['blood_pressure_rest'] ?? 'Non mesuré') : 'Non mesuré' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tension Artérielle à l'Effort:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['blood_pressure_exercise'] ?? 'Non mesuré') : 'Non mesuré' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Évaluation Neurologique</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Niveau de Conscience:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['consciousness'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Nerfs Crâniens:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['cranial_nerves'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Fonction Motrice:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['motor_function'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Fonction Sensitive:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['sensory_function'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Évaluation Musculo-squelettique</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Mobilité Articulaire:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['joint_mobility'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Force Musculaire:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['muscle_strength'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Évaluation de la Douleur:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['pain_assessment'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Amplitude de Mouvement:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['range_of_motion'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
        </div>
    </div>

    @if(isset($fitnessResults))
    <div class="section">
        <h2>Évaluation Fitness Football Professionnel</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Décision Globale:</span>
                @php
                    $decision = json_decode($fitnessResults, true);
                    $overallDecision = $decision['overall_decision'] ?? 'N/A';
                @endphp
                <span class="fitness-decision 
                    @if($overallDecision === 'FIT') decision-fit
                    @elseif($overallDecision === 'NOT_FIT') decision-not-fit
                    @elseif($overallDecision === 'CONDITIONAL') decision-conditional
                    @endif">
                    {{ $overallDecision }}
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Score Cardiovasculaire:</span>
                <span class="info-value">{{ $decision['cardiovascular_score'] ?? 'N/A' }}/10</span>
            </div>
            <div class="info-item">
                <span class="info-label">Score Musculo-squelettique:</span>
                <span class="info-value">{{ $decision['musculoskeletal_score'] ?? 'N/A' }}/10</span>
            </div>
            <div class="info-item">
                <span class="info-label">Score Neurologique:</span>
                <span class="info-value">{{ $decision['neurological_score'] ?? 'N/A' }}/10</span>
            </div>
        </div>
        
        @if(isset($decision['executive_summary']))
        <div style="margin-top: 15px;">
            <span class="info-label">Résumé Exécutif:</span>
            <p style="margin-top: 5px; color: #333;">{{ $decision['executive_summary'] }}</p>
        </div>
        @endif
    </div>
    @endif

    <div class="section">
        <h2>Imagerie Médicale</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">ECG Date:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['ecg_date'] ?? 'Non effectué') : 'Non effectué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Interprétation ECG:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['ecg_interpretation'] ?? 'Non interprété') : 'Non interprété' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Notes ECG:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['ecg_notes'] ?? 'Aucune note') : 'Aucune note' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">IRM Date:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['mri_date'] ?? 'Non effectué') : 'Non effectué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Type d'IRM:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['mri_type'] ?? 'Non spécifié') : 'Non spécifié' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Résultats IRM:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['mri_findings'] ?? 'Non évalué') : 'Non évalué' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Notes IRM:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['mri_notes'] ?? 'Aucune note') : 'Aucune note' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Notes Radiographie:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['xray_notes'] ?? 'Aucune note') : 'Aucune note' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Notes Scanner:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['ct_notes'] ?? 'Aucune note') : 'Aucune note' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Notes Échographie:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['ultrasound_notes'] ?? 'Aucune note') : 'Aucune note' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Conformité FIFA</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">ID FIFA:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['fifa_id'] ?? 'Non spécifié') : 'Non spécifié' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Nom de la Compétition:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['competition_name'] ?? 'Non spécifié') : 'Non spécifié' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date de la Compétition:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['competition_date'] ?? 'Non spécifiée') : 'Non spécifiée' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Nom de l'Équipe:</span>
                <span class="info-value">{{ is_array($formData) ? ($formData['team_name'] ?? 'Non spécifié') : 'Non spécifié' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Poste du Joueur:</span>
                <span class="info-value">{{ is_array($formData) ? ucfirst($formData['position'] ?? 'Non spécifié') : 'Non spécifié' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Conforme aux Standards FIFA:</span>
                <span class="info-value">{{ is_array($formData) && isset($formData['fifa_compliant']) ? ($formData['fifa_compliant'] ? 'Oui' : 'Non') : 'Non évalué' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Notes Cliniques</h2>
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
            <div class="info-item" style="margin-bottom: 10px;">
                <span class="info-label" style="font-weight: bold; color: #333;">Notes Générales:</span>
                <div style="margin-top: 5px; padding: 10px; background-color: white; border: 1px solid #ddd; border-radius: 3px; min-height: 20px;">
                    {{ is_array($formData) ? ($formData['notes'] ?? 'Aucune note') : 'Aucune note' }}
                </div>
            </div>
            <div class="info-item">
                <span class="info-label" style="font-weight: bold; color: #333;">Notes Cliniques Détaillées:</span>
                <div style="margin-top: 5px; padding: 10px; background-color: white; border: 1px solid #ddd; border-radius: 3px; min-height: 20px;">
                    {{ is_array($formData) ? ($formData['clinical_notes'] ?? 'Aucune note clinique') : 'Aucune note clinique' }}
                </div>
            </div>
        </div>
    </div>

    <div class="signature-section">
        <h2>Signature du Médecin Responsable</h2>
        
        @if($isSigned && $signedBy)
            <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #28a745;">
                <h3 style="margin: 0 0 10px 0; color: #28a745;">Déclaration Légale</h3>
                <p style="margin: 0; font-size: 14px; line-height: 1.5;">
                    Je, soussigné(e) médecin professionnel, confirme avoir examiné les informations cliniques complètes 
                    et assume l'entière responsabilité de la décision de fitness rendue dans ce document. 
                    Je comprends que cette évaluation sera utilisée pour la détermination de l'éligibilité au football professionnel 
                    et je certifie que toutes les informations fournies sont exactes selon mes meilleures connaissances médicales.
                </p>
            </div>
        @else
            <div style="margin: 20px 0; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
                <h3 style="margin: 0 0 10px 0; color: #856404;">Déclaration Légale</h3>
                <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #856404;">
                    ⚠️ Déclaration légale non confirmée - Signature médicale requise
                </p>
            </div>
        @endif
        
        @if($isSigned && $signedBy)
            <div style="margin: 20px 0;">
                @if($signatureImage)
                    <div style="margin-bottom: 15px;">
                        <span class="info-label">Signature Numérique:</span>
                        <div style="margin-top: 10px;">
                            <img src="{{ $signatureImage }}" alt="Signature médicale" style="max-width: 200px; border: 1px solid #ccc; padding: 5px;">
                        </div>
                    </div>
                @else
                    <div class="signature-line"></div>
                    <p style="margin-top: 10px; font-size: 12px; color: #666;">
                        Signature numérique capturée
                    </p>
                @endif
            </div>
            
            <div style="margin-top: 30px;">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nom du Médecin:</span>
                        <span class="info-value">{{ $signedBy }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Numéro de Licence:</span>
                        <span class="info-value">{{ $licenseNumber ?? 'Non spécifié' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de Signature:</span>
                        <span class="info-value">{{ $signedAt ? \Carbon\Carbon::parse($signedAt)->format('d/m/Y H:i') : 'Non spécifiée' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Statut:</span>
                        <span class="info-value" style="color: #28a745; font-weight: bold;">✅ Document Signé</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Déclaration Légale:</span>
                        <span class="info-value" style="color: #28a745; font-weight: bold;">✅ Confirmée</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Validation:</span>
                        <span class="info-value" style="color: #28a745; font-weight: bold;">✅ Document Médical Validé</span>
                    </div>
                </div>
            </div>
        @else
            <div style="margin: 20px 0;">
                <div class="signature-line"></div>
                <p style="margin-top: 10px; font-size: 12px; color: #666;">
                    Nom et signature du médecin responsable
                </p>
            </div>
            
            <div style="margin-top: 30px;">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nom du Médecin:</span>
                        <span class="info-value">_________________</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Numéro de Licence:</span>
                        <span class="info-value">_________________</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de Signature:</span>
                        <span class="info-value">_________________</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Statut:</span>
                        <span class="info-value" style="color: #dc3545; font-weight: bold;">⚠️ Document Non Signé</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Déclaration Légale:</span>
                        <span class="info-value" style="color: #dc3545; font-weight: bold;">⚠️ Non Confirmée</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Validation:</span>
                        <span class="info-value" style="color: #dc3545; font-weight: bold;">⚠️ Document Non Validé</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Ce rapport a été généré automatiquement par le système PCMA</p>
        <p>Document confidentiel - Usage médical uniquement</p>
    </div>
</body>
</html> 