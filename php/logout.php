<?php
session_start();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(),"", time() - 3600, "/");
}
unset($_SESSION["member"]);
unset($_SESSION["memberData"]);
echo json_encode(array("code"=>"0", "message"=>"SUCCESS"));