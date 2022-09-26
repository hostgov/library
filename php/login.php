<?php
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include_once "./ExceptionInfo.php";
    // anti spam
    session_start();
    if (isset($_SESSION["failedTimeOffset"]) && floor((time() - (int)$_SESSION["failedTimeOffset"]) % 86400 / 60) < 1) {
        echo json_encode(array("code" => "2", "message" => LOGIN["0"]));
        die();
    }

    if (isset($_SESSION["member"]) && $_SESSION["member"] == "true") {
        echo json_encode(array("code" => "3", "message" => LOGIN["3"]));
        die();
    }

    include_once "validate.php";
    //validation
    $email = $_POST['email'];
    $res = checkEmail($email);
    if ($res["code"] != "0") {
        echo json_encode(array("code"=> "4", "message"=> LOGIN["4"]));
        die();
    }

    $password = $_POST['password'];
    $res = checkPassword($password);
    if ($res["code"] != "0") {
        echo json_encode(array("code"=> "5", "message"=> LOGIN["5"]));
        die();
    }


    include_once './base_params.php';

    $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME, DB_PORT);
    if ($con->connect_error) {
        $arr = array("code" => "1", "message" => SIGNUPENUM["1"]);
        echo json_encode($arr);
        die();
    }

    include_once 'ExceptionInfo.php';
    include_once 'DataEnum.php';

    $con->set_charset(DB_CHARSET);
    $stmt = $con->stmt_init();
    $stmt = $con->prepare('select `id`, `member_type`,`first_name`,`last_name`,`email`, `password` from `user` where `email` = ?');
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $memberId = $row['id'];
            $memberType = $row['member_type'];
            $firstName = $row['first_name'];
            $lastName = $row['last_name'];
            $storedPassword = $row['password'];
            $compare = password_verify($password, $storedPassword);
            if ($compare) {
                $_SESSION["member"] = "true";
                $memberData = array("memberId"=> $memberId, "memberType"=>$memberType, "firstName"=>$firstName, "lastName"=>$lastName);
                $_SESSION["memberData"] = $memberData;
                setcookie(session_name(), session_id(), time() + 3600 * 2, "/");
                setcookie(USER_LOGIN_SESSION_NAME, json_encode($memberData), time() + 3600 * 2, "/");
                unset($_SESSION["failed"]);
                unset($_SESSION["failedTimeOffset"]);
                echo json_encode(array('code' => '0', 'message' => LOGIN['0'], 'data'=> $memberData));
            } else {
                if (isset($_SESSION["failed"])) {
                    if ($_SESSION["failed"] >= 5) {
                        $_SESSION["failedTimeOffset"] = time();
                    }
                } else {
                    $_SESSION["failed"] = 0;
                }
                $_SESSION["failed"]++;
                echo json_encode(array('code' => '1', 'message' => LOGIN['1']));
            }


        } else {
            if (isset($_SESSION["failed"])) {
                if ($_SESSION["failed"] >= 5) {
                    $_SESSION["failedTimeOffset"] = time();
                }
            } else {
                $_SESSION["failed"] = 0;
            }
            $_SESSION["failed"]++;
            echo json_encode(array('code' => '1', 'message' => LOGIN['1']));
        }
    }
    $stmt->close();
    $con->close();
}
