<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."TuleapUser.class.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."CurlManager.class.php");

function getArtifactInformation(TuleapUser $user, $artifactID)
{
    $curlManager = new CurlManager();
    $curlManager->setUrl("api/artifacts/".$artifactID);
    $curlManager->setHeaders($user->getId(), $user->getToken());

    $query = $curlManager->execute();

    if ($curlManager->checkHandleError()) {
        echo "Error: " . $curlManager->showError();
        echo "\n";
        return false;
    } else {
        $jsonResponse = json_decode($query);
        
        $artifactInformation = array();
        if (strpos($jsonResponse->xref, 'story') !== false || strpos($jsonResponse->xref, 'bug') !== false) {
            $artifactInformation['id'] = $jsonResponse->id;
            $artifactInformation['status'] = $jsonResponse->status;
            $artifactInformation['init_date'] = $jsonResponse->submitted_on;
            $artifactInformation['last_update'] = $jsonResponse->last_modified_date;
        }

        return $artifactInformation;
    }

}
?>
