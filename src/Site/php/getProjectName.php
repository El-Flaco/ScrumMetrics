<?php
namespace Flacox;

require_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."Functions".DIRECTORY_SEPARATOR."getProjects.php");

session_start();

$found = null;

if (isset($_SESSION['projectID']) && isset($_SESSION['userID']) && isset($_SESSION['userTK'])) {
    $result = getProjects($_SESSION['userID'], $_SESSION['userTK']);
    foreach ($result as $project) {
        if ($project['id'] == $_SESSION['projectID']) {
            $found = $project['label'];
            break;
        }
    }
    
    if ($found != null) {
        echo $found;
    } else {
        echo "FALSE";
    }
} else {
    echo "FALSE";
}
?>
