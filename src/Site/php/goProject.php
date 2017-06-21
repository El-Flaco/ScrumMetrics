<?php
namespace Flacox;

session_start();
$projectID = $_GET['projectId'];

if ($projectID != '' && isset($_SESSION['userID']) && isset($_SESSION['userTK'])) {
    $_SESSION['projectID'] = $projectID;
    header("Location: ../project.html");
    echo "W";
} else {
    echo "FALSE";
}
?>
