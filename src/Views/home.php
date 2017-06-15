<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."getProjects.php");
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
        echo "<center><h2>Hello, user " . $_SESSION['user']. " _tk_: " . $_SESSION['token'] . "</h2></center>";
    }
    ?>
    <div name="menu_list">
        <button name="get_projects" onclick="getProjects()">Get Projects</button>
    </div>
</body>
</html>
