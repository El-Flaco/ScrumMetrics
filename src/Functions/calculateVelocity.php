<?php
namespace Flacox;

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getArtifacts.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getArtifactInformation.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getMilestones.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getMilestoneInformation.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getPlannings.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getProjects.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."JsonFileManager.class.php");

function calculateVelocity($userID, $userToken, $projectID)
{
    $projects = getProjects($userID, $userToken);
    $projectsID = array();
    $projectsLabel = array();
    
    foreach ($projects as $project) {
        $projectsID[] = $project{"id"};
        $projectsLabel[] = strtolower($project{"label"});
    }
    
    if (in_array($projectID, $projectsID)) {
        $planningsInfo = getPlannings($userID, $userToken, $projectID);
        
        $filesName = array();
        foreach ($planningsInfo as $planning) {
            $milestones = getMilestones($userID, $userToken, $planning['id']);
            $dataCollected = array();
            
            foreach ($milestones as $bigMilestone) {
                $aux = calculateMilestoneVelocity($userID, $userToken, $bigMilestone["id"]);
                $aux['label'] = $bigMilestone['label'];
                $dataCollected[] = $aux;
            }
            
            $jsonFileManager = new JsonFileManager();
            $fileName = date("Y-m-d_h:i:sa") . "-" . $planning['label']. "-id_" . $planning['id'] . "-Velocity.json";
            $jsonFileManager->saveToFile($fileName, $dataCollected);
            $filesName[] = $fileName;
        }
        
        return $filesName;
    }
}

function calculateMilestoneVelocity($userID, $userToken, $milestoneID)
{
    $milestoneInfo = array();
    
    $milestoneInfo[] = getMilestoneInformation($userID, $userToken, $milestoneID);
    
    $milestoneCapacity;
    $milestoneCommited;
    $milestoneDone;
    $calculatedArray = array();
    
    for ($i = 0; $i < count($milestoneInfo); $i++) {
        $milestoneCapacity = $milestoneInfo[$i]["capacity"];
             
        $artifacts = getArtifacts($userID, $userToken, $milestoneInfo[$i]["id"]);
        $milestoneCommited = calculateCommitedEffort($artifacts);
        $milestoneDone = calculateWorkDone($userID, $userToken, $artifacts);

        $calculatedArray["capacity"] = $milestoneCapacity;
        $calculatedArray["work_commited"] = $milestoneCommited;
        $calculatedArray["work_done"] = $milestoneDone;
    }

    return $calculatedArray;
}

function calculateCommitedEffort(array $artifacts)
{
    $totalInitialEffort = 0;
    foreach ($artifacts as $artifact) {
        $totalInitialEffort += $artifact["initial_effort"];
    }

    return $totalInitialEffort;
}

function calculateWorkDone($userID, $userToken, array $artifacts)
{
    $totalWorkDone = 0;
    foreach ($artifacts as $artifact) {
        $artifactInfo = getArtifactInformation($userID, $userToken, $artifact["id"]);

        if ($artifactInfo["status"] === "Done") {
            $totalWorkDone += $artifact["initial_effort"];
        }
    }
    
    return $totalWorkDone;

}
?>
