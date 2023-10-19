<?
// delete all session
session_start();
session_destroy();

setcookie("uname", 'ex',time()+3600*24*365,'/',$_SERVER['HTTP_HOST']);
// bye bye :)
header('location:index.php');
?>