<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."CurlManager.class.php");

function getMilestoneContent($userID, $userToken, $milestoneID)
{
    $curlManager = new CurlManager();
    $curlManager->setUrl("api/milestones/".$milestoneID."/content");
    $curlManager->setHeaders($userID, $userToken);

    $query = $curlManager->execute();

    if ($curlManager->checkHandleError()) {
        echo "Error: " . $curlManager->showError();
        echo "\n";
        return false;
    } else {
        $jsonResponse = json_decode($query);

        $milestoneContent = array();
        $i = 0;

        foreach ($jsonResponse as $jsonObject) {
            $milestoneContent[$i]['id'] = $jsonObject->id;
            $milestoneContent[$i]['status'] = $jsonObject->status;
            $milestoneContent[$i]['type'] = $jsonObject->type;
            $milestoneContent[$i]['effort'] = $jsonObject->initial_effort;
            $i++;
        }
        
        return $milestoneContent;
    }
}
?>
