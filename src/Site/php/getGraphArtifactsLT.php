<?php
namespace Flacox;

require_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."Drawers".DIRECTORY_SEPARATOR."drawArtifactsLeadTime.php");

session_start();

if (isset($_SESSION['projectID']) && isset($_SESSION['userID']) && isset($_SESSION['userTK'])) {
    $result = drawArtifactsLeadTime($_SESSION['userID'], $_SESSION['userTK'], $_SESSION['projectID']);
    echo $result;
} else {
    echo "FALSE";
}
?>
