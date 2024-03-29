<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."TuleapUser.class.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."CurlManager.class.php");

function getUserStoryStatusChangesets($userID, $userToken, $artifactID)
{
    $curlManager = new CurlManager();
    $curlManager->setUrl("api/artifacts/".$artifactID."/changesets");
    $curlManager->setHeaders($userID, $userToken);

    $query = $curlManager->execute();

    if ($curlManager->checkHandleError()) {
        echo "Error: " . $curlManager->showError();
        echo "\n";
        return false;
    } else {
        $jsonResponse = json_decode($query);

        $artifactChangesets = array();

        foreach ($jsonResponse as $jsonObject) {
            $artifactValues = $jsonObject->values;
            $status = getStatus($artifactValues);

            if ($status !== null) {
                $aux['status'] = $status;
                $aux['submission'] = $jsonObject->submitted_on;
                $artifactChangesets[] = $aux;
            }
        }

        return $artifactChangesets;
    }
}

function getStatus($artifactValues) {
    $statusSubmissions = null;

    foreach ($artifactValues as $value) {
        if ($value->type == "sb" && $value->label == "Status") {
            $statusValues = $value->values;
            return $statusValues[0]->label;
        }
    }

    return $statusSubmissions;
}
?>
