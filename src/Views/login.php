<?php
namespace Flacox;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."TuleapUser.class.php");
?>
<html>
<head>
    <title>Login meTricks</title>
</head>
<body>
    <form method="POST" action="login.php" style="border: 1px solid black; display: table; margin: 0px auto; padding-left: 10px; padding-bottom: 5px;">
        <table width="300" cellpadding="4" cellspacing="1">
            <tr><td></td><td colspan="3"><strong>User Login</strong></td></tr>
            <tr></tr>
            <tr><td width="78">Username</td><td width="6">:</td><td width="294"><input name="username" size="25" type="text"></td></tr>
            <tr><td>Password</td><td>:</td><td><input name="password" size="25" type="password"></td></tr>
            <tr><td></td><td></td><td><input name="submit" type="submit" Value="Login"></td></tr>
        </table>
        
        <?php        
            session_start();
            
            if (isset($_POST)) {
                $name = $_POST['username'];
                $password = $_POST['password'];
                
                if ($name !== '' && $password !== '') {
                    $user = new TuleapUser($name, $password);
                    
                    if ($user->getToken() !== null && $user->getId() !== null) {
                        $_SESSION['user'] = $user;
                        
                        if (isset($_SESSION)) {
                            header('Location: home.php');
                        }
                    }
                }
            }
        ?>
        
    </form>
</body>
</html>
