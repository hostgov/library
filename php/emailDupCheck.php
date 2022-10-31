<?php
include_once './validate.php';

header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
$email = $_GET["email"];
$res = checkEmail($email);
if ($res["code"] !== "0") {
  echo json_encode($res);
  die();
}
$res = checkEmailDup($email);
if ($res["code"] !== "0") {
    echo json_encode($res);
    die();
}
echo json_encode($res);


