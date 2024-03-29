<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."CurlManager.class.php");

function getProjects($userID, $userToken)
{
    $curlManager = new CurlManager();
    $curlManager->setUrl("api/projects");
    $curlManager->setHeaders($userID, $userToken);

    $query = $curlManager->execute();

    if ($curlManager->checkHandleError()) {
        echo "Error: " . $curlManager->showError();
        echo "\n";
        return false;
    } else {
        $jsonResponse = json_decode($query);

        $projects = array();
        $i = 0;
        foreach ($jsonResponse as $jsonObject) {
            $projects[$i]['id'] = $jsonObject->id;
            $projects[$i]['label'] = $jsonObject->label;
            $i++;
        }
    
        return $projects;
    }
}
?>
