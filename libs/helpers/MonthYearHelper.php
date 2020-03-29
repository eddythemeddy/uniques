<?php

class MonthYearHelper
{
    private static $months = [];

    private static function loadMonths() {
        self::$months['jan'] = 'January';
        self::$months['feb'] = 'February';
        self::$months['mar'] = 'March';
        self::$months['apr'] = 'April';
        self::$months['may'] = 'May';
        self::$months['jun'] = 'June';
        self::$months['jul'] = 'July';
        self::$months['aug'] = 'August';
        self::$months['sep'] = 'September';
        self::$months['oct'] = 'October';
        self::$months['nov'] = 'November';
        self::$months['dec'] = 'December';
    }

    /**
     * Get country name from code
     * @param string $code
     */
    public static function getMonthName($code) {
        if(empty(self::$months)) {
            self::loadMonths();
        }

        return isset(self::$months[$code]) ? self::$months[$code] : $code;
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

    public function getMonthsOptions($selected = false) {

        if(empty(self::$months)) {
            self::loadMonths();
        }
        $months = self::$months;
        $html = '';
        foreach($months as $key => $val){
            $thisSel = '';
            if($selected) {
                if($selected == $key) { $thisSel = 'selected="selected"'; }
            }
            $html .= '<option value="' . $key . '" ' . $thisSel . '>' . $val . '</option>';
        }

        return $html;
    }
}