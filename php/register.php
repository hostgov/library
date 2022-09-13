<?php
include_once './DataEnum.php';
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $requestParam = json_decode(file_get_contents('php://input'),true);



  if (!$requestParam) {
    $arr=array("code"=>"12", "message"=>SIGNUPENUM["12"]);
    echo json_encode($arr);
    die();
  }

  session_start();
  if (isset($_SESSION["lastTime"]) && $_SESSION["lastTime"] != "") {
    if (floor((time() - (int)$_SESSION["lastTime"]) % 86400 / 60) < 1) {
      $arr = array("code" => "10", "message" => SIGNUPENUM["10"]);
      echo json_encode($arr);
      die();
    }
  }

  $firstName = $requestParam["firstName"];
  $lastName = $requestParam["lastName"];
  $password = $requestParam["password"];
  $email = strtolower($requestParam["email"]);
//echo $firstName, $lastName, $password, $email;
//validate
  include_once './validate.php';
  $check = checkName($firstName);
  if ($check["code"] !== "0") {
    echo json_encode($check);
    die();
  }
  $check = checkName($lastName);
  if ($check["code"] !== "0") {
    echo json_encode($check);
    die();
  }
  $check = checkPassword($password);
  if ($check["code"] !== "0") {
    echo json_encode($check);
    die();
  }
  $check = checkEmail($email);
  if ($check["code"] !== "0") {
    echo json_encode($check);
    die();
  }
  $check = checkEmailDup($email);
  if ($check["code"] !== "0") {
    echo json_encode($check);
    die();
  }


  $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME, DB_PORT);
  if ($con->connect_error) {
    $arr = array("code"=>"1","message"=>SIGNUPENUM["1"]);
    echo json_encode($arr);
    die();
  }
  $password = password_hash($password, PASSWORD_BCRYPT);
  $memberType = MEMBER_TYPE_MEMBER;
  $con->set_charset(DB_CHARSET);
  $stmt = $con->stmt_init();
  $stmt = $con->prepare('insert into `user` (`member_type`,`first_name`,`last_name`,`email`,`password`) VALUES(?,?,?,?,?)');
  $stmt->bind_param("sssss", $memberType, $firstName, $lastName, $email, $password);
  if ($stmt->execute()) {
    $_SESSION["lastTime"] = time();
    $arr = array("code" => "0", "message" => SIGNUPENUM["0"]);
  } else {
    $arr = array("code" => "11", "message" => SIGNUPENUM["11"]);
  }
  echo json_encode($arr);
  $stmt->close();
  $con->close();
}


