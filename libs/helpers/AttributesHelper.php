<?php

class AttributesHelper
{
    private static $positions = [
        'soccer' => [
            'Goalkeeper' => '1', 
            'Center Back' => '3',
            'Left Back' => '4', 
            'Right Back' => '5', 
            'Defensive Mid' => '6',
            'Center Mid' => '7',
            'Right Mid' => '8',
            'Left Mid' => '9',
            'Attacking Mid' => '10',
            'Left Wing' => '11',
            'Right Wing' => '12',
            'Second Striker' => '13',
            'Striker' => '14'
        ]
    ];

    public function getNumbersOfPositions($sport, $position) {

        if(empty($position)) {
            return '';
        }

        return self::$positions[$sport][$position];
    }


    public function getPositionsOptions($sport, $selected = false) {

        $positions = self::$positions[$sport];
        $html = '';
        foreach($positions as $key => $val){
            $thisSel = '';
            if($selected) {
                if(!is_array($selected)) {
                    if($selected == $key) { $thisSel = 'selected="selected"'; }
                } else {
                    if(in_array($key, $selected)) { $thisSel = 'selected="selected"'; }
                }
            }
            $html .= '<option value="' . $key . '" ' . $thisSel . '>' . $key . '</option>';
        }

        return $html;
    }
}