<?php
include_once "DataEnum.php";
session_start();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(),"", time() - 3600, "/");
}
if (isset($_COOKIE[USER_LOGIN_SESSION_NAME])) {
    setcookie(USER_LOGIN_SESSION_NAME, "", time() - 3600, "/");
}
unset($_SESSION["member"]);
unset($_SESSION["memberData"]);
echo json_encode(array("code"=>"0", "message"=>"SUCCESS"));