<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."CurlManager.class.php");

function getMilestoneInformation($userID, $userToken, $milestoneID)
{
    $curlManager = new CurlManager();
    $curlManager->setUrl("api/milestones/".$milestoneID);
    $curlManager->setHeaders($userID, $userToken);

    $query = $curlManager->execute();

    if ($curlManager->checkHandleError()) {
        echo "Error: " . $curlManager->showError();
        echo "\n";
        return false;
    } else {
        $jsonResponse = json_decode($query);

        $milestoneInformation = array();
        
        $milestoneInformation['id'] = $jsonResponse->id;
        $milestoneInformation['capacity'] = $jsonResponse->capacity;
        $milestoneInformation['status'] = $jsonResponse->status_value;

        return $milestoneInformation;
    }
}
?>
