<?php
namespace Flacox;

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cms_chart.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Functions".DIRECTORY_SEPARATOR."CycleTimeCalculator.php");

function drawArtifactsCycleTime($userID, $userToken, $projectID)
{
    $title = "Cycle Time";
    $days = false;
    $hours = false;
    $minutes = false;
    $time0=true;
    
    $bugsCycleTimeCalculator = new CycleTimeCalculator($userID, $userToken, $projectID, 2);
    $userStoriesCycleTimeCalculator = new CycleTimeCalculator($userID, $userToken, $projectID, 1);
    
    $arrayBugsCycleTime = $bugsCycleTimeCalculator->calculateCycleTime();
    $arrayUserStoriesCycleTime = $userStoriesCycleTimeCalculator->calculateCycleTime();
    $arrayArtifactsCycleTime;
    $bugsCycleTime;
    $userStoriesCycleTime;
    
    foreach ($arrayBugsCycleTime as $key => $value) {
        
        $arrayArtifactsCycleTime[$key]['Bugs'] = $value;
    }
    
    foreach ($arrayUserStoriesCycleTime as $key => $value) {
        
        $arrayArtifactsCycleTime[$key]['User Stories'] = $value;
    }
    
    foreach ($arrayArtifactsCycleTime as $key => $sprintsCycleTime) {
        $title = $key . " " . $title;
        $orderedBugsCycleTime = $sprintsCycleTime['Bugs'];
        $orderedUserStoriesCycleTime = $sprintsCycleTime['User Stories'];
        asort($orderedBugsCycleTime);
        asort($orderedUserStoriesCycleTime);
        
        $lowestTime = 0;
        $timeSet = false;
        
        $bugsKeys = array_keys($orderedBugsCycleTime);
        $userStoriesKeys = array_keys($orderedUserStoriesCycleTime);
        
        for ($i=0; $i<count($bugsKeys) && $timeSet === false; $i++) {
            if ($orderedBugsCycleTime[$bugsKeys[$i]] != 0) {
                $lowestTime = $orderedBugsCycleTime[$bugsKeys[$i]];
                for ($j=0; $j<count($userStoriesKeys); $j++) {
                    if ($orderedUserStoriesCycleTime[$userStoriesKeys[$j]] != 0) {
                        if ($orderedBugsCycleTime[$bugsKeys[$i]] > $orderedUserStoriesCycleTime[$userStoriesKeys[$j]]) {
                            $lowestTime = $orderedUserStoriesCycleTime[$userStoriesKeys[$j]];
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
            foreach ($sprintsCycleTime['Bugs'] as $key => $value) {
                if ($value != 0) {
                    $bugsCycleTime[$key] = ceil($value / 60 / 60 / 24);
                } else {
                    $bugsCycleTime[$key] = $value;
                }
            }
            
            foreach ($sprintsCycleTime['User Stories'] as $key => $value) {
                if ($value != 0) {
                    $userStoriesCycleTime[$key] = ceil($value / 60 / 60 / 24);
                } else {
                    $userStoriesCycleTime[$key] = $value;
                }
            }

            $title = $title . " (in Days)";
        } elseif ($hours) {
            foreach ($sprintsCycleTime['Bugs'] as $key => $value) {
                if ($value != 0) {
                    $bugsCycleTime[$key] = ceil($value / 60 / 60);
                } else {
                    $bugsCycleTime[$key] = $value;
                }
            }
            
            foreach ($sprintsCycleTime['User Stories'] as $key => $value) {
                if ($value != 0) {
                    $userStoriesCycleTime[$key] = ceil($value / 60 / 60);
                } else {
                    $userStoriesCycleTime[$key] = $value;
                }
            }

            $title = $title . " (in Hours)";
        } elseif ($minutes) {
        
            foreach ($sprintsCycleTime['Bugs'] as $key => $value) {
                if ($value != 0) {
                    $bugsCycleTime[$key] = ceil($value / 60);
                } else {
                    $bugsCycleTime[$key] = $value;
                }
            }
            
            foreach ($sprintsCycleTime['User Stories'] as $key => $value) {
                if ($value != 0) {
                    $userStoriesCycleTime[$key] = ceil($value / 60);
                } else {
                    $userStoriesCycleTime[$key] = $value;
                }
            }

            $title = $title . " (in Minutes)";
        } else {
            foreach ($sprintsCycleTime['Bugs'] as $key => $value) {
                $bugsCycleTime[$key] = $value;
            }
            
            foreach ($sprintsCycleTime['User Stories'] as $key => $value) {
                $userStoriesCycleTime[$key] = $value;
            }
            
            $title = $title . " (in Seconds)";
        }

        $formatedData = array(
            "Avg. Bugs Cycle Time" => $bugsCycleTime,
            "Avg. User Stories Cycle Time" => $userStoriesCycleTime
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
