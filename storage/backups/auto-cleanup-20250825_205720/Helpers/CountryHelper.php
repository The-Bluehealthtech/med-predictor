<?php

namespace App\Helpers;

class CountryHelper
{
    /**
     * Mapping des noms de pays vers leurs codes ISO 3166-1 alpha-2
     * Basé sur la norme internationale
     */
    private static $countryMapping = [
        // Europe
        'france' => 'FR',
        'england' => 'GB',
        'united kingdom' => 'GB',
        'great britain' => 'GB',
        'germany' => 'DE',
        'deutschland' => 'DE',
        'spain' => 'ES',
        'espana' => 'ES',
        'italy' => 'IT',
        'italia' => 'IT',
        'norway' => 'NO',
        'norge' => 'NO',
        'belgium' => 'BE',
        'belgique' => 'BE',
        'belgie' => 'BE',
        'portugal' => 'PT',
        'switzerland' => 'CH',
        'schweiz' => 'CH',
        'suisse' => 'CH',
        'netherlands' => 'NL',
        'holland' => 'NL',
        'nederland' => 'NL',
        'sweden' => 'SE',
        'sverige' => 'SE',
        'denmark' => 'DK',
        'danmark' => 'DK',
        'finland' => 'FI',
        'suomi' => 'FI',
        'poland' => 'PL',
        'polska' => 'PL',
        'austria' => 'AT',
        'osterreich' => 'AT',
        'greece' => 'GR',
        'hellas' => 'GR',
        'czech republic' => 'CZ',
        'czechia' => 'CZ',
        'hungary' => 'HU',
        'magyarorszag' => 'HU',
        'romania' => 'RO',
        'russia' => 'RU',
        'russian federation' => 'RU',
        'ukraine' => 'UA',
        'croatia' => 'HR',
        'hrvatska' => 'HR',
        'serbia' => 'RS',
        'slovenia' => 'SI',
        'slovenija' => 'SI',
        'slovakia' => 'SK',
        'slovensko' => 'SK',
        'bulgaria' => 'BG',
        'bulgariya' => 'BG',
        'lithuania' => 'LT',
        'lietuva' => 'LT',
        'latvia' => 'LV',
        'latvija' => 'LV',
        'estonia' => 'EE',
        'eesti' => 'EE',
        'ireland' => 'IE',
        'eire' => 'IE',
        'iceland' => 'IS',
        'island' => 'IS',
        'luxembourg' => 'LU',
        'malta' => 'MT',
        'cyprus' => 'CY',
        'kypros' => 'CY',
        
        // Afrique
        'morocco' => 'MA',
        'maroc' => 'MA',
        'tunisia' => 'TN',
        'tunisie' => 'TN',
        'algeria' => 'DZ',
        'algerie' => 'DZ',
        'egypt' => 'EG',
        'misr' => 'EG',
        'senegal' => 'SN',
        'cameroon' => 'CM',
        'cameroun' => 'CM',
        'nigeria' => 'NG',
        'ghana' => 'GH',
        'south africa' => 'ZA',
        'zuid-afrika' => 'ZA',
        'kenya' => 'KE',
        'ethiopia' => 'ET',
        'uganda' => 'UG',
        'tanzania' => 'TZ',
        'zambia' => 'ZM',
        'zimbabwe' => 'ZW',
        'angola' => 'AO',
        'mozambique' => 'MZ',
        'madagascar' => 'MG',
        'ivory coast' => 'CI',
        'cote d\'ivoire' => 'CI',
        'mali' => 'ML',
        'burkina faso' => 'BF',
        'niger' => 'NE',
        'chad' => 'TD',
        'sudan' => 'SD',
        'libya' => 'LY',
        'libya' => 'LY',
        'somalia' => 'SO',
        'djibouti' => 'DJ',
        'eritrea' => 'ER',
        'comoros' => 'KM',
        'mauritius' => 'MU',
        'seychelles' => 'SC',
        'cape verde' => 'CV',
        'cabo verde' => 'CV',
        'guinea-bissau' => 'GW',
        'guinea' => 'GN',
        'sierra leone' => 'SL',
        'liberia' => 'LR',
        'togo' => 'TG',
        'benin' => 'BJ',
        'gabon' => 'GA',
        'congo' => 'CG',
        'central african republic' => 'CF',
        'chad' => 'TD',
        'north sudan' => 'SD',
        'south sudan' => 'SS',
        'equatorial guinea' => 'GQ',
        'sao tome and principe' => 'ST',
        'namibia' => 'NA',
        'botswana' => 'BW',
        'lesotho' => 'LS',
        'eswatini' => 'SZ',
        'swaziland' => 'SZ',
        
        // Amérique du Nord
        'united states' => 'US',
        'usa' => 'US',
        'united states of america' => 'US',
        'canada' => 'CA',
        'mexico' => 'MX',
        'mexique' => 'MX',
        
        // Amérique du Sud
        'brazil' => 'BR',
        'brasil' => 'BR',
        'argentina' => 'AR',
        'chile' => 'CL',
        'peru' => 'PE',
        'colombia' => 'CO',
        'venezuela' => 'VE',
        'ecuador' => 'EC',
        'bolivia' => 'BO',
        'paraguay' => 'PY',
        'uruguay' => 'UY',
        'guyana' => 'GY',
        'suriname' => 'SR',
        'french guiana' => 'GF',
        'guyane' => 'GF',
        
        // Asie
        'china' => 'CN',
        'japan' => 'JP',
        'japan' => 'JP',
        'south korea' => 'KR',
        'republic of korea' => 'KR',
        'north korea' => 'KP',
        'democratic people\'s republic of korea' => 'KP',
        'india' => 'IN',
        'pakistan' => 'PK',
        'bangladesh' => 'BD',
        'sri lanka' => 'LK',
        'nepal' => 'NP',
        'bhutan' => 'BT',
        'myanmar' => 'MM',
        'burma' => 'MM',
        'thailand' => 'TH',
        'vietnam' => 'VN',
        'cambodia' => 'KH',
        'laos' => 'LA',
        'malaysia' => 'MY',
        'singapore' => 'SG',
        'indonesia' => 'ID',
        'philippines' => 'PH',
        'taiwan' => 'TW',
        'mongolia' => 'MN',
        'kazakhstan' => 'KZ',
        'uzbekistan' => 'UZ',
        'kyrgyzstan' => 'KG',
        'tajikistan' => 'TJ',
        'turkmenistan' => 'TM',
        'afghanistan' => 'AF',
        'iran' => 'IR',
        'iraq' => 'IQ',
        'syria' => 'SY',
        'lebanon' => 'LB',
        'jordan' => 'JO',
        'israel' => 'IL',
        'palestine' => 'PS',
        'saudi arabia' => 'SA',
        'yemen' => 'YE',
        'oman' => 'OM',
        'united arab emirates' => 'AE',
        'uae' => 'AE',
        'qatar' => 'QA',
        'kuwait' => 'KW',
        'bahrain' => 'BH',
        
        // Océanie
        'australia' => 'AU',
        'new zealand' => 'NZ',
        'aotearoa' => 'NZ',
        'fiji' => 'FJ',
        'papua new guinea' => 'PG',
        'solomon islands' => 'SB',
        'vanuatu' => 'VU',
        'new caledonia' => 'NC',
        'nouvelle-caledonie' => 'NC',
        'french polynesia' => 'PF',
        'polynesie francaise' => 'PF',
        
        // Amérique centrale et Caraïbes
        'costa rica' => 'CR',
        'panama' => 'PA',
        'nicaragua' => 'NI',
        'honduras' => 'HN',
        'el salvador' => 'SV',
        'guatemala' => 'GT',
        'belize' => 'BZ',
        'cuba' => 'CU',
        'jamaica' => 'JM',
        'haiti' => 'HT',
        'dominican republic' => 'DO',
        'republica dominicana' => 'DO',
        'puerto rico' => 'PR',
        'trinidad and tobago' => 'TT',
        'barbados' => 'BB',
        'bahamas' => 'BS',
        'antigua and barbuda' => 'AG',
        'saint kitts and nevis' => 'KN',
        'saint lucia' => 'LC',
        'saint vincent and the grenadines' => 'VC',
        'grenada' => 'GD',
        'dominica' => 'DM'
    ];

    /**
     * Convertit un nom de pays en code ISO 3166-1 alpha-2
     *
     * @param string|null $countryName
     * @return string|null
     */
    public static function getCountryCode(?string $countryName): ?string
    {
        if (!$countryName) {
            return null;
        }

        $normalizedName = strtolower(trim($countryName));
        
        // Recherche directe
        if (isset(self::$countryMapping[$normalizedName])) {
            return self::$countryMapping[$normalizedName];
        }
        
        // Recherche partielle (pour gérer les variations)
        foreach (self::$countryMapping as $name => $code) {
            if (str_contains($name, $normalizedName) || str_contains($normalizedName, $name)) {
                return $code;
            }
        }
        
        return null;
    }

    /**
     * Vérifie si un code pays est valide
     *
     * @param string|null $countryCode
     * @return bool
     */
    public static function isValidCountryCode(?string $countryCode): bool
    {
        if (!$countryCode) {
            return false;
        }
        
        return preg_match('/^[A-Z]{2}$/i', $countryCode) && 
               in_array(strtoupper($countryCode), array_values(self::$countryMapping));
    }

    /**
     * Génère l'URL du drapeau depuis flagcdn.com
     *
     * @param string|null $countryCode
     * @param string $format
     * @param string $size
     * @return string|null
     */
    public static function getFlagUrl(?string $countryCode, string $format = 'svg', string $size = 'md'): ?string
    {
        if (!self::isValidCountryCode($countryCode)) {
            return null;
        }

        $code = strtolower($countryCode);
        
        if ($format === 'svg') {
            return "https://flagcdn.com/{$code}.svg";
        }
        
        // Pour PNG, on utilise une taille appropriée
        $pngSizes = [
            'xs' => 'w40',
            'sm' => 'w80',
            'md' => 'w120',
            'lg' => 'w160',
            'xl' => 'w240',
            '2xl' => 'w320'
        ];
        
        $pngSize = $pngSizes[$size] ?? $pngSizes['md'];
        return "https://flagcdn.com/{$pngSize}/{$code}.png";
    }

    /**
     * Liste de tous les codes pays disponibles
     *
     * @return array
     */
    public static function getAllCountryCodes(): array
    {
        return array_values(self::$countryMapping);
    }

    /**
     * Liste de tous les noms de pays disponibles
     *
     * @return array
     */
    public static function getAllCountryNames(): array
    {
        return array_keys(self::$countryMapping);
    }
}

