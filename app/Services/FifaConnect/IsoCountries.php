<?php

namespace App\Services\FifaConnect;

use Illuminate\Support\Str;

/**
 * Pays ISO 3166-1 alpha-2 (config/iso_countries.php : codes de l'énumération
 * officielle FIFA Connect, noms français et anglais CLDR). Convertit un code ou
 * un nom saisi (français ou anglais) en code ; rien n'est deviné au-delà.
 */
final class IsoCountries
{
    /**
     * Noms encore en usage dans FIT qui ne sont pas les noms CLDR : anciens noms
     * courts ISO 3166 (Turkey, Czech Republic, East Timor), noms des associations
     * membres FIFA (England, Republic of Ireland…, ressortissants britanniques
     * codés GB : l'énumération FIFA Connect n'a pas de code infranational).
     */
    private const ALIASES = [
        'England' => 'GB', 'Scotland' => 'GB', 'Wales' => 'GB', 'Northern Ireland' => 'GB',
        'Republic of Ireland' => 'IE', 'Czech Republic' => 'CZ', 'Turkey' => 'TR', 'Ivory Coast' => 'CI',
        'Democratic Republic of the Congo' => 'CD', 'Republic of the Congo' => 'CG', 'Hong Kong' => 'HK',
        'Macau' => 'MO', 'Myanmar' => 'MM', 'East Timor' => 'TL', 'Palestine' => 'PS',
    ];

    private ?array $byName = null;

    /** @return array<string, string> code => nom français */
    public function options(): array
    {
        $options = collect(config('iso_countries', []))->map(fn ($names) => $names[0])->all();
        asort($options, SORT_LOCALE_STRING);

        return $options;
    }

    public function label(?string $code): ?string
    {
        return $code ? (config("iso_countries.{$code}")[0] ?? $code) : null;
    }

    public function code(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $candidate = strtoupper(trim($value));
        if (array_key_exists($candidate, config('iso_countries', []))) {
            return $candidate;
        }
        if ($this->byName === null) {
            $this->byName = [];
            foreach (config('iso_countries', []) as $code => $names) {
                foreach ($names as $name) {
                    $this->byName[$this->normalize($name)] = $code;
                }
            }
            foreach (self::ALIASES as $name => $code) {
                $this->byName[$this->normalize($name)] = $code;
            }
        }

        return $this->byName[$this->normalize($value)] ?? null;
    }

    private function normalize(string $value): string
    {
        return trim(preg_replace('/[^A-Z]+/', ' ', str_replace('&', ' AND ', strtoupper(Str::ascii($value)))));
    }
}
