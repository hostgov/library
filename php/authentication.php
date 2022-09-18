<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once "./ExceptionInfo.php";
    session_start();
    if (isset($_SESSION["member"]) && $_SESSION["member"] == "true") {
        if (isset($_SESSION["memberData"])) {
            include_once "DataEnum.php";
            $memberData = $_SESSION["memberData"];
            if ($memberData["memberType"] == MEMBER_TYPE_MEMBER) {
                echo json_encode(array("code" => "2", "message" => AUTHEN["2"], "data"=>$memberData));
                die();
            } else if ($memberData["memberType"] == MEMBER_TYPE_ADMIN) {
                echo json_encode(array("code" => "3", "message" => AUTHEN["3"], "data"=>$memberData));
                die();
            }
        } else {
            unset($_SESSION["member"]);
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(),"", time() - 3600, "/");
            }
            echo json_encode(array("code" => "0", "message" => AUTHEN["0"]));
            die();
        }

    } else {
        echo json_encode(array("code" => "1", "message" => AUTHEN["1"]));
        die();
    }
}