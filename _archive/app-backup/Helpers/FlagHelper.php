<?php

namespace App\Helpers;

class FlagHelper
{
    /**
     * Obtenir l'URL du drapeau pour un pays donné
     */
    public static function getFlagUrl($countryName, $size = 128)
    {
        if (empty($countryName)) {
            return null;
        }

        $countryCode = self::getCountryCode(strtolower($countryName));
        
        if ($countryCode) {
            return "https://flagcdn.com/w{$size}/{$countryCode}.png";
        }

        return null;
    }

    /**
     * Convertir le nom du pays en code ISO
     */
    private static function getCountryCode($countryName)
    {
        $countryMap = [
            // Europe
            'france' => 'fr',
            'england' => 'gb',
            'germany' => 'de',
            'spain' => 'es',
            'italy' => 'it',
            'norway' => 'no',
            'belgium' => 'be',
            'portugal' => 'pt',
            'switzerland' => 'ch',
            'netherlands' => 'nl',
            'sweden' => 'se',
            'denmark' => 'dk',
            'poland' => 'pl',
            'austria' => 'at',
            'greece' => 'gr',
            'croatia' => 'hr',
            'serbia' => 'rs',
            'ukraine' => 'ua',
            'russia' => 'ru',
            
            // Afrique
            'morocco' => 'ma',
            'tunisia' => 'tn',
            'algeria' => 'dz',
            'egypt' => 'eg',
            'senegal' => 'sn',
            'cameroon' => 'cm',
            'nigeria' => 'ng',
            'ghana' => 'gh',
            'ivory coast' => 'ci',
            'mali' => 'ml',
            'burkina faso' => 'bf',
            'niger' => 'ne',
            'chad' => 'td',
            'sudan' => 'sd',
            'ethiopia' => 'et',
            'kenya' => 'ke',
            'uganda' => 'ug',
            'tanzania' => 'tz',
            'zambia' => 'zm',
            'zimbabwe' => 'zw',
            'south africa' => 'za',
            'angola' => 'ao',
            'congo' => 'cg',
            'dr congo' => 'cd',
            'gabon' => 'ga',
            'equatorial guinea' => 'gq',
            'central african republic' => 'cf',
            
            // Amérique du Sud
            'brazil' => 'br',
            'argentina' => 'ar',
            'uruguay' => 'uy',
            'paraguay' => 'py',
            'chile' => 'cl',
            'peru' => 'pe',
            'ecuador' => 'ec',
            'colombia' => 'co',
            'venezuela' => 've',
            'bolivia' => 'bo',
            'guyana' => 'gy',
            'suriname' => 'sr',
            
            // Amérique du Nord
            'united states' => 'us',
            'usa' => 'us',
            'canada' => 'ca',
            'mexico' => 'mx',
            'costa rica' => 'cr',
            'panama' => 'pa',
            'honduras' => 'hn',
            'el salvador' => 'sv',
            'guatemala' => 'gt',
            'belize' => 'bz',
            'nicaragua' => 'ni',
            
            // Asie
            'japan' => 'jp',
            'south korea' => 'kr',
            'china' => 'cn',
            'india' => 'in',
            'pakistan' => 'pk',
            'bangladesh' => 'bd',
            'sri lanka' => 'lk',
            'nepal' => 'np',
            'bhutan' => 'bt',
            'myanmar' => 'mm',
            'thailand' => 'th',
            'vietnam' => 'vn',
            'laos' => 'la',
            'cambodia' => 'kh',
            'malaysia' => 'my',
            'singapore' => 'sg',
            'indonesia' => 'id',
            'philippines' => 'ph',
            'taiwan' => 'tw',
            'hong kong' => 'hk',
            'macau' => 'mo',
            'mongolia' => 'mn',
            'kazakhstan' => 'kz',
            'uzbekistan' => 'uz',
            'turkmenistan' => 'tm',
            'kyrgyzstan' => 'kg',
            'tajikistan' => 'tj',
            'afghanistan' => 'af',
            'iran' => 'ir',
            'iraq' => 'iq',
            'syria' => 'sy',
            'lebanon' => 'lb',
            'jordan' => 'jo',
            'israel' => 'il',
            'palestine' => 'ps',
            'saudi arabia' => 'sa',
            'yemen' => 'ye',
            'oman' => 'om',
            'uae' => 'ae',
            'qatar' => 'qa',
            'kuwait' => 'kw',
            'bahrain' => 'bh',
            
            // Océanie
            'australia' => 'au',
            'new zealand' => 'nz',
            'fiji' => 'fj',
            'papua new guinea' => 'pg',
            'solomon islands' => 'sb',
            'vanuatu' => 'vu',
            'new caledonia' => 'nc',
            'french polynesia' => 'pf',
        ];

        return $countryMap[$countryName] ?? null;
    }
}
