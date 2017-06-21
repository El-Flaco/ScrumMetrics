<?php
namespace Flacox;

require_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."Functions".DIRECTORY_SEPARATOR."getProjects.php");

session_start();

if (isset($_SESSION['userID']) && isset($_SESSION['userTK'])) {
    $userID = $_SESSION['userID'];
    $userToken = $_SESSION['userTK'];

    $result = getProjects($userID, $userToken);
    $encodedResponse = json_encode($result);
    header('Content-type: application/json');
    exit($encodedResponse);
} else {
    echo "FALSE";
}
?>
