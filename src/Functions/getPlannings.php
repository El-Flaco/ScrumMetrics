<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."TuleapUser.class.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."CurlManager.class.php");

function getPlannings(TuleapUser $user, $projectID)
{
    $curlManager = new CurlManager();
    $curlManager->setUrl("api/projects/".$projectID."/plannings");
    $curlManager->setHeaders($user->getId(), $user->getToken());

    $query = $curlManager->execute();

    if ($curlManager->checkHandleError()) {
        echo "Error: " . $curlManager->showError();
        echo "\n";
        return false;
    } else {
        $jsonResponse = json_decode($query);

        $planningsInfo = array();
        $i = 0;
        foreach ($jsonResponse as $jsonObject) {
            if (strpos($jsonObject->label, 'Sprint Planning') !== false) {
                $planningsInfo[$i]['id'] = $jsonObject->id;
                $planningsInfo[$i]['label'] = $jsonObject->label;
            }
            $i++;
        }

        return $planningsInfo;
    }
}
?>
