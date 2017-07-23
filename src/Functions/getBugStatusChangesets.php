<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."CurlManager.class.php");

function getBugStatusChangesets($userID, $userToken, $bugID)
{
    $curlManager = new CurlManager();
    $curlManager->setUrl("api/artifacts/".$bugID."/changesets");
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
            $status = getBugStatus($artifactValues);

            if ($status !== null) {
                $aux['status'] = $status;
                $aux['submission'] = $jsonObject->submitted_on;
                $artifactChangesets[] = $aux;
            }
        }

        return $artifactChangesets;
    }
}

function getBugStatus($artifactValues) {
    $statusSubmissions = null;

   foreach ($artifactValues as $object) {
        if ($value->type == "sb" && $object->label ==="Status") {
            $statusValues = $object->values;
            return $statusValues[0]->label;
        }
    }

    return $statusSubmissions;
}
?>
