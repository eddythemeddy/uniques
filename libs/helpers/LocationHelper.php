<?php

class LocationHelper
{
    private static $countries = array();

    protected $nationalities = array (
        'AF' => 'Afghan',
        'AL' => 'Albanian',
        'DZ' => 'Algerian',
        'US' => 'American',
        'AD' => 'Andorran',
        'AO' => 'Angolan',
        'AG' => 'Antiguans',
        'AR' => 'Argentinean',
        'AM' => 'Armenian',
        'AU' => 'Australian',
        'AT' => 'Austrian',
        'AZ' => 'Azerbaijani',
        'BS' => 'Bahamian',
        'BH' => 'Bahraini',
        'BD' => 'Bangladeshi',
        'BB' => 'Barbudans',
        'BW' => 'Batswana',
        'BY' => 'Belarusian',
        'BE' => 'Belgian',
        'BZ' => 'Belizean',
        'BJ' => 'Beninese',
        'BT' => 'Bhutanese',
        'BO' => 'Bolivian',
        'BA' => 'Bosnian',
        'BR' => 'Brazilian',
        'GB' => 'British',
        'BN' => 'Bruneian',
        'BG' => 'Bulgarian',
        'BF' => 'Burkinabe',
        'MM' => 'Burmese',
        'BI' => 'Burundian',
        'KH' => 'Cambodian',
        'CM' => 'Cameroonian',
        'CA' => 'Canadian',
        'CV' => 'Cape Verdean',
        'CF' => 'Central African',
        'TD' => 'Chadian',
        'CL' => 'Chilean',
        'CN' => 'Chinese',
        'CO' => 'Colombian',
        'KM' => 'Comoran',
        'CG' => 'Congolese',
        'CR' => 'Costa Rican',
        'HR' => 'Croatian',
        'CU' => 'Cuban',
        'CY' => 'Cypriot',
        'CZ' => 'Czech',
        'DK' => 'Danish',
        'DJ' => 'Djibouti',
        'DO' => 'Dominican',
        'NL' => 'Dutch',
        'TL' => 'East Timorese',
        'EC' => 'Ecuadorean',
        'EG' => 'Egyptian',
        'AE' => 'Emirian',
        'GB-ENG' => 'English',
        'GQ' => 'Equatorial Guinean',
        'ER' => 'Eritrean',
        'EE' => 'Estonian',
        'ET' => 'Ethiopian',
        'FJ' => 'Fijian',
        'PH' => 'Filipino',
        'FI' => 'Finnish',
        'FR' => 'French',
        'GA' => 'Gabonese',
        'GM' => 'Gambian',
        'GE' => 'Georgian',
        'DE' => 'German',
        'GH' => 'Ghanaian',
        'GR' => 'Greek',
        'GD' => 'Grenadian',
        'GT' => 'Guatemalan',
        'GW' => 'Guinea-Bissauan',
        'GN' => 'Guinean',
        'GY' => 'Guyanese',
        'HT' => 'Haitian',
        'HN' => 'Honduran',
        'HU' => 'Hungarian',
        'KI' => 'I-Kiribati',
        'IS' => 'Icelander',
        'IN' => 'Indian',
        'ID' => 'Indonesian',
        'IR' => 'Iranian',
        'IQ' => 'Iraqi',
        'IE' => 'Irish',
        'IL' => 'Israeli',
        'IT' => 'Italian',
        'CI' => 'Ivorian',
        'JM' => 'Jamaican',
        'JP' => 'Japanese',
        'JO' => 'Jordanian',
        'KZ' => 'Kazakhstani',
        'KE' => 'Kenyan',
        'KN' => 'Kittian and Nevisian',
        'KW' => 'Kuwaiti',
        'KG' => 'Kyrgyz',
        'LA' => 'Laotian',
        'LV' => 'Latvian',
        'LB' => 'Lebanese',
        'LR' => 'Liberian',
        'LY' => 'Libyan',
        'LI' => 'Liechtensteiner',
        'LT' => 'Lithuanian',
        'LU' => 'Luxembourger',
        'MK' => 'Macedonian',
        'MG' => 'Malagasy',
        'MW' => 'Malawian',
        'MY' => 'Malaysian',
        'MV' => 'Maldivan',
        'ML' => 'Malian',
        'MT' => 'Maltese',
        'MH' => 'Marshallese',
        'MR' => 'Mauritanian',
        'MU' => 'Mauritian',
        'MX' => 'Mexican',
        'FM' => 'Micronesian',
        'MD' => 'Moldovan',
        'MC' => 'Monacan',
        'MN' => 'Mongolian',
        'MA' => 'Moroccan',
        'LS' => 'Mosotho',
        'MZ' => 'Mozambican',
        'NA' => 'Namibian',
        'NR' => 'Nauruan',
        'NP' => 'Nepalese',
        'NZ' => 'New Zealander',
        'NI' => 'Nicaraguan',
        'NE' => 'Nigerian (Niger)',
        'NG' => 'Nigerien (Nigeria)',
        'PRK' => 'North Korean',
        'NW' => 'Norwegian',
        'OM' => 'Omani',
        'PK' => 'Pakistani',
        'PW' => 'Palauan',
        'PA' => 'Panamanian',
        'PG' => 'Papua New Guinean',
        'PY' => 'Paraguayan',
        'PE' => 'Peruvian',
        'PL' => 'Polish',
        'PT' => 'Portuguese',
        'QA' => 'Qatari',
        'RO' => 'Romanian',
        'RU' => 'Russian',
        'RW' => 'Rwandan',
        'LC' => 'Saint Lucian',
        'SV' => 'Salvadoran',
        'WS' => 'Samoan',
        'SM' => 'San Marinese',
        'ST' => 'Sao Tomean',
        'SA' => 'Saudi',
        'GB-SCT' => 'Scottish',
        'SN' => 'Senegalese',
        'RS' => 'Serbian',
        'SC' => 'Seychellois',
        'SL' => 'Sierra Leonean',
        'SG' => 'Singaporean',
        'SK' => 'Slovakian',
        'SI' => 'Slovenian',
        'SB' => 'Solomon Islander',
        'SO' => 'Somali',
        'ZA' => 'South African',
        'KP' => 'South Korean',
        'ES' => 'Spanish',
        'LK' => 'Sri Lankan',
        'SD' => 'Sudanese',
        'SR' => 'Surinamer',
        'SZ' => 'Swazi',
        'SE' => 'Swedish',
        'CH' => 'Swiss',
        'SY' => 'Syrian',
        'TW' => 'Taiwanese',
        'TJ' => 'Tajik',
        'TZ' => 'Tanzanian',
        'TH' => 'Thai',
        'TG' => 'Togolese',
        'TO' => 'Tongan',
        'TT' => 'Trinidadian/Tobagonian',
        'TN' => 'Tunisian',
        'TR' => 'Turkish',
        'TV' => 'Tuvaluan',
        'UG' => 'Ugandan',
        'UA' => 'Ukrainian',
        'UY' => 'Uruguayan',
        'UZ' => 'Uzbekistani',
        'VE' => 'Venezuelan',
        'VN' => 'Vietnamese',
        'YE' => 'Yemenite',
        'GB-WLS' => 'Welsh',
        'ZM' => 'Zambian',
        'ZW' => 'Zimbabwean',
    );

    private static function load_countries() {
        self::$countries['AF'] = 'Afghanistan';
        self::$countries['AX'] = 'Aland Islands';
        self::$countries['AL'] = 'Albania';
        self::$countries['DZ'] = 'Algeria';
        self::$countries['AS'] = 'American Samoa';
        self::$countries['AD'] = 'Andorra';
        self::$countries['AO'] = 'Angola';
        self::$countries['AI'] = 'Anguilla';
        self::$countries['AQ'] = 'Antarctica';
        self::$countries['AG'] = 'Antigua & Barbuda';
        self::$countries['AR'] = 'Argentina';
        self::$countries['AM'] = 'Armenia';
        self::$countries['AW'] = 'Aruba';
        self::$countries['AU'] = 'Australia';
        self::$countries['AT'] = 'Austria';
        self::$countries['AZ'] = 'Azerbaijan';
        self::$countries['BS'] = 'Bahamas';
        self::$countries['BH'] = 'Bahrain';
        self::$countries['BD'] = 'Bangladesh';
        self::$countries['BB'] = 'Barbados';
        self::$countries['BY'] = 'Belarus';
        self::$countries['BE'] = 'Belgium';
        self::$countries['BZ'] = 'Belize';
        self::$countries['BJ'] = 'Benin';
        self::$countries['BM'] = 'Bermuda';
        self::$countries['BT'] = 'Bhutan';
        self::$countries['BO'] = 'Bolivia';
        self::$countries['BA'] = 'Bosnia & Herzegovina';
        self::$countries['BW'] = 'Botswana';
        self::$countries['BV'] = 'Bouvet Island';
        self::$countries['BR'] = 'Brazil';
        self::$countries['IO'] = 'British Indian Ocean Territory';
        self::$countries['VG'] = 'British Virgin Islands';
        self::$countries['BN'] = 'Brunei';
        self::$countries['BG'] = 'Bulgaria';
        self::$countries['BF'] = 'Burkina Faso';
        self::$countries['BI'] = 'Burundi';
        self::$countries['KH'] = 'Cambodia';
        self::$countries['CM'] = 'Cameroon';
        self::$countries['CA'] = 'Canada';
        self::$countries['CV'] = 'Cape Verde';
        self::$countries['KY'] = 'Cayman Islands';
        self::$countries['CF'] = 'Central African Republic';
        self::$countries['TD'] = 'Chad';
        self::$countries['CL'] = 'Chile';
        self::$countries['CN'] = 'China';
        self::$countries['CX'] = 'Christmas Island';
        self::$countries['CC'] = 'Cocos (Keeling) Islands';
        self::$countries['CO'] = 'Colombia';
        self::$countries['KM'] = 'Comoros Islands';
        self::$countries['CG'] = 'Congo';
        self::$countries['CK'] = 'Cook Islands';
        self::$countries['CR'] = 'Costa Rica';
        self::$countries['HR'] = 'Croatia';
        self::$countries['CU'] = 'Cuba';
        self::$countries['CW'] = 'Cura&ccedil;ao';
        self::$countries['CY'] = 'Cyprus';
        self::$countries['CZ'] = 'Czech Republic';
        self::$countries['DK'] = 'Denmark';
        self::$countries['DJ'] = 'Djibouti';
        self::$countries['DM'] = 'Dominica';
        self::$countries['DO'] = 'Dominican Republic';
        self::$countries['TL'] = 'East Timor';
        self::$countries['EC'] = 'Ecuador';
        self::$countries['EG'] = 'Egypt';
        self::$countries['SV'] = 'El Salvador';
        self::$countries['GQ'] = 'Equatorial Guinea';
        self::$countries['ER'] = 'Eritrea';
        self::$countries['EE'] = 'Estonia';
        self::$countries['ET'] = 'Ethiopia';
        self::$countries['FK'] = 'Falkland Islands (Malvinas)';
        self::$countries['FO'] = 'Faroe Islands';
        self::$countries['FJ'] = 'Fiji';
        self::$countries['FI'] = 'Finland';
        self::$countries['FR'] = 'France';
        self::$countries['GF'] = 'French Guiana';
        self::$countries['PF'] = 'French Polynesia';
        self::$countries['TF'] = 'French Southern Territories';
        self::$countries['GA'] = 'Gabon';
        self::$countries['GM'] = 'Gambia';
        self::$countries['GE'] = 'Georgia';
        self::$countries['DE'] = 'Germany';
        self::$countries['GH'] = 'Ghana';
        self::$countries['GI'] = 'Gibraltar (UK)';
        self::$countries['GR'] = 'Greece';
        self::$countries['GL'] = 'Greenland (DK)';
        self::$countries['GD'] = 'Grenada';
        self::$countries['GP'] = 'Guadeloupe (FR)';
        self::$countries['GU'] = 'Guam (US)';
        self::$countries['GT'] = 'Guatemala';
        self::$countries['GG'] = 'Guernsey';
        self::$countries['GN'] = 'Guinea';
        self::$countries['GW'] = 'Guinea-Bissau';
        self::$countries['GY'] = 'Guyana';
        self::$countries['HT'] = 'Haiti';
        self::$countries['HM'] = 'Heard Island and Mcdonald Islands';
        self::$countries['HN'] = 'Honduras';
        self::$countries['HK'] = 'Hong Kong';
        self::$countries['HU'] = 'Hungary';
        self::$countries['IS'] = 'Iceland';
        self::$countries['IN'] = 'India';
        self::$countries['ID'] = 'Indonesia';
        self::$countries['IR'] = 'Iran';
        self::$countries['IQ'] = 'Iraq';
        self::$countries['IE'] = 'Ireland';
        self::$countries['IM'] = 'Isle of Man';
        self::$countries['IL'] = 'Israel';
        self::$countries['IT'] = 'Italy';
        self::$countries['CI'] = 'Ivory Coast';
        self::$countries['JM'] = 'Jamaica';
        self::$countries['JP'] = 'Japan';
        self::$countries['JE'] = 'Jersey';
        self::$countries['JO'] = 'Jordan';
        self::$countries['KZ'] = 'Kazakhstan';
        self::$countries['KE'] = 'Kenya';
        self::$countries['KI'] = 'Kiribati';
        self::$countries['KP'] = 'Korea, North';
        self::$countries['KR'] = 'Korea, Republic of';
        self::$countries['KW'] = 'Kuwait';
        self::$countries['KG'] = 'Kyrgyzstan';
        self::$countries['LA'] = 'Laos';
        self::$countries['LV'] = 'Latvia';
        self::$countries['LB'] = 'Lebanon';
        self::$countries['LS'] = 'Lesotho';
        self::$countries['LR'] = 'Liberia';
        self::$countries['LY'] = 'Libya';
        self::$countries['LI'] = 'Liechtenstein';
        self::$countries['LT'] = 'Lithuania';
        self::$countries['LU'] = 'Luxembourg';
        self::$countries['MO'] = 'Macao';
        self::$countries['MK'] = 'Macedonia';
        self::$countries['MG'] = 'Madagascar';
        self::$countries['MW'] = 'Malawi';
        self::$countries['MY'] = 'Malaysia';
        self::$countries['MV'] = 'Maldives';
        self::$countries['ML'] = 'Mali';
        self::$countries['MT'] = 'Malta';
        self::$countries['MH'] = 'Marshall Islands';
        self::$countries['MQ'] = 'Martinique (FR)';
        self::$countries['MR'] = 'Mauritania';
        self::$countries['MU'] = 'Mauritius';
        self::$countries['YT'] = 'Mayotte';
        self::$countries['MX'] = 'Mexico';
        self::$countries['FM'] = 'Micronesia';
        self::$countries['MD'] = 'Moldova';
        self::$countries['MC'] = 'Monaco';
        self::$countries['MN'] = 'Mongolia';
        self::$countries['ME'] = 'Montenegro';
        self::$countries['MS'] = 'Montserrat';
        self::$countries['MA'] = 'Morocco';
        self::$countries['MZ'] = 'Mozambique';
        self::$countries['MM'] = 'Myanmar';
        self::$countries['NA'] = 'Namibia';
        self::$countries['NR'] = 'Nauru';
        self::$countries['NP'] = 'Nepal';
        self::$countries['NL'] = 'Netherlands';
        self::$countries['AN'] = 'Netherlands Antilles (NL)';
        self::$countries['NC'] = 'New Caledonia (FR)';
        self::$countries['NZ'] = 'New Zealand';
        self::$countries['NI'] = 'Nicaragua';
        self::$countries['NE'] = 'Niger';
        self::$countries['NG'] = 'Nigeria';
        self::$countries['NU'] = 'Niue';
        self::$countries['NF'] = 'Norfolk Island';
        self::$countries['MP'] = 'Northern Mariana Islands';
        self::$countries['NO'] = 'Norway';
        self::$countries['OM'] = 'Oman';
        self::$countries['XX'] = 'Other';
        self::$countries['PK'] = 'Pakistan';
        self::$countries['PW'] = 'Palau';
        self::$countries['PS'] = 'Palestine';
        self::$countries['PA'] = 'Panama';
        self::$countries['PG'] = 'Papua New Guinea';
        self::$countries['PY'] = 'Paraguay';
        self::$countries['PE'] = 'Peru';
        self::$countries['PH'] = 'Philippines';
        self::$countries['PN'] = 'Pitcairn';
        self::$countries['PL'] = 'Poland';
        self::$countries['PT'] = 'Portugal';
        self::$countries['PR'] = 'Puerto Rico';
        self::$countries['QA'] = 'Qatar';
        self::$countries['RE'] = 'Reunion';
        self::$countries['RO'] = 'Romania';
        self::$countries['RU'] = 'Russia';
        self::$countries['RW'] = 'Rwanda';
        self::$countries['SH'] = 'Saint Helena';
        self::$countries['SM'] = 'San Marino';
        self::$countries['ST'] = 'Sao Tome & Principe';
        self::$countries['SA'] = 'Saudi Arabia';
        self::$countries['SN'] = 'Senegal';
        self::$countries['RS'] = 'Serbia';
        self::$countries['SC'] = 'Seychelles';
        self::$countries['SL'] = 'Sierra Leone';
        self::$countries['SG'] = 'Singapore';
        self::$countries['SK'] = 'Slovakia';
        self::$countries['SI'] = 'Slovenia';
        self::$countries['SB'] = 'Solomon Islands';
        self::$countries['SO'] = 'Somalia';
        self::$countries['ZA'] = 'South Africa';
        self::$countries['GS'] = 'South Georgia and the South Sandwich Islands';
        self::$countries['ES'] = 'Spain';
        self::$countries['LK'] = 'Sri Lanka';
        self::$countries['BL'] = 'St. Barthilemy';
        self::$countries['KN'] = 'St. Kits & Nevis';
        self::$countries['LC'] = 'St. Lucia';
        self::$countries['MF'] = 'St. Maarten/St. Martin';
        self::$countries['PM'] = 'St. Pierre et Miquelon';
        self::$countries['VC'] = 'St. Vincent & the Grenadines';
        self::$countries['SD'] = 'Sudan';
        self::$countries['SR'] = 'Suriname';
        self::$countries['SJ'] = 'Svalbard and Jan Mayen';
        self::$countries['SZ'] = 'Swaziland';
        self::$countries['SE'] = 'Sweden';
        self::$countries['CH'] = 'Switzerland';
        self::$countries['SY'] = 'Syria';
        self::$countries['TW'] = 'Taiwan';
        self::$countries['TJ'] = 'Tajikistan';
        self::$countries['TZ'] = 'Tanzania';
        self::$countries['TH'] = 'Thailand';
        self::$countries['TG'] = 'Togo';
        self::$countries['TK'] = 'Tokelau';
        self::$countries['TO'] = 'Tonga';
        self::$countries['TT'] = 'Trinidad and Tobago';
        self::$countries['TN'] = 'Tunisia';
        self::$countries['TR'] = 'Turkey';
        self::$countries['TM'] = 'Turkmenistan';
        self::$countries['TC'] = 'Turks and Caicos Islands (UK)';
        self::$countries['TV'] = 'Tuvalu';
        self::$countries['UG'] = 'Uganda';
        self::$countries['UA'] = 'Ukraine';
        self::$countries['AE'] = 'United Arab Emirates';
        self::$countries['GB'] = 'United Kingdom';
        self::$countries['US'] = 'United States';
        self::$countries['UM'] = 'United States Minor Outlying Islands';
        self::$countries['UY'] = 'Uruguay';
        self::$countries['UZ'] = 'Uzbekistan';
        self::$countries['VU'] = 'Vanuatu';
        self::$countries['VA'] = 'Vatican City State';
        self::$countries['VE'] = 'Venezuela';
        self::$countries['VN'] = 'Vietnam';
        self::$countries['VI'] = 'Virgin Islands (US)';
        self::$countries['WF'] = 'Wallis and Futuna';
        self::$countries['EH'] = 'Western Sahara';
        self::$countries['WS'] = 'Western Samoa';
        self::$countries['YE'] = 'Yemen';
        self::$countries['CD'] = 'Zaire';
        self::$countries['ZM'] = 'Zambia';
        self::$countries['ZW'] = 'Zimbabwe';
    }

    /**
     * Get country name from code
     * @param string $code
     */
    public static function get_country_name($code) {
        if(empty(self::$countries)) {
            self::load_countries();
        }

        return isset(self::$countries[$code]) ? self::$countries[$code] : $code;
    }

    /**
     * Get country name from code
     *
     * @deprecated use get_country_name
     * @param string $code
     */
    public function getCountryName($code) {
        return self::get_country_name($code);
    }

    /**
     * Get country name from code
     *
     * @deprecated use get_country_name
     * @param string $code
     */
    public function getNationalityName($code) {
        return $this->nationalities[$code];
    }

    public function getCountriesOptions($selected = false) {

        if(empty(self::$countries)) {
            self::load_countries();
        }
        $countries = self::$countries;
        $html = '';
        foreach($countries as $key => $val){
            $thisSel = '';
            if($selected) {
                if($selected == $key) { $thisSel = 'selected="selected"'; }
            }
            $html .= '<option value="' . $key . '" ' . $thisSel . '>' . $val . '</option>';
        }

        return $html;
    }

    public function getNationalitiesOptions($selected = false) {
        $countries = $this->nationalities;
        $html = '';
        foreach($countries as $key => $val){
            $thisSel = '';
            if($selected) {
                if($selected == $key) { $thisSel = 'selected="selected"'; }
            }
            $html .= '<option value="' . $key . '" ' . $thisSel . '>' . $val . '</option>';
        }
        return $html;
    }
}