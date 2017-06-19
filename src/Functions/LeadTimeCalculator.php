<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."TuleapUser.class.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getPlannings.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getMilestones.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getMilestoneContent.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getUserStoryStatusChangesets.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."getBugStatusChangesets.php");

class LeadTimeCalculator
{
    private $SPRINT = 0;
    private $USER_STORY = 1;
    private $BUG = 2;
    
    private $user;
    private $projectID;
    private $leadTimeType=-1;
    
    public function __construct(TuleapUser $user, $projectID, $leadTimeType)
    {
        $this->user = $user;
        $this->projectID = $projectID;
        
        if ($leadTimeType == $this->SPRINT) {
            $this->leadTimeType = $this->SPRINT;
        } elseif ($leadTimeType == $this->USER_STORY) {
            $this->leadTimeType = $this->USER_STORY;
        } elseif ($leadTimeType == $this->BUG) {
            $this->leadTimeType = $this->BUG;
        }
    }
    
    public function calculateLeadTime()
    {
        $planningsInfo = getPlannings($this->user, $this->projectID);
        $AvgLeadTimes;

        foreach ($planningsInfo as $planning) {
            if (strpos($planning['label'], 'Sprint Planning') !== false) {
                $milestones = getMilestones($this->user, $planning['id']);

                if ($this->leadTimeType != -1) {
                    switch ($this->leadTimeType) {
                        case $this->SPRINT:
                            $AvgLeadTimes[$planning['label']."_".$planning['id']] = $this->calculateSprintsLeadTime($milestones);
                            break;
                        case $this->USER_STORY:
                            $AvgLeadTimes[$planning['label']."_".$planning['id']] = $this->calculateUserStoriesLeadTime($milestones);
                            break;
                        case $this->BUG:
                            $AvgLeadTimes[$planning['label']."_".$planning['id']] = $this->calculateBugsLeadTime($milestones);
                            break;
                        default:
                            return false;
                    }
                }
            }
        }
        
        return $AvgLeadTimes;
    }
    
    function calculateSprintsLeadTime(array $milestones)
    {
        $sprintAverageLeadTime = array();
        
        foreach ($milestones as $sprint) {
            $sprintAverageLeadTime = $this->calculateAverageSprintLeadTime($sprint['id']);
            if ($sprintAverageLeadTime !== null) {
                $sprintsAvgLeadTime[$sprint['label']] = $sprintAverageLeadTime;
            }
        }

        return $sprintsAvgLeadTime;
    }
    
    function calculateAverageSprintLeadTime($sprintID)
    {
        $sprintElements = getMilestoneContent($this->user, $sprintID);
        $elementsData = array();
        $averageSprintLeadTime = null;
        $acumulatedSeconds = 0;
        $numberOfElementsDone = 0;
        
        foreach ($sprintElements as $sprintArtifact) {
            if ($sprintArtifact['status'] === 'Closed') {
                if ($sprintArtifact['type'] === 'Bug') {
                    $time = $this->getBugLeadTime($sprintArtifact['id']);
                    if ($time !== false) {
                        $elementsData[$sprintArtifact['id']]['time'] = $time;
                    }
                } else {
                    $time = $this->getUserStoryLeadTime($sprintArtifact['id']);
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

    function calculateBugsLeadTime(array $milestones)
    {
        $bugAverageLeadTime = array();
        
        foreach ($milestones as $sprint) {
            $bugAverageLeadTime = $this->calculateAverageBugLeadTime($sprint['id']);
            if ($bugAverageLeadTime !== null) {
                $bugsAvgLeadTime[$sprint['label']] = $bugAverageLeadTime;
            }
        }
        
        return $bugsAvgLeadTime;
    }
    
    function calculateAverageBugLeadTime($sprintID)
    {
        $sprintElements = getMilestoneContent($this->user, $sprintID);
        $elementsData = array();
        $averageBugLeadTime = null;
        $acumulatedSeconds = 0;
        $numberOfElementsDone = 0;
        
        foreach ($sprintElements as $sprintArtifact) {
            if ($sprintArtifact['status'] === 'Closed') {
                if ($sprintArtifact['type'] === 'Bug') {
                    $time = $this->getBugLeadTime($sprintArtifact['id']);
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
            $averageBugLeadTime = $acumulatedSeconds / $numberOfElementsDone;
        }
        
        return round($averageBugLeadTime);
    }

    function calculateUserStoriesLeadTime(array $milestones)
    {
        $userStoryAverageLeadTime = array();
        
        foreach ($milestones as $sprint) {
            $userStoryAverageLeadTime = $this->calculateAverageUserStoryLeadTime($sprint['id']);
            if ($userStoryAverageLeadTime !== null) {
                $userStoriesAvgLeadTime[$sprint['label']] = $userStoryAverageLeadTime;
            }
        }
        
        return $userStoriesAvgLeadTime;
    }
    
    function calculateAverageUserStoryLeadTime($sprintID)
    {
        $sprintElements = getMilestoneContent($this->user, $sprintID);
        $elementsData = array();
        $averageBugLeadTime = null;
        $acumulatedSeconds = 0;
        $numberOfElementsDone = 0;
        
        
        foreach ($sprintElements as $sprintArtifact) {
            if ($sprintArtifact['status'] === 'Closed') {
                if ($sprintArtifact['type'] === 'User Stories') {
                    $time = $this->getUserStoryLeadTime($sprintArtifact['id']);
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
            $averageBugLeadTime = $acumulatedSeconds / $numberOfElementsDone;
        }
        
        return round($averageBugLeadTime);
    }

    function getUserStoryLeadTime($userStoryID)
    {
        $currentStatus = "";
        $artifactChangesets = getUserStoryStatusChangesets($this->user, $userStoryID);
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

    function getBugLeadTime($bugID)
    {
        $currentStatus = "";
        $artifactChangesets = getBugStatusChangesets($this->user, $bugID);
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
}
?>
