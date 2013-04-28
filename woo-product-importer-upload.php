<?php /*
    This file is part of Woo Product Importer.

    Woo Product Importer is Copyright 2012-2013 Web Presence Partners LLC.

    Woo Product Importer is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Woo Product Importer is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with Woo Product Importer.  If not, see <http://www.gnu.org/licenses/>.
*/

    $country_codes = array(
        'AF' => "Afghanistan" ,
        'AL' => "Albania" ,
        'DZ' => "Algeria" ,
        'AS' => "American Samoa" ,
        'AD' => "Andorra" ,
        'AO' => "Angola" ,
        'AI' => "Anguilla" ,
        'AQ' => "Antarctica" ,
        'AG' => "Antigua And Barbuda" ,
        'AR' => "Argentina" ,
        'AM' => "Armenia" ,
        'AW' => "Aruba" ,
        'AU' => "Australia" ,
        'AT' => "Austria" ,
        'AZ' => "Azerbaijan" ,
        'BS' => "Bahamas" ,
        'BH' => "Bahrain" ,
        'BD' => "Bangladesh" ,
        'BB' => "Barbados" ,
        'BY' => "Belarus" ,
        'BE' => "Belgium" ,
        'BZ' => "Belize" ,
        'BJ' => "Benin" ,
        'BM' => "Bermuda" ,
        'BT' => "Bhutan" ,
        'BO' => "Bolivia" ,
        'BA' => "Bosnia And Herzegovina" ,
        'BW' => "Botswana" ,
        'BV' => "Bouvet Island" ,
        'BR' => "Brazil" ,
        'IO' => "British Indian Ocean Territory" ,
        'BN' => "Brunei Darussalam" ,
        'BG' => "Bulgaria" ,
        'BF' => "Burkina Faso" ,
        'BI' => "Burundi" ,
        'KH' => "Cambodia" ,
        'CM' => "Cameroon" ,
        'CA' => "Canada" ,
        'CV' => "Cape Verde" ,
        'KY' => "Cayman Islands" ,
        'CF' => "Central African Republic" ,
        'TD' => "Chad" ,
        'CL' => "Chile" ,
        'CN' => "China" ,
        'CX' => "Christmas Island" ,
        'CC' => "Cocos (Keeling) Islands" ,
        'CO' => "Colombia" ,
        'KM' => "Comoros" ,
        'CG' => "Congo" ,
        'CD' => "Congo, The Democratic Republic Of The" ,
        'CK' => "Cook Islands" ,
        'CR' => "Costa Rica" ,
        'CI' => "Cote D'ivoire" ,
        'HR' => "Croatia" ,
        'CU' => "Cuba" ,
        'CY' => "Cyprus" ,
        'CZ' => "Czech Republic" ,
        'DK' => "Denmark" ,
        'DJ' => "Djibouti" ,
        'DM' => "Dominica" ,
        'DO' => "Dominican Republic" ,
        'EC' => "Ecuador" ,
        'EG' => "Egypt" ,
        'SV' => "El Salvador" ,
        'GQ' => "Equatorial Guinea" ,
        'ER' => "Eritrea" ,
        'EE' => "Estonia" ,
        'ET' => "Ethiopia" ,
        'FK' => "Falkland Islands (Malvinas)" ,
        'FO' => "Faroe Islands" ,
        'FJ' => "Fiji" ,
        'FI' => "Finland" ,
        'FR' => "France" ,
        'GF' => "French Guiana" ,
        'PF' => "French Polynesia" ,
        'TF' => "French Southern Territories" ,
        'GA' => "Gabon" ,
        'GM' => "Gambia" ,
        'GE' => "Georgia" ,
        'DE' => "Germany" ,
        'GH' => "Ghana" ,
        'GI' => "Gibraltar" ,
        'GR' => "Greece" ,
        'GL' => "Greenland" ,
        'GD' => "Grenada" ,
        'GP' => "Guadeloupe" ,
        'GU' => "Guam" ,
        'GT' => "Guatemala" ,
        'GN' => "Guinea" ,
        'GW' => "Guinea-Bissau" ,
        'GY' => "Guyana" ,
        'HT' => "Haiti" ,
        'HM' => "Heard Island And Mcdonald Islands" ,
        'VA' => "Holy See (Vatican City State)" ,
        'HN' => "Honduras" ,
        'HK' => "Hong Kong" ,
        'HU' => "Hungary" ,
        'IS' => "Iceland" ,
        'IN' => "India" ,
        'ID' => "Indonesia" ,
        'IR' => "Iran, Islamic Republic Of" ,
        'IQ' => "Iraq" ,
        'IE' => "Ireland" ,
        'IL' => "Israel" ,
        'IT' => "Italy" ,
        'JM' => "Jamaica" ,
        'JP' => "Japan" ,
        'JO' => "Jordan" ,
        'KZ' => "Kazakhstan" ,
        'KE' => "Kenya" ,
        'KI' => "Kiribati" ,
        'KP' => "Korea, Democratic People's Republic Of" ,
        'KR' => "Korea, Republic Of" ,
        'KW' => "Kuwait" ,
        'KG' => "Kyrgyzstan" ,
        'LA' => "Lao People's Democratic Republic" ,
        'LV' => "Latvia" ,
        'LB' => "Lebanon" ,
        'LS' => "Lesotho" ,
        'LR' => "Liberia" ,
        'LY' => "Libyan Arab Jamahiriya" ,
        'LI' => "Liechtenstein" ,
        'LT' => "Lithuania" ,
        'LU' => "Luxembourg" ,
        'MO' => "Macao" ,
        'MK' => "Macedonia, The Former Yugoslav Republic Of" ,
        'MG' => "Madagascar" ,
        'MW' => "Malawi" ,
        'MY' => "Malaysia" ,
        'MV' => "Maldives" ,
        'ML' => "Mali" ,
        'MT' => "Malta" ,
        'MH' => "Marshall Islands" ,
        'MQ' => "Martinique" ,
        'MR' => "Mauritania" ,
        'MU' => "Mauritius" ,
        'YT' => "Mayotte" ,
        'MX' => "Mexico" ,
        'FM' => "Micronesia, Federated States Of" ,
        'MD' => "Moldova, Republic Of" ,
        'MC' => "Monaco" ,
        'MN' => "Mongolia" ,
        'MS' => "Montserrat" ,
        'MA' => "Morocco" ,
        'MZ' => "Mozambique" ,
        'MM' => "Myanmar" ,
        'NA' => "Namibia" ,
        'NR' => "Nauru" ,
        'NP' => "Nepal" ,
        'NL' => "Netherlands" ,
        'AN' => "Netherlands Antilles" ,
        'NC' => "New Caledonia" ,
        'NZ' => "New Zealand" ,
        'NI' => "Nicaragua" ,
        'NE' => "Niger" ,
        'NG' => "Nigeria" ,
        'NU' => "Niue" ,
        'NF' => "Norfolk Island" ,
        'MP' => "Northern Mariana Islands" ,
        'NO' => "Norway" ,
        'OM' => "Oman" ,
        'PK' => "Pakistan" ,
        'PW' => "Palau" ,
        'PS' => "Palestinian Territory, Occupied" ,
        'PA' => "Panama" ,
        'PG' => "Papua New Guinea" ,
        'PY' => "Paraguay" ,
        'PE' => "Peru" ,
        'PH' => "Philippines" ,
        'PN' => "Pitcairn" ,
        'PL' => "Poland" ,
        'PT' => "Portugal" ,
        'PR' => "Puerto Rico" ,
        'QA' => "Qatar" ,
        'RE' => "Reunion" ,
        'RO' => "Romania" ,
        'RU' => "Russian Federation" ,
        'RW' => "Rwanda" ,
        'SH' => "Saint Helena" ,
        'KN' => "Saint Kitts And Nevis" ,
        'LC' => "Saint Lucia" ,
        'PM' => "Saint Pierre And Miquelon" ,
        'VC' => "Saint Vincent And The Grenadines" ,
        'WS' => "Samoa" ,
        'SM' => "San Marino" ,
        'ST' => "Sao Tome And Principe" ,
        'SA' => "Saudi Arabia" ,
        'SN' => "Senegal" ,
        'CS' => "Serbia And Montenegro" ,
        'SC' => "Seychelles" ,
        'SL' => "Sierra Leone" ,
        'SG' => "Singapore" ,
        'SK' => "Slovakia" ,
        'SI' => "Slovenia" ,
        'SB' => "Solomon Islands" ,
        'SO' => "Somalia" ,
        'ZA' => "South Africa" ,
        'GS' => "South Georgia And The South Sandwich Islands" ,
        'ES' => "Spain" ,
        'LK' => "Sri Lanka" ,
        'SD' => "Sudan" ,
        'SR' => "Suriname" ,
        'SJ' => "Svalbard And Jan Mayen" ,
        'SZ' => "Swaziland" ,
        'SE' => "Sweden" ,
        'CH' => "Switzerland" ,
        'SY' => "Syrian Arab Republic" ,
        'TW' => "Taiwan, Province Of China" ,
        'TJ' => "Tajikistan" ,
        'TZ' => "Tanzania, United Republic Of" ,
        'TH' => "Thailand" ,
        'TL' => "Timor-Leste" ,
        'TG' => "Togo" ,
        'TK' => "Tokelau" ,
        'TO' => "Tonga" ,
        'TT' => "Trinidad And Tobago" ,
        'TN' => "Tunisia" ,
        'TR' => "Turkey" ,
        'TM' => "Turkmenistan" ,
        'TC' => "Turks And Caicos Islands" ,
        'TV' => "Tuvalu" ,
        'UG' => "Uganda" ,
        'UA' => "Ukraine" ,
        'AE' => "United Arab Emirates" ,
        'GB' => "United Kingdom" ,
        'US' => "United States" ,
        'UM' => "United States Minor Outlying Islands" ,
        'UY' => "Uruguay" ,
        'UZ' => "Uzbekistan" ,
        'VU' => "Vanuatu" ,
        'VE' => "Venezuela" ,
        'VN' => "Viet Nam" ,
        'VG' => "Virgin Islands, British" ,
        'VI' => "Virgin Islands, U.S." ,
        'WF' => "Wallis And Futuna" ,
        'EH' => "Western Sahara" ,
        'YE' => "Yemen" ,
        'ZM' => "Zambia" ,
        'ZW' => "Zimbabwe" ,
    );

    $language_codes = array(
        'en' => 'English' ,
        'aa' => 'Afar' ,
        'ab' => 'Abkhazian' ,
        'af' => 'Afrikaans' ,
        'am' => 'Amharic' ,
        'ar' => 'Arabic' ,
        'as' => 'Assamese' ,
        'ay' => 'Aymara' ,
        'az' => 'Azerbaijani' ,
        'ba' => 'Bashkir' ,
        'be' => 'Byelorussian' ,
        'bg' => 'Bulgarian' ,
        'bh' => 'Bihari' ,
        'bi' => 'Bislama' ,
        'bn' => 'Bengali/Bangla' ,
        'bo' => 'Tibetan' ,
        'br' => 'Breton' ,
        'ca' => 'Catalan' ,
        'co' => 'Corsican' ,
        'cs' => 'Czech' ,
        'cy' => 'Welsh' ,
        'da' => 'Danish' ,
        'de' => 'German' ,
        'dz' => 'Bhutani' ,
        'el' => 'Greek' ,
        'eo' => 'Esperanto' ,
        'es' => 'Spanish' ,
        'et' => 'Estonian' ,
        'eu' => 'Basque' ,
        'fa' => 'Persian' ,
        'fi' => 'Finnish' ,
        'fj' => 'Fiji' ,
        'fo' => 'Faeroese' ,
        'fr' => 'French' ,
        'fy' => 'Frisian' ,
        'ga' => 'Irish' ,
        'gd' => 'Scots/Gaelic' ,
        'gl' => 'Galician' ,
        'gn' => 'Guarani' ,
        'gu' => 'Gujarati' ,
        'ha' => 'Hausa' ,
        'hi' => 'Hindi' ,
        'hr' => 'Croatian' ,
        'hu' => 'Hungarian' ,
        'hy' => 'Armenian' ,
        'ia' => 'Interlingua' ,
        'ie' => 'Interlingue' ,
        'ik' => 'Inupiak' ,
        'in' => 'Indonesian' ,
        'is' => 'Icelandic' ,
        'it' => 'Italian' ,
        'iw' => 'Hebrew' ,
        'ja' => 'Japanese' ,
        'ji' => 'Yiddish' ,
        'jw' => 'Javanese' ,
        'ka' => 'Georgian' ,
        'kk' => 'Kazakh' ,
        'kl' => 'Greenlandic' ,
        'km' => 'Cambodian' ,
        'kn' => 'Kannada' ,
        'ko' => 'Korean' ,
        'ks' => 'Kashmiri' ,
        'ku' => 'Kurdish' ,
        'ky' => 'Kirghiz' ,
        'la' => 'Latin' ,
        'ln' => 'Lingala' ,
        'lo' => 'Laothian' ,
        'lt' => 'Lithuanian' ,
        'lv' => 'Latvian/Lettish' ,
        'mg' => 'Malagasy' ,
        'mi' => 'Maori' ,
        'mk' => 'Macedonian' ,
        'ml' => 'Malayalam' ,
        'mn' => 'Mongolian' ,
        'mo' => 'Moldavian' ,
        'mr' => 'Marathi' ,
        'ms' => 'Malay' ,
        'mt' => 'Maltese' ,
        'my' => 'Burmese' ,
        'na' => 'Nauru' ,
        'ne' => 'Nepali' ,
        'nl' => 'Dutch' ,
        'no' => 'Norwegian' ,
        'oc' => 'Occitan' ,
        'om' => '(Afan)/Oromoor/Oriya' ,
        'pa' => 'Punjabi' ,
        'pl' => 'Polish' ,
        'ps' => 'Pashto/Pushto' ,
        'pt' => 'Portuguese' ,
        'qu' => 'Quechua' ,
        'rm' => 'Rhaeto-Romance' ,
        'rn' => 'Kirundi' ,
        'ro' => 'Romanian' ,
        'ru' => 'Russian' ,
        'rw' => 'Kinyarwanda' ,
        'sa' => 'Sanskrit' ,
        'sd' => 'Sindhi' ,
        'sg' => 'Sangro' ,
        'sh' => 'Serbo-Croatian' ,
        'si' => 'Singhalese' ,
        'sk' => 'Slovak' ,
        'sl' => 'Slovenian' ,
        'sm' => 'Samoan' ,
        'sn' => 'Shona' ,
        'so' => 'Somali' ,
        'sq' => 'Albanian' ,
        'sr' => 'Serbian' ,
        'ss' => 'Siswati' ,
        'st' => 'Sesotho' ,
        'su' => 'Sundanese' ,
        'sv' => 'Swedish' ,
        'sw' => 'Swahili' ,
        'ta' => 'Tamil' ,
        'te' => 'Tegulu' ,
        'tg' => 'Tajik' ,
        'th' => 'Thai' ,
        'ti' => 'Tigrinya' ,
        'tk' => 'Turkmen' ,
        'tl' => 'Tagalog' ,
        'tn' => 'Setswana' ,
        'to' => 'Tonga' ,
        'tr' => 'Turkish' ,
        'ts' => 'Tsonga' ,
        'tt' => 'Tatar' ,
        'tw' => 'Twi' ,
        'uk' => 'Ukrainian' ,
        'ur' => 'Urdu' ,
        'uz' => 'Uzbek' ,
        'vi' => 'Vietnamese' ,
        'vo' => 'Volapuk' ,
        'wo' => 'Wolof' ,
        'xh' => 'Xhosa' ,
        'yo' => 'Yoruba' ,
        'zh' => 'Chinese' ,
        'zu' => 'Zulu' ,
    );

    //Adapted from: http://stackoverflow.com/questions/3938120/check-if-exec-is-disabled
    function is_shell_exec_available() {
        static $available;

        if (!isset($available)) {
            $available = true;
            if (ini_get('safe_mode')) {
                $available = false;
            } else {
                $d = ini_get('disable_functions');
                $s = ini_get('suhosin.executor.func.blacklist');
                if ("$d$s") {
                    $array = preg_split('/,\s*/', "$d,$s");
                    if (in_array('shell_exec', $array)) {
                        $available = false;
                    }
                }
            }
        }

        return $available;
    }

    $locale_options = array();

    if(is_shell_exec_available()) {

        //hopefully suppress shell_exec warning
        @$locales = shell_exec('locale -a');

        if(strlen($locales) > 0) {
            $locales = explode("\n" , $locales);

            foreach($locales as $loc) {
                if(strlen($loc)) {
                    $parts = explode('.' , $loc);
                    $lc = $parts[0];

                    list($lcode , $ccode) = explode('_' , $lc);

                    $lcode = strtolower($lcode);

                    $language = $language_codes[$lcode];
                    $country = $country_codes[$ccode];

                    if( array_key_exists($lcode, $language_codes) &&
                        array_key_exists($ccode, $country_codes)){

                        $locale_options[$loc] = strlen($parts[1]) > 0 ? $language.'/'.$country.' ('.$parts[1].')' : $language.'/'.$country;
                    }
                }
            }

            asort($locale_options);
        }
    }
?>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $("#show_advanced_settings").click(function(){
            $("#advanced_settings").show(400);
            $(this).hide(400);
        });
    });
</script>

<div class="woo_product_importer_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <h2><?php _e( 'Woo Product Importer &raquo; Upload', 'woo-product-importer' ); ?></h2>

    <form enctype="multipart/form-data" method="post" action="<?php echo get_admin_url().'tools.php?page=woo-product-importer&action=preview'; ?>">
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="import_csv"><?php _e( 'File to Import', 'woo-product-importer' ); ?></label></th>
                    <td><input type="file" name="import_csv" id="import_csv"></td>
                </tr>
                <tr>
                    <th><label for="import_csv_url"><?php _e( 'URL to Import', 'woo-product-importer' ); ?></label></th>
                    <td>
                        <input type="text" name="import_csv_url" id="import_csv_url" class="regular-text code">
                        <p class="description"><?php _e( 'Enter the full URL to a CSV file. Leave this field blank if uploading a file.', 'woo-product-importer' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="checkbox" name="header_row" id="header_row" value="1">
                        <label for="header_row"><?php _e( 'First Row is Header Row', 'woo-product-importer' ); ?></label>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <button class="button-primary" type="submit"><?php _e( 'Upload and Preview', 'woo-product-importer' ); ?></button>
                        <button class="button" type="button" id="show_advanced_settings"><?php _e( 'Advanced Settings &darr;', 'woo-product-importer' ); ?></button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div id="advanced_settings">
            <h3><?php _e( 'Advanced Settings', 'woo-product-importer' ); ?></h3>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php _e( 'Path to Your <strong>uploads</strong> Folder', 'woo-product-importer' ); ?></th>
                        <td><?php
                            $upload_dir = wp_upload_dir();
                            echo $upload_dir['basedir'];
                        ?></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'CSV field separator', 'woo-product-importer' ); ?></th>
                        <td>
                            <input type="text" name="import_csv_separator" id="import_csv_separator" class="code" value="," maxlength="1">
                            <p class="description"><?php _e( 'Enter the character used to separate each field in your CSV. The default is the comma (,) character. Some formats use a semicolon (;) instead.', 'woo-product-importer' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Category hierarchy separator', 'woo-product-importer' ); ?></th>
                        <td>
                            <input type="text" name="import_csv_hierarchy_separator" id="import_csv_hierarchy_separator" class="code" value="/" maxlength="1">
                            <p class="description"><?php _e( 'Enter the character used to separate categories in a hierarchical structure. The default is the forward-slash (/) character.', 'woo-product-importer' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="user_locale"><?php _e( 'Set Locale', 'woo-product-importer' ); ?></label></th>
                        <td>
                            <?php if(count($locale_options) == 0) : ?>
                                <ul class="import_error_messages">
                                    <li><?php _e( 'Couldn\'t get a list of available locales from your server.', 'woo-product-importer' ); ?></li>
                                </ul>
                                <input type="text" name="user_locale" id="user_locale" class="code">
                                <p class="description"><a href="http://webpresencepartners.com/2013/04/28/a-list-of-common-locale-codes/" target="_blank"><?php _e( 'A list of common locale codes is available here.', 'woo-product-importer' ); ?></a></p>
                            <?php else: ?>
                                <select name="user_locale" id="user_locale">
                                    <option value="0"><?php _e( 'System Default', 'woo-product-importer' ); ?></option>
                                    <?php foreach($locale_options as $locale_string => $label) : ?>
                                        <option value="<?php echo htmlspecialchars($locale_string); ?>"><?php echo htmlspecialchars($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif;?>
                            <p class="description"><?php _e( 'If you have special characters in your CSV, (such as &aelig;, &szlig;, &eacute;, etc.) set this to a locale compatible with those characters.', 'woo-product-importer' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>