<?php
namespace Flacox;

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cms_chart.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Functions".DIRECTORY_SEPARATOR."LeadTimeCalculator.php");

function drawArtifactsLeadTime($userID, $userToken, $projectID)
{
    $title = "Lead Time";
    $days = false;
    $hours = false;
    $minutes = false;
    $time0=true;
    
    $bugsLeadTimeCalculator = new LeadTimeCalculator($userID, $userToken, $projectID, 2);
    $userStoriesLeadTimeCalculator = new LeadTimeCalculator($userID, $userToken, $projectID, 1);
    
    $arrayBugsLeadTime = $bugsLeadTimeCalculator->calculateLeadTime();
    $arrayUserStoriesLeadTime = $userStoriesLeadTimeCalculator->calculateLeadTime();
    $arrayArtifactsLeadTime;
    $bugsLeadTime;
    $userStoriesLeadTime;
    
    foreach ($arrayBugsLeadTime as $key => $value) {
        
        $arrayArtifactsLeadTime[$key]['Bugs'] = $value;
    }
    
    foreach ($arrayUserStoriesLeadTime as $key => $value) {
        
        $arrayArtifactsLeadTime[$key]['User Stories'] = $value;
    }
    
    foreach ($arrayArtifactsLeadTime as $key => $sprintsLeadTime) {
        $title = $key . " " . $title;
        $orderedBugsLeadTime = $sprintsLeadTime['Bugs'];
        $orderedUserStoriesLeadTime = $sprintsLeadTime['User Stories'];
        asort($orderedBugsLeadTime);
        asort($orderedUserStoriesLeadTime);
        
        $lowestTime = 0;
        $timeSet = false;
        
        $bugsKeys = array_keys($orderedBugsLeadTime);
        $userStoriesKeys = array_keys($orderedUserStoriesLeadTime);
        
        for ($i=0; $i<count($bugsKeys) && $timeSet === false; $i++) {
            if ($orderedBugsLeadTime[$bugsKeys[$i]] != 0) {
                $lowestTime = $orderedBugsLeadTime[$bugsKeys[$i]];
                for ($j=0; $j<count($userStoriesKeys); $j++) {
                    if ($orderedUserStoriesLeadTime[$userStoriesKeys[$j]] != 0) {
                        if ($orderedBugsLeadTime[$bugsKeys[$i]] > $orderedUserStoriesLeadTime[$userStoriesKeys[$j]]) {
                            $lowestTime = $orderedUserStoriesLeadTime[$userStoriesKeys[$j]];
                            break;
                        }
                    }
                }
                $timeSet = true;
            }
        }
        
        if ($lowestTime != 0) {
            if (round($lowestTime / 60 / 60 / 24) >= 1) {
                $days = true;
            } elseif (round($lowestTime / 60 / 60) >= 1) {
                $hours = true;
            } elseif (round($lowestTime / 60) >= 1) {
                $minutes = true;
            }
        }

        if ($days) {
            foreach ($sprintsLeadTime['Bugs'] as $key => $value) {
                if ($value != 0) {
                    $bugsLeadTime[$key] = ceil($value / 60 / 60 / 24);
                } else {
                    $bugsLeadTime[$key] = $value;
                }
            }
            
            foreach ($sprintsLeadTime['User Stories'] as $key => $value) {
                if ($value != 0) {
                    $userStoriesLeadTime[$key] = ceil($value / 60 / 60 / 24);
                } else {
                    $userStoriesLeadTime[$key] = $value;
                }
            }

            $title = $title . " (in Days)";
        } elseif ($hours) {
            foreach ($sprintsLeadTime['Bugs'] as $key => $value) {
                if ($value != 0) {
                    $bugsLeadTime[$key] = ceil($value / 60 / 60);
                } else {
                    $bugsLeadTime[$key] = $value;
                }
            }
            
            foreach ($sprintsLeadTime['User Stories'] as $key => $value) {
                if ($value != 0) {
                    $userStoriesLeadTime[$key] = ceil($value / 60 / 60);
                } else {
                    $userStoriesLeadTime[$key] = $value;
                }
            }

            $title = $title . " (in Hours)";
        } elseif ($minutes) {
        
            foreach ($sprintsLeadTime['Bugs'] as $key => $value) {
                if ($value != 0) {
                    $bugsLeadTime[$key] = ceil($value / 60);
                } else {
                    $bugsLeadTime[$key] = $value;
                }
            }
            
            foreach ($sprintsLeadTime['User Stories'] as $key => $value) {
                if ($value != 0) {
                    $userStoriesLeadTime[$key] = ceil($value / 60);
                } else {
                    $userStoriesLeadTime[$key] = $value;
                }
            }

            $title = $title . " (in Minutes)";
        } else {
            $title = $title . " (in Seconds)";
        }

        $formatedData = array(
            "Avg. Bugs Lead Time" => $bugsLeadTime,
            "Avg. User Stories Lead Time" => $userStoriesLeadTime
        );

        $init_chart = setChartProperties($title);
        cms_chart($formatedData, $init_chart);
    }
}


function setChartProperties($title)
{
    $init_chart = array();
    $init_chart['chart'] = 'line';
    $init_chart['title'] = $title;
    $init_chart['valShow']= 1;
    $init_chart['gapR'] = 5;
    $init_chart['gapL'] = -10;
    $init_chart['css'] = 1;
    $init_chart['colorDel'] = '0';
    
    if (strpos($title, 'Days') !== false) {
        $init_chart['yUnit'] = 'days';
    } elseif (strpos($title, 'Hours') !== false) {
        $init_chart['yUnit'] = 'hs';
    } elseif (strpos($title, 'Minutes') !== false) {
        $init_chart['yUnit'] = 'min';
    } else {
        $init_chart['yUnit'] = 'sec';
    }
    
    return $init_chart;
}
?>
