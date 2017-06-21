<?php
namespace Flacox;

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cms_chart.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Functions".DIRECTORY_SEPARATOR."LeadTimeCalculator.php");

function drawSprintsLeadTime($userID, $userToken, $projectID)
{
    $title = "Lead Time";
    $days = false;
    $hours = false;
    $minutes = false;
    $time0=true;
    
    $leadTimeCalculator = new LeadTimeCalculator($userID, $userToken, $projectID, 0);
    
    $arraySprintsLeadTime = $leadTimeCalculator->calculateLeadTime();
    
    foreach ($arraySprintsLeadTime as $key => $sprintsLeadTime) {
        $title = $key . " " . $title;
        $orderedSprintsLeadTime = $sprintsLeadTime;
        asort($orderedSprintsLeadTime);
        
        do {
            $lowerTime = reset($orderedSprintsLeadTime);
            if ($lowerTime != 0) {
                $time0=false;

                if (round($lowerTime / 60 / 60 / 24) >= 1) {
                    $days = true;
                } elseif (round($lowerTime / 60 / 60) >= 1) {
                    $hours = true;
                } elseif (round($lowerTime / 60) >= 1) {
                    $minutes = true;
                }
            } else {
                array_shift($orderedSprintsLeadTime);
            }
        } while ($time0 && count($orderedSprintsLeadTime) >= 1);

        if ($days) {
            foreach ($sprintsLeadTime as $key => $value) {
                if ($value != 0) {
                    $sprintsLeadTime[$key] = ceil($value / 60 / 60 / 24);
                }
            }

            $title = $title . " (in Days)";
        } elseif ($hours) {
            foreach ($sprintsLeadTime as $key => $value) {
                if ($value != 0) {
                    $sprintsLeadTime[$key] = ceil($value / 60 / 60);
                }
            }

            $title = $title . " (in Hours)";
        } elseif ($minutes) {
            foreach ($sprintsLeadTime as $key => $value) {
                if ($value != 0) {
                    $sprintsLeadTime[$key] = ceil($value / 60);
                }
            }

            $title = $title . " (in Minutes)";
        } else {
            $title = $title . " (in Seconds)";
        }

        $formatedData = array(
            "Avg. Sprint Lead Time" => $sprintsLeadTime
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
