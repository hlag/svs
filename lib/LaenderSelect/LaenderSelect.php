<?php

class LaenderSelect
{
    protected static $instance = null;

    /**
     *
     * @returns LaenderSelect
     * @return LaenderSelect
     */
    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new LaenderSelect();
        }
        return self::$instance;
    }

    public function getList($parameterName, $id, $selected, $class = '', $firstGroupArray = array('DE', 'AT', 'CH'))
    {

        $this->landArray = $this->init();
        $retval = '<select name = "' . $parameterName . '" class ="' . $class . ' form-control" id="' . $id . '" >';
        $retval .= '<option value="NN"  >Bitte wählen</option><optgroup></optgroup>';
        if ($firstGroupArray)
        {
            foreach ($firstGroupArray AS $land)
                $retval .= '<option value="' . $land . '"  ' . ($selected == $land ? 'selected="selected"' : '') . '>' . $this->landArray[$land] . '</option>';
            unset($this->landArray[$land]);
            $retval .= '<optgroup></optgroup>';
        }

        foreach ($this->landArray AS $key => $value)
        {
            $retval .= '<option value="' . $key . '"  ' . ($selected == $key ? 'selected="selected"' : '') . '>' . $value . '</option>';
        }
        $retval .= '</select>';
        return $retval;
    }
    public function getOptions($selected, $firstGroupArray = array('DE'))
    {
        $this->landArray = $this->init();
        $retval = '<option value="NN"  >Bitte wählen</option><optgroup></optgroup>';

        if ($firstGroupArray)
        {
            foreach ($firstGroupArray AS $land)
                $retval .= '<option value="' . $land . '"  ' . ($selected == $land ? 'selected="selected"' : '') . '>' . $this->landArray[$land] . '</option>';
            unset($this->landArray[$land]);
            $retval .= '<optgroup></optgroup>';
        }

        foreach ($this->landArray AS $key => $value)
        {
            $retval .= '<option value="' . $key . '"  ' . ($selected == $key ? 'selected="selected"' : '') . '>' . $value . '</option>';
        }
        return $retval;
    }

    public function getLand($kuerzel)
    {
        $this->landArray = $this->init();
        if (isset($this->landArray[$kuerzel]))
            return $this->landArray[$kuerzel];
        else
            return 'unbekanntes Land';
    }

    private function init()
    {
        $landarray['AF'] = 'Afghanistan';
        $landarray['EG'] = 'Ägypten';
        $landarray['AL'] = 'Albanien';
        $landarray['DZ'] = 'Algerien';
        $landarray['AD'] = 'Andorra';
        $landarray['AO'] = 'Angola';
        $landarray['AI'] = 'Anguilla';
        $landarray['AQ'] = 'Antarktis';
        $landarray['AG'] = 'Antigua und Barbuda';
        $landarray['GQ'] = 'Äquatorial Guinea';
        $landarray['AR'] = 'Argentinien';
        $landarray['AM'] = 'Armenien';
        $landarray['AW'] = 'Aruba';
        $landarray['AZ'] = 'Aserbaidschan';
        $landarray['ET'] = 'Äthiopien';
        $landarray['AU'] = 'Australien';
        $landarray['BS'] = 'Bahamas';
        $landarray['BH'] = 'Bahrain';
        $landarray['BD'] = 'Bangladesh';
        $landarray['BB'] = 'Barbados';
        $landarray['BE'] = 'Belgien';
        $landarray['BZ'] = 'Belize';
        $landarray['BJ'] = 'Benin';
        $landarray['BM'] = 'Bermudas';
        $landarray['BT'] = 'Bhutan';
        $landarray['MM'] = 'Birma';
        $landarray['BO'] = 'Bolivien';
        $landarray['BA'] = 'Bosnien-Herzegowina';
        $landarray['BW'] = 'Botswana';
        $landarray['BV'] = 'Bouvet Inseln';
        $landarray['BR'] = 'Brasilien';
        $landarray['IO'] = 'Britisch-Indischer Ozean';
        $landarray['BN'] = 'Brunei';
        $landarray['BG'] = 'Bulgarien';
        $landarray['BF'] = 'Burkina Faso';
        $landarray['BI'] = 'Burundi';
        $landarray['CL'] = 'Chile';
        $landarray['CN'] = 'China';
        $landarray['CX'] = 'Christmas Island';
        $landarray['CK'] = 'Cook Inseln';
        $landarray['CR'] = 'Costa Rica';
        $landarray['DK'] = 'Dänemark';
        $landarray['DE'] = 'Deutschland';
        $landarray['DJ'] = 'Djibuti';
        $landarray['DM'] = 'Dominika';
        $landarray['DO'] = 'Dominikanische Republik';
        $landarray['EC'] = 'Ecuador';
        $landarray['SV'] = 'El Salvador';
        $landarray['CI'] = 'Elfenbeinküste';
        $landarray['ER'] = 'Eritrea';
        $landarray['EE'] = 'Estland';
        $landarray['FK'] = 'Falkland Inseln';
        $landarray['FO'] = 'Färöer Inseln';
        $landarray['FJ'] = 'Fidschi';
        $landarray['FI'] = 'Finnland';
        $landarray['FR'] = 'Frankreich';
        $landarray['GF'] = 'französisch Guyana';
        $landarray['PF'] = 'Französisch Polynesien';
        $landarray['TF'] = 'Französisches Süd-Territorium';
        $landarray['GA'] = 'Gabun';
        $landarray['GM'] = 'Gambia';
        $landarray['GE'] = 'Georgien';
        $landarray['GH'] = 'Ghana';
        $landarray['GI'] = 'Gibraltar';
        $landarray['GD'] = 'Grenada';
        $landarray['GR'] = 'Griechenland';
        $landarray['GL'] = 'Grönland';
        $landarray['UK'] = 'Großbritannien';
        $landarray['GP'] = 'Guadeloupe';
        $landarray['GU'] = 'Guam';
        $landarray['GT'] = 'Guatemala';
        $landarray['GN'] = 'Guinea';
        $landarray['GW'] = 'Guinea Bissau';
        $landarray['GY'] = 'Guyana';
        $landarray['HT'] = 'Haiti';
        $landarray['HM'] = 'Heard und McDonald Islands';
        $landarray['HN'] = 'Honduras';
        $landarray['HK'] = 'Hong Kong';
        $landarray['IN'] = 'Indien';
        $landarray['ID'] = 'Indonesien';
        $landarray['IQ'] = 'Irak';
        $landarray['IR'] = 'Iran';
        $landarray['IE'] = 'Irland';
        $landarray['IS'] = 'Island';
        $landarray['IL'] = 'Israel';
        $landarray['IT'] = 'Italien';
        $landarray['JM'] = 'Jamaika';
        $landarray['JP'] = 'Japan';
        $landarray['YE'] = 'Jemen';
        $landarray['JO'] = 'Jordanien';
        $landarray['YU'] = 'Jugoslawien';
        $landarray['KY'] = 'Kaiman Inseln';
        $landarray['KH'] = 'Kambodscha';
        $landarray['CM'] = 'Kamerun';
        $landarray['CA'] = 'Kanada';
        $landarray['CV'] = 'Kap Verde';
        $landarray['KZ'] = 'Kasachstan';
        $landarray['KE'] = 'Kenia';
        $landarray['KG'] = 'Kirgisistan';
        $landarray['KI'] = 'Kiribati';
        $landarray['CC'] = 'Kokosinseln';
        $landarray['CO'] = 'Kolumbien';
        $landarray['KM'] = 'Komoren';
        $landarray['CG'] = 'Kongo';
        $landarray['CD'] = 'Kongo, Demokratische Republik';
        $landarray['HR'] = 'Kroatien';
        $landarray['CU'] = 'Kuba';
        $landarray['KW'] = 'Kuwait';
        $landarray['LA'] = 'Laos';
        $landarray['LS'] = 'Lesotho';
        $landarray['LV'] = 'Lettland';
        $landarray['LB'] = 'Libanon';
        $landarray['LR'] = 'Liberia';
        $landarray['LY'] = 'Libyen';
        $landarray['LI'] = 'Liechtenstein';
        $landarray['LT'] = 'Litauen';
        $landarray['LU'] = 'Luxemburg';
        $landarray['MO'] = 'Macao';
        $landarray['MG'] = 'Madagaskar';
        $landarray['MW'] = 'Malawi';
        $landarray['MY'] = 'Malaysia';
        $landarray['MV'] = 'Malediven';
        $landarray['ML'] = 'Mali';
        $landarray['MT'] = 'Malta';
        $landarray['MP'] = 'Marianen';
        $landarray['MA'] = 'Marokko';
        $landarray['MH'] = 'Marshall Inseln';
        $landarray['MQ'] = 'Martinique';
        $landarray['MR'] = 'Mauretanien';
        $landarray['MU'] = 'Mauritius';
        $landarray['YT'] = 'Mayotte';
        $landarray['MK'] = 'Mazedonien';
        $landarray['MX'] = 'Mexiko';
        $landarray['FM'] = 'Mikronesien';
        $landarray['MZ'] = 'Mocambique';
        $landarray['MD'] = 'Moldavien';
        $landarray['MC'] = 'Monaco';
        $landarray['MN'] = 'Mongolei';
        $landarray['MS'] = 'Montserrat';
        $landarray['NA'] = 'Namibia';
        $landarray['NR'] = 'Nauru';
        $landarray['NP'] = 'Nepal';
        $landarray['NC'] = 'Neukaledonien';
        $landarray['NZ'] = 'Neuseeland';
        $landarray['NI'] = 'Nicaragua';
        $landarray['NL'] = 'Niederlande';
        $landarray['AN'] = 'Niederländische Antillen';
        $landarray['NE'] = 'Niger';
        $landarray['NG'] = 'Nigeria';
        $landarray['NU'] = 'Niue';
        $landarray['KP'] = 'Nord Korea';
        $landarray['NF'] = 'Norfolk Inseln';
        $landarray['NO'] = 'Norwegen';
        $landarray['OM'] = 'Oman';
        $landarray['AT'] = 'Österreich';
        $landarray['PK'] = 'Pakistan';
        $landarray['PS'] = 'Palästina';
        $landarray['PW'] = 'Palau';
        $landarray['PA'] = 'Panama';
        $landarray['PG'] = 'Papua Neuguinea';
        $landarray['PY'] = 'Paraguay';
        $landarray['PE'] = 'Peru';
        $landarray['PH'] = 'Philippinen';
        $landarray['PN'] = 'Pitcairn';
        $landarray['PL'] = 'Polen';
        $landarray['PT'] = 'Portugal';
        $landarray['PR'] = 'Puerto Rico';
        $landarray['QA'] = 'Qatar';
        $landarray['RE'] = 'Reunion';
        $landarray['RW'] = 'Ruanda';
        $landarray['RO'] = 'Rumänien';
        $landarray['RU'] = 'Rußland';
        $landarray['LC'] = 'Saint Lucia';
        $landarray['ZM'] = 'Sambia';
        $landarray['AS'] = 'Samoa';
        $landarray['WS'] = 'Samoa';
        $landarray['SM'] = 'San Marino';
        $landarray['ST'] = 'Sao Tome';
        $landarray['SA'] = 'Saudi Arabien';
        $landarray['SE'] = 'Schweden';
        $landarray['CH'] = 'Schweiz';
        $landarray['SN'] = 'Senegal';
        $landarray['SC'] = 'Seychellen';
        $landarray['SL'] = 'Sierra Leone';
        $landarray['SG'] = 'Singapur';
        $landarray['SK'] = 'Slowakei -slowakische Republik-';
        $landarray['SI'] = 'Slowenien';
        $landarray['SB'] = 'Solomon Inseln';
        $landarray['SO'] = 'Somalia';
        $landarray['GS'] = 'South Georgia, South Sandwich Isl.';
        $landarray['ES'] = 'Spanien';
        $landarray['LK'] = 'Sri Lanka';
        $landarray['SH'] = 'St. Helena';
        $landarray['KN'] = 'St. Kitts Nevis Anguilla';
        $landarray['PM'] = 'St. Pierre und Miquelon';
        $landarray['VC'] = 'St. Vincent';
        $landarray['KR'] = 'Süd Korea';
        $landarray['ZA'] = 'Südafrika';
        $landarray['SD'] = 'Sudan';
        $landarray['SR'] = 'Surinam';
        $landarray['SJ'] = 'Svalbard und Jan Mayen Islands';
        $landarray['SZ'] = 'Swasiland';
        $landarray['SY'] = 'Syrien';
        $landarray['TJ'] = 'Tadschikistan';
        $landarray['TW'] = 'Taiwan';
        $landarray['TZ'] = 'Tansania';
        $landarray['TH'] = 'Thailand';
        $landarray['TP'] = 'Timor';
        $landarray['TG'] = 'Togo';
        $landarray['TK'] = 'Tokelau';
        $landarray['TO'] = 'Tonga';
        $landarray['TT'] = 'Trinidad Tobago';
        $landarray['TD'] = 'Tschad';
        $landarray['CZ'] = 'Tschechische Republik';
        $landarray['TN'] = 'Tunesien';
        $landarray['TR'] = 'Türkei';
        $landarray['TM'] = 'Turkmenistan';
        $landarray['TC'] = 'Turks und Kaikos Inseln';
        $landarray['TV'] = 'Tuvalu';
        $landarray['UG'] = 'Uganda';
        $landarray['UA'] = 'Ukraine';
        $landarray['HU'] = 'Ungarn';
        $landarray['UY'] = 'Uruguay';
        $landarray['UZ'] = 'Usbekistan';
        $landarray['VU'] = 'Vanuatu';
        $landarray['VA'] = 'Vatikan';
        $landarray['VE'] = 'Venezuela';
        $landarray['AE'] = 'Vereinigte Arabische Emirate';
        $landarray['US'] = 'Vereinigte Staaten von Amerika';
        $landarray['VN'] = 'Vietnam';
        $landarray['VG'] = 'Virgin Island (Brit.)';
        $landarray['VI'] = 'Virgin Island (USA)';
        $landarray['WF'] = 'Wallis et Futuna';
        $landarray['BY'] = 'Weißrußland';
        $landarray['EH'] = 'Westsahara';
        $landarray['CF'] = 'Zentralafrikanische Republik';
        $landarray['ZW'] = 'Zimbabwe';
        $landarray['CY'] = 'Zypern';
        return $landarray;

    }

}

?>
