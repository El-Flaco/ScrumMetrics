<?php
namespace Flacox;

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cms_chart.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Functions".DIRECTORY_SEPARATOR."getArtifacts.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Functions".DIRECTORY_SEPARATOR."getMilestones.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Functions".DIRECTORY_SEPARATOR."getPlannings.php");

function drawArtifactsByType($userID, $userToken, $projectID)
{
    $title = "Type of Elements Proportion";
    $formatedData = getElementsByType($userID, $userToken, $projectID);
    $init_chart = setChartProperties($title);
    cms_chart($formatedData, $init_chart);
}

function getElementsByType($userID, $userToken, $projectID)
{
    $plannings = getPlannings($userID, $userToken, $projectID);
    $formatedData = array();
    
    foreach ($plannings as $plan) {
        $milestonesArray = getMilestones($userID, $userToken, $plan['id']);
        $artifacts;
        foreach ($milestonesArray as $milestone) {
            $artifacts[$milestone['label']] = getArtifacts($userID, $userToken, $milestone['id']);

            foreach ($artifacts as $artifact) {
                $numberOfBugs = 0;
                $numberOfUserStories = 0;

                foreach($artifact as $artifactInfo) {
                    if ($artifactInfo['type'] === "Bug") {
                        $numberOfBugs = $numberOfBugs + $artifactInfo['initial_effort'];
                    } elseif ($artifactInfo['type'] === "User Stories") {
                        $numberOfUserStories = $numberOfUserStories + $artifactInfo['initial_effort'];
                    }
                }
                
                $formatedData['Bugs'][$milestone['label']] = $numberOfBugs;
                $formatedData['User Stories'][$milestone['label']] = $numberOfUserStories;
            }
        }
    }

    return $formatedData;
}

function setChartProperties($title)
{
    $init_chart = array();
    $init_chart['chart'] = 'barV';
    $init_chart['title'] = $title;
    $init_chart['valShow']= 1;
    $init_chart['yUnit'] = 'SP';
    $init_chart['gapR'] = 5;
    $init_chart['gapL'] = -20;
    $init_chart['css'] = 1;
    $init_chart['colorDel'] = '1,2,3';
    return $init_chart;
}
?>
