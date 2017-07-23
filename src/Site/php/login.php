<?php
namespace Flacox;

require_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."TuleapUser.class.php");

session_start();
$_SESSION = array();

$userName = $_POST['userName'];
$userPassword = $_POST['userPassword'];


echo $userName . "-" . $userPassword;

$tuleapUser = new TuleapUser($userName, $userPassword);

if ($tuleapUser->getId() != null && $tuleapUser->getId() != '') {
    $_SESSION['userID'] = $tuleapUser->getId();
    $_SESSION['userTK'] = $tuleapUser->getToken();
    $_SESSION['userName'] = $tuleapUser->getName();
    header("Location: ../home.html");
} else {
    header("Location: ../login.html");
}
?>
