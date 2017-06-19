<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Functions".DIRECTORY_SEPARATOR."getProjects.php");
<<<<<<< HEAD
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."TuleapUser.class.php");
=======
>>>>>>> elementsType
?>
<html>
<head>
    <title>Welcome to meTricks</title>
</head>
<body>
    <button name="logout" type="button" onclick="logout()">Logout</button>
    <?php
    session_start();
    if ($_SESSION['user'] == '' || $_SESSION['token'] == '') {
        header("Location: login.php");
    } else {
        $tuleapUser = $_SESSION['user'];
        echo "<center><h2>Hello, user " . $tuleapUser->getId() . " _tk_: " . $tuleapUser->getToken() . "</h2></center>";
    }
    ?>
    <div name="menu_list">
        <button name="get_projects" onclick="getProjects()">Get Projects</button>
    </div>
</body>
</html>
