<?php

namespace App\Services\Medical;


/**
 * Analyse d'une dictée de PCMA (français) en propositions de champs du formulaire. Rien n'est
 * appliqué automatiquement : le médecin accepte chaque proposition. Une valeur n'est proposée
 * que si la phrase la désigne explicitement (« tension », « fréquence cardiaque »…) ; une
 * négation (« pas d'antécédents cardiaques ») produit une mention explicite d'absence ; aucune
 * valeur n'est déduite.
 *
 * @phpstan-type Proposal array{field:string, label:string, value:string, excerpt:string}
 */
final class PcmaDictationParser
{
    private const POSITIONS = ['gardien' => 'goalkeeper', 'défenseur' => 'defender', 'defenseur' => 'defender', 'milieu' => 'midfielder', 'attaquant' => 'forward'];

    private const NUMBERS = ['zéro' => 0, 'zero' => 0, 'un' => 1, 'une' => 1, 'deux' => 2, 'trois' => 3, 'quatre' => 4, 'cinq' => 5, 'six' => 6, 'sept' => 7, 'huit' => 8, 'neuf' => 9, 'dix' => 10,
        'onze' => 11, 'douze' => 12, 'treize' => 13, 'quatorze' => 14, 'quinze' => 15, 'seize' => 16];

    /** @return list<Proposal> */
    public function parse(string $text): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        if ($text === '') {
            return [];
        }
        $lower = mb_strtolower($text);
        $lower = preg_replace_callback('/\b(' . implode('|', array_keys(self::NUMBERS)) . ')\b/u', fn ($m) => (string) self::NUMBERS[$m[1]], $lower);
        $proposals = [];
        $add = function (string $field, string $label, string $value, string $excerpt) use (&$proposals) {
            $proposals[$field] = ['field' => $field, 'label' => $label, 'value' => $value, 'excerpt' => trim($excerpt)];
        };

        // Identité
        if (preg_match("/(?:s'appelle|nom du joueur\s*:?|le joueur est)\s+((?:[A-ZÉÈÀÂÎÔÛÇ][\p{L}'-]+\s?){1,3})/u", $text, $m)) {
            $add('player_name', 'Nom du joueur (recherche)', trim($m[1]), $m[0]);
        }
        foreach (self::POSITIONS as $word => $code) {
            if (preg_match('/\b(?:est|joue|poste\s*:?)\s+(?:au\s+poste\s+de\s+|comme\s+)?' . $word . 's?\b/u', $lower, $m)) {
                $add('position', 'Poste', $code, $m[0]);
                break;
            }
        }
        if (preg_match('/\bfifa(?:\s+connect)?(?:\s+id)?\s*:?\s*((?:[a-z0-9][\s-]?){7})/u', $lower, $m)) {
            $id = strtoupper(preg_replace('/[\s-]/', '', $m[1]));
            if (preg_match('/^[A-Z0-9]{7}$/', $id)) {
                $add('fifa_connect_id', 'FIFA ID', $id, $m[0]);
            }
        }

        // Constantes (la phrase doit nommer la mesure)
        if (preg_match('/\b(?:tension(?:\s+artérielle)?|pression\s+artérielle|ta)\s*(?:de|à|:)?\s*(\d{1,3})\s*(?:sur|\/|-)\s*(\d{1,3})/u', $lower, $m)) {
            [$sys, $dia] = [(int) $m[1], (int) $m[2]];
            if ($sys < 30 && $dia < 30) { // convention française en cmHg : « 12 sur 8 » = 120/80 mmHg
                [$sys, $dia] = [$sys * 10, $dia * 10];
            }
            if ($sys >= 60 && $sys <= 260 && $dia >= 30 && $dia <= 160 && $sys > $dia) {
                $add('blood_pressure', 'Tension artérielle (mmHg)', "{$sys}/{$dia}", $m[0]);
            }
        }
        $this->measure($lower, '(?:fréquence\s+cardiaque|rythme\s+cardiaque|pouls|fc)', 'heart_rate', 'Fréquence cardiaque (bpm)', 25, 250, $add);
        $this->measure($lower, '(?:saturation(?:\s+en\s+oxygène)?|spo2|sat)', 'oxygen_saturation', 'Saturation en oxygène (%)', 50, 100, $add);
        $this->measure($lower, '(?:fréquence\s+respiratoire|fr)', 'respiratory_rate', 'Fréquence respiratoire (/min)', 4, 80, $add);
        if (preg_match('/\btempérature\s*(?:de|à|:)?\s*(\d{2})(?:\s*(?:[.,]|virgule)\s*(\d))?/u', $lower, $m)) {
            $value = (float) ($m[1] . (isset($m[2]) && $m[2] !== '' ? '.' . $m[2] : ''));
            if ($value >= 30 && $value <= 45) {
                $add('temperature', 'Température (°C)', rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.'), $m[0]);
            }
        }
        if (preg_match('/\bpoids\s*(?:de|à|:)?\s*(\d{2,3})(?:\s*(?:[.,]|virgule)\s*(\d))?\s*(?:kilos?|kg|kilogrammes?)?/u', $lower, $m)) {
            $value = (float) ($m[1] . (isset($m[2]) && $m[2] !== '' ? '.' . $m[2] : ''));
            if ($value >= 30 && $value <= 200) {
                $add('weight', 'Poids (kg)', rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.'), $m[0]);
            }
        }

        // Antécédents : négation explicite, sinon le texte qui suit, jusqu'à la fin de la phrase
        $this->history($text, $lower, 'cardi(?:aques?|o-?vasculaires?)', 'cardiovascular_history', 'Antécédents cardiovasculaires', 'Aucun antécédent cardiovasculaire déclaré', $add);
        $this->history($text, $lower, 'chirurgica(?:ux|l)', 'surgical_history', 'Antécédents chirurgicaux', 'Aucun antécédent chirurgical déclaré', $add);

        // Conclusion : proposée seulement si l'aptitude est dictée en toutes lettres
        if (preg_match('/\binapte\b/u', $lower, $m)) {
            $add('final_statement[overall_decision]', 'Conclusion', 'NOT_FIT', $m[0]);
        } elseif (preg_match('/\bapte\s+avec\s+(?:des\s+)?(?:restrictions?|réserves?)\b/u', $lower, $m)) {
            $add('final_statement[overall_decision]', 'Conclusion', 'CONDITIONAL', $m[0]);
        } elseif (preg_match('/\bapte\b/u', $lower, $m)) {
            $add('final_statement[overall_decision]', 'Conclusion', 'FIT', $m[0]);
        }

        return array_values($proposals);
    }

    private function measure(string $lower, string $names, string $field, string $label, int $min, int $max, callable $add): void
    {
        if (preg_match('/\b' . $names . '\s*(?:de|à|:)?\s*(\d{1,3})\b/u', $lower, $m) && (int) $m[1] >= $min && (int) $m[1] <= $max) {
            $add($field, $label, $m[1], $m[0]);
        }
    }

    private function history(string $text, string $lower, string $kind, string $field, string $label, string $none, callable $add): void
    {
        // Sur le texte d'origine (insensible à la casse) : le texte proposé reste celui dicté.
        if (preg_match('/\b(?:pas\s+d\'|pas\s+de\s+|aucuns?\s+|sans\s+)antécédents?\s+(?:\p{L}+\s+)?' . $kind . '/iu', $text, $m)) {
            $add($field, $label, $none, $m[0]);

            return;
        }
        if (preg_match('/\bantécédents?\s+' . $kind . '\s*:?\s*([^.;]{1,500})/iu', $text, $m)) {
            $sentence = rtrim(trim($m[1]), ', ');
            if ($sentence !== '') {
                $add($field, $label, $sentence, $m[0]);
            }
        }
    }
}
