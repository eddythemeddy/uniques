<?php

class SurveyHelper
{
    private static $surveys = [
        'survey-1' => [
            'title' => 'Accountability',
            'description' => 'Measures the extent to which leaders/managers mobilise people through their own belief in the future of the business. Leaders act as facilitators of performance and inspire people to achieve the business vision.',
                'dimensions' => [
                [
                    'dimension' => 'Integration (vertical)',
                    'questions' => [
                        'Our leader assists us in understanding how our objectives are aligned in support to the overall business objectives.',
                        'Where I work we ensure that our individual and team outputs relate to the expected business results.',
                        'All our activities are integrated with the overall strategy of the business'
                    ]
                ],
                [
                    'dimension' => 'Integration (horizontal)',
                    'questions' => [
                        'At my place of work we regularly meet to ensure we do not duplicate our efforts.',
                    ]
                ],
                [
                    'dimension' => 'Continuous Improvement',
                    'questions' => [
                        'Our team\'s goals and targets accommodate changes in the business environment',
                        'Where I work our business plan addresses the changing needs of our clients.'
                    ]
                ],
                [
                    'dimension' => 'Credible Information',
                    'questions' => [
                        'The business information I receive is credible'
                    ]
                ],
                [
                    'dimension' => 'Inspirational Motivation',
                    'questions' => [
                        'My leader inspires people to achieve the business vision.',
                        'My leader ensures that people believe in the future of the business',
                        'My leader regularly discusses the future of our business with us.'
                    ]
                ],
            ]
        ],
        'survey-2' => [
            'title' => 'Survey 2',
            'questions' => [
                [
                    'dimension' => 'Management',
                    'questions' => [
                        'Mission/Vision clarity'
                    ]
                ],
                [
                    'dimension' => 'Communication',
                    'questions' => [

                    ]
                ],
                [
                    'dimension' => 'Teamwork',
                    'questions' => [

                    ]
                ],
                [
                    'dimension' => 'Training',
                    'questions' => [

                    ]
                ],
                [
                    'dimension' => 'Rewards',
                    'questions' => [

                    ]
                ],
            ]
        ]
    ];


    public function getAllSurveys() {
        if(!empty(self::$surveys)):
            return self::$surveys;
        else:
            return array();
        endif;
    }

    public function getSurveyByKey($key) {
        $surveys = $this->getAllSurveys();
        if(!empty($surveys[$key])) {
            return $surveys[$key];
        }

        return false;
    }

    public function buildSurveyDropdown() {
        $allSurveys = $this->getAllSurveys();
        $html = '';
        foreach($allSurveys as $key => $survey) {
            $html .= '<option value="' . $key . '">' . $survey['title'] . '</option>';
        }

        return $html;
    }

}