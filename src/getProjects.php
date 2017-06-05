<?php
namespace Flacox;

require_once(dirname(__FILE__).'/TuleapUser.class.php');

function getProjects(TuleapUser $user)
{
    $curlHandle = curl_init();
    $url = "https://tuleap-web.tuleap-aio-dev.docker/";

    curl_setopt($curlHandle, CURLOPT_URL, $url."api/projects");
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "X-Auth-Token: " . $user->getToken();
    $headers[] = "X-Auth-UserId: " . $user->getId();
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);

    $query = curl_exec($curlHandle);

    if (curl_errno($curlHandle)) {
        echo 'Error:' . curl_error($curlHandle);
        return false;
    }

    $jsonResponse = json_decode($query);

    $projectsID = array();
    foreach ($jsonResponse as $jsonObject) {
        $projectsID[] = $jsonObject->id;
    }
    
    return $projectsID;
}

$userName = $argv[1];
$password = $argv[2];

$u = new TuleapUser($userName, $password);
if ($u->getToken() !== NULL) {
    $projects = getProjects($user1);
}
?>