<?php

namespace App\Helpers;

class NationalityHelper
{
    public static function getNationalities(): array
    {
        return [
            'England', 'Scotland', 'Wales', 'Northern Ireland', 'Republic of Ireland',
            'France', 'Germany', 'Spain', 'Italy', 'Portugal', 'Netherlands', 'Belgium',
            'Switzerland', 'Austria', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Poland',
            'Czech Republic', 'Slovakia', 'Hungary', 'Romania', 'Bulgaria', 'Croatia',
            'Serbia', 'Slovenia', 'Bosnia and Herzegovina', 'Montenegro', 'Albania',
            'Greece', 'Turkey', 'Ukraine', 'Russia', 'Belarus', 'Lithuania', 'Latvia',
            'Estonia', 'Moldova', 'Georgia', 'Armenia', 'Azerbaijan', 'Kazakhstan',
            'Brazil', 'Argentina', 'Uruguay', 'Paraguay', 'Chile', 'Peru', 'Colombia',
            'Venezuela', 'Ecuador', 'Bolivia', 'Mexico', 'United States', 'Canada',
            'Costa Rica', 'Panama', 'Honduras', 'El Salvador', 'Guatemala', 'Nicaragua',
            'Jamaica', 'Trinidad and Tobago', 'Haiti', 'Dominican Republic', 'Cuba',
            'Morocco', 'Algeria', 'Tunisia', 'Libya', 'Egypt', 'Sudan', 'South Sudan',
            'Ethiopia', 'Eritrea', 'Djibouti', 'Somalia', 'Kenya', 'Uganda', 'Tanzania',
            'Rwanda', 'Burundi', 'Democratic Republic of the Congo', 'Republic of the Congo',
            'Central African Republic', 'Chad', 'Cameroon', 'Nigeria', 'Niger', 'Mali',
            'Burkina Faso', 'Senegal', 'Gambia', 'Guinea-Bissau', 'Guinea', 'Sierra Leone',
            'Liberia', 'Ivory Coast', 'Ghana', 'Togo', 'Benin', 'South Africa', 'Namibia',
            'Botswana', 'Zimbabwe', 'Zambia', 'Malawi', 'Mozambique', 'Madagascar',
            'Mauritius', 'Seychelles', 'Comoros', 'Mayotte', 'Reunion', 'China', 'Japan',
            'South Korea', 'North Korea', 'Mongolia', 'Taiwan', 'Hong Kong', 'Macau',
            'Vietnam', 'Laos', 'Cambodia', 'Thailand', 'Myanmar', 'Malaysia', 'Singapore',
            'Brunei', 'Indonesia', 'Philippines', 'East Timor', 'Papua New Guinea',
            'Australia', 'New Zealand', 'Fiji', 'Vanuatu', 'New Caledonia', 'Solomon Islands',
            'Samoa', 'Tonga', 'Tuvalu', 'Kiribati', 'Marshall Islands', 'Micronesia',
            'Palau', 'Nauru', 'India', 'Pakistan', 'Bangladesh', 'Sri Lanka', 'Nepal',
            'Bhutan', 'Maldives', 'Afghanistan', 'Iran', 'Iraq', 'Syria', 'Lebanon',
            'Jordan', 'Israel', 'Palestine', 'Saudi Arabia', 'Yemen', 'Oman', 'United Arab Emirates',
            'Qatar', 'Bahrain', 'Kuwait', 'Iceland', 'Faroe Islands', 'Greenland'
        ];
    }

    public static function getPositions(): array
    {
        return [
            'ST' => 'Attaquant (ST)',
            'RW' => 'Ailier droit (RW)',
            'LW' => 'Ailier gauche (LW)',
            'CAM' => 'Milieu offensif (CAM)',
            'CM' => 'Milieu central (CM)',
            'CDM' => 'Milieu défensif (CDM)',
            'CB' => 'Défenseur central (CB)',
            'RB' => 'Arrière droit (RB)',
            'LB' => 'Arrière gauche (LB)',
            'GK' => 'Gardien (GK)',
        ];
    }

    public static function getStakeholderRoles(): array
    {
        return [
            'player' => 'Joueur',
            'referee' => 'Arbitre',
            'assistant_referee' => 'Arbitre assistant',
            'fourth_official' => 'Quatrième arbitre',
            'var_official' => 'Officiel VAR',
            'match_commissioner' => 'Commissaire de match',
            'club_admin' => 'Administrateur de club',
            'club_manager' => 'Manager de club',
            'club_medical' => 'Staff médical de club',
            'association_admin' => 'Administrateur d\'association',
            'association_registrar' => 'Registraire d\'association',
            'association_medical' => 'Staff médical d\'association',
            'team_doctor' => 'Médecin d\'équipe',
            'physiotherapist' => 'Physiothérapeute',
            'sports_scientist' => 'Scientifique du sport',
            'system_admin' => 'Administrateur système',
        ];
    }

    public static function getCompetitionTypes(): array
    {
        return [
            'league' => 'Championnat',
            'cup' => 'Coupe',
            'international' => 'Compétition internationale',
            'friendly' => 'Match amical',
            'playoff' => 'Barrage',
        ];
    }

    public static function getLicenseTypes(): array
    {
        return [
            'federation' => 'Licence fédérale',
            'international' => 'Licence internationale',
            'amateur' => 'Licence amateur',
            'youth' => 'Licence jeune',
            'temporary' => 'Licence temporaire',
        ];
    }

    public static function getLicenseStatuses(): array
    {
        return [
            'pending' => 'En attente',
            'approved' => 'Approuvée',
            'rejected' => 'Rejetée',
            'suspended' => 'Suspendue',
            'expired' => 'Expirée',
        ];
    }

    public static function getPlayerStatuses(): array
    {
        return [
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'suspended' => 'Suspendu',
            'injured' => 'Blessé',
            'retired' => 'Retraité',
        ];
    }

    public static function getMatchStages(): array
    {
        return [
            'draft' => 'Brouillon',
            'submitted' => 'Soumis',
            'validated' => 'Validé',
            'finalized' => 'Finalisé',
        ];
    }

    public static function getEventTypes(): array
    {
        return [
            'goal' => 'But',
            'assist' => 'Passe décisive',
            'yellow_card' => 'Carton jaune',
            'red_card' => 'Carton rouge',
            'substitution' => 'Remplacement',
            'injury' => 'Blessure',
            'penalty' => 'Penalty',
            'own_goal' => 'But contre son camp',
            'missed_penalty' => 'Penalty manqué',
            'foul' => 'Faute',
            'offside' => 'Hors-jeu',
            'corner' => 'Corner',
            'free_kick' => 'Coup franc',
            'throw_in' => 'Remise en jeu',
        ];
    }
} 