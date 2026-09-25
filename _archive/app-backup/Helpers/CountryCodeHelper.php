<?php

namespace App\Helpers;

class CountryCodeHelper
{
    /**
     * Mappage des noms de pays français vers les codes ISO
     */
    private static $countryMapping = [
        // Europe
        'france' => 'FR',
        'angleterre' => 'GB-ENG',
        'england' => 'GB-ENG',
        'allemagne' => 'DE',
        'germany' => 'DE',
        'italie' => 'IT',
        'italy' => 'IT',
        'espagne' => 'ES',
        'spain' => 'ES',
        'portugal' => 'PT',
        'belgique' => 'BE',
        'belgium' => 'BE',
        'pays-bas' => 'NL',
        'netherlands' => 'NL',
        'suisse' => 'CH',
        'switzerland' => 'CH',
        'norvège' => 'NO',
        'norway' => 'NO',
        'suède' => 'SE',
        'sweden' => 'SE',
        'danemark' => 'DK',
        'denmark' => 'DK',
        'pologne' => 'PL',
        'poland' => 'PL',
        'république tchèque' => 'CZ',
        'czech republic' => 'CZ',
        'autriche' => 'AT',
        'austria' => 'AT',
        'hongrie' => 'HU',
        'hungary' => 'HU',
        'roumanie' => 'RO',
        'romania' => 'RO',
        'bulgarie' => 'BG',
        'bulgaria' => 'BG',
        'grèce' => 'GR',
        'greece' => 'GR',
        'croatie' => 'HR',
        'croatia' => 'HR',
        'serbie' => 'RS',
        'serbia' => 'RS',
        'ukraine' => 'UA',
        'ukraine' => 'UA',
        'russie' => 'RU',
        'russia' => 'RU',
        
        // Afrique
        'maroc' => 'MA',
        'morocco' => 'MA',
        'tunisie' => 'TN',
        'tunisia' => 'TN',
        'algérie' => 'DZ',
        'algeria' => 'DZ',
        'sénégal' => 'SN',
        'senegal' => 'SN',
        'mali' => 'ML',
        'mali' => 'ML',
        'côte d\'ivoire' => 'CI',
        'ivory coast' => 'CI',
        'nigeria' => 'NG',
        'nigeria' => 'NG',
        'ghana' => 'GH',
        'ghana' => 'GH',
        'cameroun' => 'CM',
        'cameroon' => 'CM',
        'égypte' => 'EG',
        'egypt' => 'EG',
        'afrique du sud' => 'ZA',
        'south africa' => 'ZA',
        'kenya' => 'KE',
        'kenya' => 'KE',
        'ouganda' => 'UG',
        'uganda' => 'UG',
        'tanzanie' => 'TZ',
        'tanzania' => 'TZ',
        'zimbabwe' => 'ZW',
        'zimbabwe' => 'ZW',
        'zambie' => 'ZM',
        'zambia' => 'ZM',
        'botswana' => 'BW',
        'botswana' => 'BW',
        'namibie' => 'NA',
        'namibia' => 'NA',
        'angola' => 'AO',
        'angola' => 'AO',
        'mozambique' => 'MZ',
        'mozambique' => 'MZ',
        'madagascar' => 'MG',
        'madagascar' => 'MG',
        'comores' => 'KM',
        'comoros' => 'KM',
        'seychelles' => 'SC',
        'seychelles' => 'SC',
        'maurice' => 'MU',
        'mauritius' => 'MU',
        
        // Amérique du Sud
        'brésil' => 'BR',
        'brazil' => 'BR',
        'argentine' => 'AR',
        'argentina' => 'AR',
        'uruguay' => 'UY',
        'uruguay' => 'UY',
        'paraguay' => 'PY',
        'paraguay' => 'PY',
        'chili' => 'CL',
        'chile' => 'CL',
        'pérou' => 'PE',
        'peru' => 'PE',
        'colombie' => 'CO',
        'colombia' => 'CO',
        'venezuela' => 'VE',
        'venezuela' => 'VE',
        'équateur' => 'EC',
        'ecuador' => 'EC',
        'bolivie' => 'BO',
        'bolivia' => 'BO',
        'guyane' => 'GY',
        'guyana' => 'GY',
        
        // Amérique du Nord
        'états-unis' => 'US',
        'united states' => 'US',
        'canada' => 'CA',
        'canada' => 'CA',
        'mexique' => 'MX',
        'mexico' => 'MX',
        'costa rica' => 'CR',
        'costa rica' => 'CR',
        'panama' => 'PA',
        'panama' => 'PA',
        'honduras' => 'HN',
        'honduras' => 'HN',
        'guatemala' => 'GT',
        'guatemala' => 'GT',
        'el salvador' => 'SV',
        'el salvador' => 'SV',
        'nicaragua' => 'NI',
        'nicaragua' => 'NI',
        'belize' => 'BZ',
        'belize' => 'BZ',
        
        // Asie
        'japon' => 'JP',
        'japan' => 'JP',
        'corée du sud' => 'KR',
        'south korea' => 'KR',
        'chine' => 'CN',
        'china' => 'CN',
        'inde' => 'IN',
        'india' => 'IN',
        'iran' => 'IR',
        'iran' => 'IR',
        'irak' => 'IQ',
        'iraq' => 'IQ',
        'arabie saoudite' => 'SA',
        'saudi arabia' => 'SA',
        'qatar' => 'QA',
        'qatar' => 'QA',
        'émirats arabes unis' => 'AE',
        'united arab emirates' => 'AE',
        'kuwait' => 'KW',
        'kuwait' => 'KW',
        'bahreïn' => 'BH',
        'bahrain' => 'BH',
        'oman' => 'OM',
        'oman' => 'OM',
        'yémen' => 'YE',
        'yemen' => 'YE',
        'jordanie' => 'JO',
        'jordan' => 'JO',
        'liban' => 'LB',
        'lebanon' => 'LB',
        'syrie' => 'SY',
        'syria' => 'SY',
        'israël' => 'IL',
        'israel' => 'IL',
        'palestine' => 'PS',
        'palestine' => 'PS',
        'turquie' => 'TR',
        'turkey' => 'TR',
        'géorgie' => 'GE',
        'georgia' => 'GE',
        'arménie' => 'AM',
        'armenia' => 'AM',
        'azerbaïdjan' => 'AZ',
        'azerbaijan' => 'AZ',
        'kazakhstan' => 'KZ',
        'kazakhstan' => 'KZ',
        'ouzbékistan' => 'UZ',
        'uzbekistan' => 'UZ',
        'tadjikistan' => 'TJ',
        'tajikistan' => 'TJ',
        'kirghizistan' => 'KG',
        'kyrgyzstan' => 'KG',
        'turkmenistan' => 'TM',
        'turkmenistan' => 'TM',
        'afghanistan' => 'AF',
        'afghanistan' => 'AF',
        'pakistan' => 'PK',
        'pakistan' => 'PK',
        'bangladesh' => 'BD',
        'bangladesh' => 'BD',
        'sri lanka' => 'LK',
        'sri lanka' => 'LK',
        'népal' => 'NP',
        'nepal' => 'NP',
        'bhoutan' => 'BT',
        'bhutan' => 'BT',
        'myanmar' => 'MM',
        'myanmar' => 'MM',
        'thailande' => 'TH',
        'thailand' => 'TH',
        'laos' => 'LA',
        'laos' => 'LA',
        'cambodge' => 'KH',
        'cambodia' => 'KH',
        'vietnam' => 'VN',
        'vietnam' => 'VN',
        'malaisie' => 'MY',
        'malaysia' => 'MY',
        'singapour' => 'SG',
        'singapore' => 'SG',
        'indonésie' => 'ID',
        'indonesia' => 'ID',
        'philippines' => 'PH',
        'philippines' => 'PH',
        'brunei' => 'BN',
        'brunei' => 'BN',
        'timor oriental' => 'TL',
        'east timor' => 'TL',
        
        // Océanie
        'australie' => 'AU',
        'australia' => 'AU',
        'nouvelle-zélande' => 'NZ',
        'new zealand' => 'NZ',
        'fidji' => 'FJ',
        'fiji' => 'FJ',
        'papouasie-nouvelle-guinée' => 'PG',
        'papua new guinea' => 'PG',
        'îles salomon' => 'SB',
        'solomon islands' => 'SB',
        'vanuatu' => 'VU',
        'vanuatu' => 'VU',
        'nouvelle-calédonie' => 'NC',
        'new caledonia' => 'NC',
        'polynésie française' => 'PF',
        'french polynesia' => 'PF',
        'wallis-et-futuna' => 'WF',
        'wallis and futuna' => 'WF',
        
        // Cas spéciaux
        'ftf' => 'TN', // Fédération Tunisienne de Football
        'faf' => 'DZ', // Fédération Algérienne de Football
        'frmf' => 'MA', // Fédération Royale Marocaine de Football
        'fsf' => 'SN', // Fédération Sénégalaise de Football
        'fbf' => 'BF', // Fédération Burkinabé de Football
        'fif' => 'CI', // Fédération Ivoirienne de Football
        'fmn' => 'ML', // Fédération Malienne de Football
        'fnn' => 'NG', // Fédération Nigériane de Football
        'fgh' => 'GH', // Fédération Ghanéenne de Football
        'fecafoot' => 'CM', // Fédération Camerounaise de Football
        'efa' => 'EG', // Fédération Égyptienne de Football
        'safa' => 'ZA', // Fédération Sud-Africaine de Football
    ];

    /**
     * Convertit un nom de pays en code ISO
     */
    public static function getCountryCode(string $countryName): ?string
    {
        if (empty($countryName)) {
            return null;
        }

        $normalizedName = strtolower(trim($countryName));
        
        // Vérifier d'abord la correspondance exacte
        if (isset(self::$countryMapping[$normalizedName])) {
            return self::$countryMapping[$normalizedName];
        }
        
        // Vérifier les correspondances partielles
        foreach (self::$countryMapping as $name => $code) {
            if (str_contains($name, $normalizedName) || str_contains($normalizedName, $name)) {
                return $code;
            }
        }
        
        return null;
    }

    /**
     * Vérifie si un code de pays est valide
     */
    public static function isValidCountryCode(string $countryCode): bool
    {
        return in_array(strtoupper($countryCode), array_values(self::$countryMapping));
    }

    /**
     * Obtient tous les codes de pays disponibles
     */
    public static function getAllCountryCodes(): array
    {
        return array_values(self::$countryMapping);
    }

    /**
     * Obtient tous les noms de pays disponibles
     */
    public static function getAllCountryNames(): array
    {
        return array_keys(self::$countryMapping);
    }
}

