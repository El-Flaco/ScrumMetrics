<?php
namespace Flacox;

session_start();

if (isset($_SESSION['userID']) && isset($_SESSION['userTK']) && isset($_SESSION['userName'])) {
    echo $_SESSION['userName'];
} else {
    echo "FALSE";
}

?>
