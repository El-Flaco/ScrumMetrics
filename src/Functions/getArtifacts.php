<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."TuleapUser.class.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."CurlManager.class.php");

function getArtifacts(TuleapUser $user, $milestoneID)
{
    $curlManager = new CurlManager();
    $curlManager->setUrl("api/milestones/".$milestoneID."/content");
    $curlManager->setHeaders($user->getId(), $user->getToken());

    $query = $curlManager->execute();

    if ($curlManager->checkHandleError()) {
        echo "Error: " . $curlManager->showError();
        echo "\n";
        return false;
    } else {
        $jsonResponse = json_decode($query);
        
        $artifacts = array();
        $i = 0;

        foreach($jsonResponse as $jsonObject) {
            $artifacts[$i] = array(
                'id' => $jsonObject->id, 
                'initial_effort' => $jsonObject->initial_effort,
                'type' => $jsonObject->type
            );
            $i++;
        }

        return $artifacts;
    }
}
?>
