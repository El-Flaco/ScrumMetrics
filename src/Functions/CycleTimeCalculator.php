<?php
namespace Flacox;

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getPlannings.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getMilestones.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getMilestoneContent.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getUserStoryStatusChangesets.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getBugStatusChangesets.php");

class CycleTimeCalculator
{
    private $SPRINT = 0;
    private $USER_STORY = 1;
    private $BUG = 2;
    
    private $userID;
    private $userToken;
    private $projectID;
    private $cycleTimeType = -1;
    
    public function __construct($userID, $userToken, $projectID, $cycleTimeType)
    {
        $this->userID = $userID;
        $this->userToken = $userToken;
        $this->projectID = $projectID;
        
        if ($cycleTimeType == $this->SPRINT) {
            $this->cycleTimeType = $this->SPRINT;
        } elseif ($cycleTimeType == $this->USER_STORY) {
            $this->cycleTimeType = $this->USER_STORY;
        } elseif ($cycleTimeType == $this->BUG) {
            $this->cycleTimeType = $this->BUG;
        }
    }
    
    public function calculateCycleTime()
    {
        $planningsInfo = getPlannings($this->userID, $this->userToken, $this->projectID);
        $AvgCycleTimes;

        foreach ($planningsInfo as $planning) {
            if (strpos($planning['label'], 'Sprint Planning') !== false) {
                $milestones = getMilestones($this->userID, $this->userToken, $planning['id']);

                if ($this->cycleTimeType != -1) {
                    switch ($this->cycleTimeType) {
                        case $this->SPRINT:
                            $AvgCycleTimes[$planning['label']."_".$planning['id']] = $this->calculateSprintsCycleTime($milestones);
                            break;
                        case $this->USER_STORY:
                            $AvgCycleTimes[$planning['label']."_".$planning['id']] = $this->calculateUserStoriesCycleTime($milestones);
                            break;
                        case $this->BUG:
                            $AvgCycleTimes[$planning['label']."_".$planning['id']] = $this->calculateBugsCycleTime($milestones);
                            break;
                        default:
                            return false;
                    }
                }
            }
        }
        
        return $AvgCycleTimes;
    }
    
    function calculateSprintsCycleTime(array $milestones)
    {
        $sprintAverageCycleTime = array();
        
        foreach ($milestones as $sprint) {
            $sprintAverageCycleTime = $this->calculateAverageSprintCycleTime($sprint['id']);
            if ($sprintAverageCycleTime !== null) {
                $sprintsAvgCycleTime[$sprint['label']] = $sprintAverageCycleTime;
            }
        }

        return $sprintsAvgCycleTime;
    }
    
    function calculateAverageSprintCycleTime($sprintID)
    {
        $sprintElements = getMilestoneContent($this->userID, $this->userToken, $sprintID);
        $elementsData = array();
        $averageSprintCycleTime = null;
        $acumulatedSeconds = 0;
        $numberOfElementsDone = 0;
        
        foreach ($sprintElements as $sprintArtifact) {
            if ($sprintArtifact['status'] === 'Closed') {
                if ($sprintArtifact['type'] === 'Bug') {
                    $time = $this->getBugCycleTime($sprintArtifact['id']);
                    if ($time !== false) {
                        $elementsData[$sprintArtifact['id']]['time'] = $time;
                    }
                } else {
                    $time = $this->getUserStoryCycleTime($sprintArtifact['id']);
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
            $averageSprintCycleTime = $acumulatedSeconds / $numberOfElementsDone;
        }
        
        return round($averageSprintCycleTime);
    }

    function calculateBugsCycleTime(array $milestones)
    {
        $bugAverageCycleTime = array();
        
        foreach ($milestones as $sprint) {
            $bugAverageCycleTime = $this->calculateAverageBugCycleTime($sprint['id']);
            if ($bugAverageCycleTime !== null) {
                $bugsAvgCycleTime[$sprint['label']] = $bugAverageCycleTime;
            }
        }
        
        return $bugsAvgCycleTime;
    }
    
    function calculateAverageBugCycleTime($sprintID)
    {
        $sprintElements = getMilestoneContent($this->userID, $this->userToken, $sprintID);
        $elementsData = array();
        $averageBugCycleTime = null;
        $acumulatedSeconds = 0;
        $numberOfElementsDone = 0;
        
        foreach ($sprintElements as $sprintArtifact) {
            if ($sprintArtifact['status'] === 'Closed') {
                if ($sprintArtifact['type'] === 'Bug') {
                    $time = $this->getBugCycleTime($sprintArtifact['id']);
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
            $averageBugCycleTime = $acumulatedSeconds / $numberOfElementsDone;
        }
        
        return round($averageBugCycleTime);
    }

    function calculateUserStoriesCycleTime(array $milestones)
    {
        $userStoryAverageCycleTime = array();
        
        foreach ($milestones as $sprint) {
            $userStoryAverageCycleTime = $this->calculateAverageUserStoryCycleTime($sprint['id']);
            if ($userStoryAverageCycleTime !== null) {
                $userStoriesAvgCycleTime[$sprint['label']] = $userStoryAverageCycleTime;
            }
        }
        
        return $userStoriesAvgCycleTime;
    }
    
    function calculateAverageUserStoryCycleTime($sprintID)
    {
        $sprintElements = getMilestoneContent($this->userID, $this->userToken, $sprintID);
        $elementsData = array();
        $averageBugCycleTime = null;
        $acumulatedSeconds = 0;
        $numberOfElementsDone = 0;
        
        
        foreach ($sprintElements as $sprintArtifact) {
            if ($sprintArtifact['status'] === 'Closed') {
                if ($sprintArtifact['type'] === 'User Stories') {
                    $time = $this->getUserStoryCycleTime($sprintArtifact['id']);
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
            $averageBugCycleTime = $acumulatedSeconds / $numberOfElementsDone;
        }
        
        return round($averageBugCycleTime);
    }

    function getUserStoryCycleTime($userStoryID)
    {
        $currentStatus = "";
        $artifactChangesets = getUserStoryStatusChangesets($this->userID, $this->userToken, $userStoryID);
        $artifactStartTime=0;
        $artifactEndTime;
        
        foreach ($artifactChangesets as $artifactInfo) {
            if ($artifactInfo['status'] != $currentStatus && $artifactInfo['status'] == "Todo") {
                $artifactStartTime = null;
                $currentStatus = $artifactInfo['status'];
            }
            
            if ($artifactInfo['status'] != $currentStatus && $artifactInfo['status'] == "On Going") {
                $artifactStartTime = $artifactInfo['submission'];
                $currentStatus = $artifactInfo['status'];
            } elseif ($artifactInfo['status'] != $currentStatus && $artifactInfo['status'] == "Done") {
                $artifactEndTime = $artifactInfo['submission'];
                $currentStatus = $artifactInfo['status'];

                if(isset($artifactStartTime)){
                    return strtotime($artifactEndTime) - strtotime($artifactStartTime);
                } else {
                    return 1;
                }
            }
        }
        
        return false;
    }

    function getBugCycleTime($bugID)
    {
        $currentStatus = "";
        $artifactChangesets = getBugStatusChangesets($this->userID, $this->userToken, $bugID);
        $artifactStartTime;
        $artifactEndTime;

        foreach ($artifactChangesets as $artifactInfo) {
            if ($artifactInfo['status'] != $currentStatus && $artifactInfo['status'] == "New") {
                $artifactStartTime = null;
                $currentStatus = $artifactInfo['status'];
            }
            
            if ($artifactInfo['status'] != $currentStatus && $artifactInfo['status'] == "On going") {
                $artifactStartTime = $artifactInfo['submission'];
                $currentStatus = $artifactInfo['status'];
            } elseif ($artifactInfo['status'] != $currentStatus && $artifactInfo['status'] == "Fixed") {
                $artifactEndTime = $artifactInfo['submission'];
                $currentStatus = $artifactInfo['status'];
                
                if(isset($artifactStartTime)){
                    return strtotime($artifactEndTime) - strtotime($artifactStartTime);
                } else {
                    return 1;
                }
            }
        }
        
        return false;
    }
}
?>
