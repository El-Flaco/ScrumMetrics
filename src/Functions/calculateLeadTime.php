<?php
namespace Flacox;

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."TuleapUser.class.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getPlannings.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getMilestones.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getMilestoneContent.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getUserStoryStatusChangesets.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getBugStatusChangesets.php");

/**
 * @params user, Tuleap user
 * @params projectID, id of the chosen project
 */
function calculateLeadTime(TuleapUser $user, $projectId)
{
    $planningsInfo = getPlannings($user, $projectId);

    foreach ($planningsInfo as $planning) {
        $sprintsAvgLeadTime = array();

        if (strpos($planning['label'], 'Sprint Planning') !== false) {
            $milestones = getMilestones($user, $planning['id']);
            
            foreach ($milestones as $sprint) {
                $sprintAverageLeadTime = calculateAverageSprintLeadTime($user, $sprint['id']);
                if ($sprintAverageLeadTime !== null) {
                    $sprintsAvgLeadTime[$sprint['label']] = $sprintAverageLeadTime;
                }
            }
        }

        return $sprintsAvgLeadTime;
    }
}

/**
 * @params user, Tuleap user
 * @params sprintID, ID of the sprint from which 
 * the lead time will be calculated
 */
function calculateAverageSprintLeadTime(TuleapUser $user, $sprintID)
{
    $sprintElements = getMilestoneContent($user, $sprintID);
    $elementsData = array();
    $averageSprintLeadTime = null;
    $acumulatedSeconds = 0;
    $numberOfElementsDone = 0;
    
    foreach ($sprintElements as $sprintArtifact) {
        if ($sprintArtifact['status'] === 'Closed') {
            if ($sprintArtifact['type'] === 'Bug') {
                $time = calculateBugLeadTime($user, $sprintArtifact['id']);
                if ($time !== false) {
                    $elementsData[$sprintArtifact['id']]['time'] = $time;
                }
            } else {
                $time =calculateUserStoryLeadTime($user, $sprintArtifact['id']);
                if ($time !== false) {
                    $elementsData[$sprintArtifact['id']]['time'] = $time;
                }
            }
            
            if (isset($elementsData[$sprintArtifact['id']]['time'])) {
                $elementsData[$sprintArtifact['id']]['effort'] = $sprintArtifact['effort'];
            }
        }
        
        if (isset($elementsData[$sprintArtifact['id']]['time'])) {
            $numberOfElementsDone++;
            $acumulatedSeconds = $acumulatedSeconds + $elementsData[$sprintArtifact['id']]['time'];
        }
    }
    
    if ($numberOfElementsDone > 0) {
        $averageSprintLeadTime = $acumulatedSeconds / $numberOfElementsDone;
    }
    
    return round($averageSprintLeadTime);
}

/**
 * @params user, Tuleap user
 * @params userStoryID, id of the user story from which 
 * the lead time will be calculated
 */
function calculateUserStoryLeadTime(TuleapUser $user, $userStoryID)
{
    $currentStatus = "";
    $artifactChangesets = getUserStoryStatusChangesets($user, $userStoryID);
    $artifactStartTime;
    $artifactEndTime;
    
    foreach ($artifactChangesets as $artifactInfo) {

        if ($artifactInfo['status'] !== $currentStatus && $artifactInfo['status'] === "Todo") {
            $artifactStartTime = $artifactInfo['submission'];
            $currentStatus = $artifactInfo['status'];
        } elseif ($artifactInfo['status'] !== $currentStatus && $artifactInfo['status'] === "Done") {
            $artifactEndTime = $artifactInfo['submission'];
            $currentStatus = $artifactInfo['status'];

            return strtotime($artifactEndTime) - strtotime($artifactStartTime);
        }
    }
    
    return false;
}

/**
 * @params user, Tuleap user
 * @params bugID, id of the bug from which
 * the lead time will be calculated
 */
function calculateBugLeadTime(TuleapUser $user, $bugID)
{
    $currentStatus = "";
    $artifactChangesets = getBugStatusChangesets($user, $bugID);
    $artifactStartTime;
    $artifactEndTime;

    foreach ($artifactChangesets as $artifactInfo) {
        if ($artifactInfo['status'] !== $currentStatus && $artifactInfo['status'] === "New") {
            $artifactStartTime = $artifactInfo['submission'];
            $currentStatus = $artifactInfo['status'];
        } elseif ($artifactInfo['status'] !== $currentStatus && $artifactInfo['status'] === "Fixed") {
            $artifactEndTime = $artifactInfo['submission'];
            $currentStatus = $artifactInfo['status'];
            return strtotime($artifactEndTime) - strtotime($artifactStartTime);
        }
    }
    
    return false;
}
?>
