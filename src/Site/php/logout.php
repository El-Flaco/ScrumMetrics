<?php
namespace Flacox;

session_start();
session_unset();
session_destroy();
$_SESSION = array();
header("Location: ../login.html");
?>
