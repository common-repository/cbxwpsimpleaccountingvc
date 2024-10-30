<?php
	// If this file is called directly, abort.
	if (!defined('WPINC')) {
		die;
	}

    /**
     * Class vcHelper
     * return country array
     */
    class CBXWPSimpleaccountingVCHelper
    {

        public static function getAllCountries()
        {
            $countries = array(
                'AF' => 'Afghanistan',
                'AL' => 'Albania',
                'DZ' => 'Algeria',
                'DS' => 'American Samoa',
                'AD' => 'Andorra',
                'AO' => 'Angola',
                'AI' => 'Anguilla',
                'AQ' => 'Antarctica',
                'AG' => 'Antigua and Barbuda',
                'AR' => 'Argentina',
                'AM' => 'Armenia',
                'AW' => 'Aruba',
                'AU' => 'Australia',
                'AT' => 'Austria',
                'AZ' => 'Azerbaijan',
                'BS' => 'Bahamas',
                'BH' => 'Bahrain',
                'BD' => 'Bangladesh',
                'BB' => 'Barbados',
                'BY' => 'Belarus',
                'BE' => 'Belgium',
                'BZ' => 'Belize',
                'BJ' => 'Benin',
                'BM' => 'Bermuda',
                'BT' => 'Bhutan',
                'BO' => 'Bolivia',
                'BA' => 'Bosnia and Herzegovina',
                'BW' => 'Botswana',
                'BV' => 'Bouvet Island',
                'BR' => 'Brazil',
                'IO' => 'British Indian Ocean Territory',
                'BN' => 'Brunei Darussalam',
                'BG' => 'Bulgaria',
                'BF' => 'Burkina Faso',
                'BI' => 'Burundi',
                'KH' => 'Cambodia',
                'CM' => 'Cameroon',
                'CA' => 'Canada',
                'CV' => 'Cape Verde',
                'KY' => 'Cayman Islands',
                'CF' => 'Central African Republic',
                'TD' => 'Chad',
                'CL' => 'Chile',
                'CN' => 'China',
                'CX' => 'Christmas Island',
                'CC' => 'Cocos (Keeling) Islands',
                'CO' => 'Colombia',
                'KM' => 'Comoros',
                'CG' => 'Congo',
                'CK' => 'Cook Islands',
                'CR' => 'Costa Rica',
                'HR' => 'Croatia (Hrvatska)',
                'CU' => 'Cuba',
                'CY' => 'Cyprus',
                'CZ' => 'Czech Republic',
                'DK' => 'Denmark',
                'DJ' => 'Djibouti',
                'DM' => 'Dominica',
                'DO' => 'Dominican Republic',
                'TP' => 'East Timor',
                'EC' => 'Ecuador',
                'EG' => 'Egypt',
                'SV' => 'El Salvador',
                'GQ' => 'Equatorial Guinea',
                'ER' => 'Eritrea',
                'EE' => 'Estonia',
                'ET' => 'Ethiopia',
                'FK' => 'Falkland Islands (Malvinas)',
                'FO' => 'Faroe Islands',
                'FJ' => 'Fiji',
                'FI' => 'Finland',
                'FR' => 'France',
                'FX' => 'France, Metropolitan',
                'GF' => 'French Guiana',
                'PF' => 'French Polynesia',
                'TF' => 'French Southern Territories',
                'GA' => 'Gabon',
                'GM' => 'Gambia',
                'GE' => 'Georgia',
                'DE' => 'Germany',
                'GH' => 'Ghana',
                'GI' => 'Gibraltar',
                'GK' => 'Guernsey',
                'GR' => 'Greece',
                'GL' => 'Greenland',
                'GD' => 'Grenada',
                'GP' => 'Guadeloupe',
                'GU' => 'Guam',
                'GT' => 'Guatemala',
                'GN' => 'Guinea',
                'GW' => 'Guinea-Bissau',
                'GY' => 'Guyana',
                'HT' => 'Haiti',
                'HM' => 'Heard and Mc Donald Islands',
                'HN' => 'Honduras',
                'HK' => 'Hong Kong',
                'HU' => 'Hungary',
                'IS' => 'Iceland',
                'IN' => 'India',
                'IM' => 'Isle of Man',
                'ID' => 'Indonesia',
                'IR' => 'Iran (Islamic Republic of)',
                'IQ' => 'Iraq',
                'IE' => 'Ireland',
                'IL' => 'Israel',
                'IT' => 'Italy',
                'CI' => 'Ivory Coast',
                'JE' => 'Jersey',
                'JM' => 'Jamaica',
                'JP' => 'Japan',
                'JO' => 'Jordan',
                'KZ' => 'Kazakhstan',
                'KE' => 'Kenya',
                'KI' => 'Kiribati',
                'KP' => "Korea, Democratic People's Republic of",
                'KR' => 'Korea, Republic of',
                'XK' => 'Kosovo',
                'KW' => 'Kuwait',
                'KG' => 'Kyrgyzstan',
                'LA' => "Lao People's Democratic Republic",
                'LV' => 'Latvia',
                'LB' => 'Lebanon',
                'LS' => 'Lesotho',
                'LR' => 'Liberia',
                'LY' => 'Libyan Arab Jamahiriya',
                'LI' => 'Liechtenstein',
                'LT' => 'Lithuania',
                'LU' => 'Luxembourg',
                'MO' => 'Macau',
                'MK' => 'Macedonia',
                'MG' => 'Madagascar',
                'MW' => 'Malawi',
                'MY' => 'Malaysia',
                'MV' => 'Maldives',
                'ML' => 'Mali',
                'MT' => 'Malta',
                'MH' => 'Marshall Islands',
                'MQ' => 'Martinique',
                'MR' => 'Mauritania',
                'MU' => 'Mauritius',
                'TY' => 'Mayotte',
                'MX' => 'Mexico',
                'FM' => 'Micronesia, Federated States of',
                'MD' => 'Moldova, Republic of',
                'MC' => 'Monaco',
                'MN' => 'Mongolia',
                'ME' => 'Montenegro',
                'MS' => 'Montserrat',
                'MA' => 'Morocco',
                'MZ' => 'Mozambique',
                'MM' => 'Myanmar',
                'NA' => 'Namibia',
                'NR' => 'Nauru',
                'NP' => 'Nepal',
                'NL' => 'Netherlands',
                'AN' => 'Netherlands Antilles',
                'NC' => 'New Caledonia',
                'NZ' => 'New Zealand',
                'NI' => 'Nicaragua',
                'NE' => 'Niger',
                'NG' => 'Nigeria',
                'NU' => 'Niue',
                'NF' => 'Norfolk Island',
                'MP' => 'Northern Mariana Islands',
                'NO' => 'Norway',
                'OM' => 'Oman',
                'PK' => 'Pakistan',
                'PW' => 'Palau',
                'PS' => 'Palestine',
                'PA' => 'Panama',
                'PG' => 'Papua New Guinea',
                'PY' => 'Paraguay',
                'PE' => 'Peru',
                'PH' => 'Philippines',
                'PN' => 'Pitcairn',
                'PL' => 'Poland',
                'PT' => 'Portugal',
                'PR' => 'Puerto Rico',
                'QA' => 'Qatar',
                'RE' => 'Reunion',
                'RO' => 'Romania',
                'RU' => 'Russian Federation',
                'RW' => 'Rwanda',
                'KN' => 'Saint Kitts and Nevis',
                'LC' => 'Saint Lucia',
                'VC' => 'Saint Vincent and the Grenadines',
                'WS' => 'Samoa',
                'SM' => 'San Marino',
                'ST' => 'Sao Tome and Principe',
                'SA' => 'Saudi Arabia',
                'SN' => 'Senegal',
                'RS' => 'Serbia',
                'SC' => 'Seychelles',
                'SL' => 'Sierra Leone',
                'SG' => 'Singapore',
                'SK' => 'Slovakia',
                'SI' => 'Slovenia',
                'SB' => 'Solomon Islands',
                'SO' => 'Somalia',
                'ZA' => 'South Africa',
                'GS' => 'South Georgia South Sandwich Islands',
                'ES' => 'Spain',
                'LK' => 'Sri Lanka',
                'SH' => 'St. Helena',
                'PM' => 'St. Pierre and Miquelon',
                'SD' => 'Sudan',
                'SR' => 'Suriname',
                'SJ' => 'Svalbard and Jan Mayen Islands',
                'SZ' => 'Swaziland',
                'SE' => 'Sweden',
                'CH' => 'Switzerland',
                'SY' => 'Syrian Arab Republic',
                'TW' => 'Taiwan',
                'TJ' => 'Tajikistan',
                'TZ' => 'Tanzania, United Republic of',
                'TH' => 'Thailand',
                'TG' => 'Togo',
                'TK' => 'Tokelau',
                'TO' => 'Tonga',
                'TT' => 'Trinidad and Tobago',
                'TN' => 'Tunisia',
                'TR' => 'Turkey',
                'TM' => 'Turkmenistan',
                'TC' => 'Turks and Caicos Islands',
                'TV' => 'Tuvalu',
                'UG' => 'Uganda',
                'UA' => 'Ukraine',
                'AE' => 'United Arab Emirates',
                'GB' => 'United Kingdom',
                'US' => 'United States',
                'UM' => 'United States minor outlying islands',
                'UY' => 'Uruguay',
                'UZ' => 'Uzbekistan',
                'VU' => 'Vanuatu',
                'VA' => 'Vatican City State',
                'VE' => 'Venezuela',
                'VN' => 'Vietnam',
                'VG' => 'Virgin Islands (British)',
                'VI' => 'Virgin Islands (U.S.)',
                'WF' => 'Wallis and Futuna Islands',
                'EH' => 'Western Sahara',
                'YE' => 'Yemen',
                'ZR' => 'Zaire',
                'ZM' => 'Zambia',
                'ZW' => 'Zimbabwe'
            );

            return apply_filters('cbxwpsimpleaccountingvc_countries', $countries);
        }//end method getAllCountries

        public static function getCommunicationWay()        {
            $ways = array(
                'work'  => esc_html__('Work', 'cbxwpsimpleaccountingvc'),
                'home'  => esc_html__('Home', 'cbxwpsimpleaccountingvc'),
                'other' => esc_html__('Other', 'cbxwpsimpleaccountingvc'),
            );

            return apply_filters('cbxwpsimpleaccountingvc_communicationways', $ways);
        }//end method getCommunicationWay

        public static function getCommunicationWayName($phonetype)
        {
            $ways = self::getCommunicationWay();
            return isset($ways[$phonetype]) ? $ways[$phonetype] : '';
        }

        /**
         * Get vendor and client types
         *
         * @return mixed
         */
        public static function vcTypes()
        {
            $vc_types = array(
                'vendor' => esc_html__('Vendor', 'cbxwpsimpleaccountingvc'),
                'client' => esc_html__('Client', 'cbxwpsimpleaccountingvc'),
            );

            return apply_filters('cbxwpsimpleaccountingvc_vctypes', $vc_types);
        }

        /**
         * Get vendor type readable name
         *
         * @param $type
         *
         * @return mixed
         */
        public static function vcType($type)
        {
            $vc_types = self::vcTypes();

            return $vc_types[$type];
        }

	    /**
	     * Check if the category has any logs associated
	     *
	     * @param int $cat_id
	     *
	     * @return bool
	     */
	    public static function isVCEmpty($vc_id = 0){
		    global $wpdb;
		    $cbxexpinc_table                = $wpdb->prefix . 'cbaccounting_expinc'; //expinc table name

		    $vc_id = intval($vc_id);
		    $result = false;

		    if($vc_id == 0)return $result; //false is safe here

		    $query = $wpdb->prepare("SELECT * from $cbxexpinc_table WHERE  vc_id = %d LIMIT 0, 1", $vc_id);
		    $row = $wpdb->get_row($query, ARRAY_A);
		    if($row === null){
			    return true;
		    }

		    return $result;
	    }//end method isVCEmpty
    }//end class CBXWPSimpleaccountingVCHelper