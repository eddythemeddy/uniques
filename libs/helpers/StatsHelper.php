<?php

class StatsHelper
{
    // private static $stats = [
    //     'soccer' => [
    //         'Set Pieces' => [
    //             'Corners', 'Crossing', 'Free Kicks', 'Long Throws', 'Penalty Taking'
    //         ], 
    //         'Ball Control' => [
    //             'Dribbling', 'First Touch', 'Heading', 'Passing', 'Technique'  
    //         ], 
    //         'Attacking' => [
    //             'Finishing', 'Long Shots',
    //         ], 
    //         'Defending' => [
    //             'Marking', 'Tackling'
    //         ],
    //         'Mental' => [
    //             'Creativity', 'Flair', 'Off The Ball', 'Ambition',
    //             'Aggression', 'Decisions'
    //         ],
    //         'Technical' => [
    //             'Composure','Work Rate', 'Decisions', 'Determination',
    //             'Influence', 'Anticipation', 'Bravery', 'Positioning', 'Teamwork'
    //         ],
    //         'Physical' => [
    //             'Pace', 'Stamina', 'Agility', 'Jumping', 'Acceleration',
    //             'Natural Fitness', 'Balance', 'Strength'
    //         ],
    //         'Goal-Keeping' => [
    //             'Aerial Ability', 'Command Of Area', 'Communication', 
    //             'Eccentricity', 'Handling', 'Kicking', 'One On Ones', 
    //             'Reflexes', 'Rushing Out', 'Tendency To Punch', 'Throwing'
    //         ]
    //     ]
    // ];

    // private $defaultStats = [
    //     'soccer' => [[
    //       'Set_Pieces' => 
    //       [[
    //         'Corners' => '2',
    //         'Crossing' => '2',
    //         'Free_Kicks' => '2',
    //         'Long_Throws' => '2',
    //         'Penalty_Taking' => '2',
    //       ],
    //       'Ball_Control' => 
    //       [[
    //         'Dribbling' => '2',
    //         'First_Touch' => '2',
    //         'Heading' => '2',
    //         'Passing' => '2',
    //         'Technique' => '2',
    //       ],
    //       'Attacking' => 
    //       [[
    //         'Finishing' => '2',
    //         'Long_Shots' => '2',
    //       ],
    //       'Defending' => 
    //       [[
    //         'Marking' => '2',
    //         'Tackling' => '2',
    //       ],
    //       'Mental' => 
    //       [[
    //         'Creativity' => '2',
    //         'Flair' => '2',
    //         'Off_The_Ball' => '2',
    //         'Ambition' => '2',
    //         'Aggression' => '2',
    //         'Decisions' => '2',
    //       ],
    //       'Technical' => 
    //       [[
    //         'Composure' => '2',
    //         'Work_Rate' => '2',
    //         'Decisions' => '2',
    //         'Determination' => '2',
    //         'Influence' => '2',
    //         'Anticipation' => '2',
    //         'Bravery' => '2',
    //         'Positioning' => '2',
    //         'Teamwork' => '2',
    //       ],
    //       'Physical' => 
    //       [[
    //         'Pace' => '2',
    //         'Stamina' => '2',
    //         'Agility' => '2',
    //         'Jumping' => '2',
    //         'Acceleration' => '2',
    //         'Natural_Fitness' => '2',
    //         'Balance' => '2',
    //         'Strength' => '2',
    //       ],
    //       'Goal-Keeping' => 
    //       [[
    //         'Aerial_Ability' => '2',
    //         'Command_Of_Area' => '2',
    //         'Communication' => '2',
    //         'Eccentricity' => '2',
    //         'Handling' => '2',
    //         'Kicking' => '2',
    //         'One_On_Ones' => '2',
    //         'Reflexes' => '2',
    //         'Rushing_Out' => '2',
    //         'Tendency_To_Punch' => '2',
    //         'Throwing' => '2',
    //       ],
    //      ]
        
    // ];

    private static $statistic = [
        'date' => '',
        'season' => ''
    ];

    public static function getStats($type) {
        if(!empty(self::$stats[$type])) {
            return self::$stats[$type];
        }
    }

    public static function getStatsistics($type) {
        if(!empty(self::$stats[$type])) {
            return self::$stats[$type];
        }
    }

    public function convertStatsForChart($stats) {
        if(!is_array($stats) || empty($stats)) {
            return "var chartCategories=[],\nchartValues=[];\n";
        }
        $statCategories = array_keys($stats);
        $str = "var chartCategories=" . json_encode($statCategories) . ";\n";

        $str .= "var chartValues=[";
        $d = 0;
        foreach($stats as $key => $stat) {
            $str .= ($d > 0 ? "," : "") . array_sum($stat)/count($stat);
            $d++;
        }
        $str .= "];\n";
        return $str;
    }

    public function loadStatistics($id = false, $json = true) {

        $exp = isset($_GET['loadStatistics']) ? $_GET['loadStatistics'] : $id;

        $team = $this->eqDb->subQuery ('t');
        $team->get("experience", null, 'id,team');
        $this->eqDb->join($team, 't.id = s.experience_id', 'LEFT');

        $this->eqDb->where ('experience_id', $exp);
        $this->eqDb->groupBy ('season');
        $res = $this->eqDb->get ('statistics s', null, 
            'COUNT(*) AS total, 
            YEAR(STR_TO_DATE(date, "%y")) as year, 
            MONTH(STR_TO_DATE(date, "%Y-%m-%d")) as month, date, 
            IF(MONTH(STR_TO_DATE(date, "%Y-%m-%d")) > 7, 
                CONCAT(YEAR(STR_TO_DATE(date, "%Y-%m-%d")),"-",YEAR(STR_TO_DATE(date, "%Y-%m-%d")) + 1), 
                CONCAT(YEAR(STR_TO_DATE(date, "%Y-%m-%d")) - 1,"-",YEAR(STR_TO_DATE(date, "%Y-%m-%d")))
            ) as season,
            t.team as team,
            experience_id,
            SUM(goals) as goals, 
            SUM(assists) as assists, 
            SUM(passes) as passes, 
            SUM(yellows) as yellows, 
            SUM(reds) as reds, 
            SUM(tackles) as tackles, 
            SUM(headers) as headers');
        
        // echo $this->eqDb->getLastQuery();
        if($json) {
            echo json_encode($res);
        } else {
            return $res;
        }
    }

    public static function getDefaulStats($type){
        if(!empty(self::$defaultStats[$type])) {
            return self::$defaultStats[$type];
        }
    }
}