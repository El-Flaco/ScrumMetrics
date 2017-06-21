<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."CurlManager.class.php");

function getMilestones($userID, $userToken, $planningID)
{
    $curlManager = new CurlManager();
    $curlManager->setUrl("api/plannings/".$planningID."/milestones");
    $curlManager->setHeaders($userID, $userToken);

    $query = $curlManager->execute();

    if ($curlManager->checkHandleError()) {
        echo "Error: " . $curlManager->showError();
        echo "\n";
        return false;
    } else {
        $jsonResponse = json_decode($query);

        $milestones = array();
        $i = 0;
        foreach ($jsonResponse as $jsonObject) {
            $milestones[$i]["id"] = $jsonObject->id;
            $milestones[$i]["label"] = $jsonObject->label;
            $i++;
        }

        return $milestones;
    }
}
?>
