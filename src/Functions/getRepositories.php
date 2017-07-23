<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."CurlManager.class.php");

function getRepositories($userID, $userToken, $projectID)
{
    $curlManager = new CurlManager();
    $curlManager->setUrl("api/projects/".$projectID."/git");
    $curlManager->setHeaders($userID, $userToken);

    $query = $curlManager->execute();

    if ($curlManager->checkHandleError()) {
        echo "Error: " . $curlManager->showError();
        echo "\n";
        return false;
    } else {
        $jsonResponse = json_decode($query);
        $repositories = null;
        
        if (count($jsonResponse) > 0) {
            $i = 0;
            $repositories = array();
            foreach ($jsonResponse as $element) {
                $repositories[] = $element[$i]->path;
                $i++;
            }
        }
        
        return $repositories;
    }
}
?>
