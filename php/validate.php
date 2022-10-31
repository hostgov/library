<?php
include_once 'base_params.php';
include_once './ExceptionInfo.php';
include_once './DataEnum.php';

function checkName($name): array
{
    $name = formFilter($name);
    if (empty($name)) {
        return array("code" => "13", "message" => SIGNUPENUM["13"]);
    }
    if (strlen($name) <= 0 || strlen($name) > 20) {
        return array("code" => "6", "message" => SIGNUPENUM["6"]);
    }
    $reg = '/[\w\x{4e00}-\x{9fa5}]+/u';
    if (!preg_match($reg, $name)) {
        return array("code" => "7", "message" => SIGNUPENUM["7"]);
    }
    return array("code" => "0", "message" => SIGNUPENUM["0"]);
}

function checkEmail($email): array
{
    $email = formFilter($email);
    if (empty($email)) {
        return array("code" => "14", "message" => SIGNUPENUM["14"]);
    }
    if (strlen($email) <= 3 || strlen($email) > 50) {
        return array("code" => "4", "message" => SIGNUPENUM["4"]);
    }
    $reg = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
    if (!preg_match($reg, $email)) {
        return array("code" => "5", "message" => SIGNUPENUM["5"]);
    }
    return array("code" => "0", "message" => SIGNUPENUM["0"]);
}

function checkEmailDup($email): array
{
    $email = strtolower($email);
    $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME, DB_PORT);
    if ($con->connect_error) {
        return array("code" => "1", "message" => SIGNUPENUM["1"] . $con->connect_error);
    }
    $con->set_charset(DB_CHARSET);
    $stmt = $con->stmt_init();
    $stmt = $con->prepare("select * from user where email=?");
    $stmt->bind_param('s', $email);
    if (!$stmt->execute()) {
        return array("code" => "2", "message" => SIGNUPENUM["2"]);
    }
    $result = $stmt->get_result();
    $dupEmail = $result->num_rows;

    $con->close();
    $stmt->close();
    if ($dupEmail !== 0) {
        return array("code" => "3", "message" => SIGNUPENUM["3"]);

    }
    return array("code" => "0", "message" => SIGNUPENUM["0"]);
}


function checkPassword($password): array
{
    $password = formFilter($password);
    if (empty($password)) {
        return array("code" => "15", "message" => SIGNUPENUM["15"]);
    }
    if (strlen($password) <= 5 || strlen($password) > 15) {
        return array("code" => "8", "message" => SIGNUPENUM["8"]);
    }
    $reg = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,15}/";
    if (!preg_match($reg, $password)) {
        return array("code" => "9", "message" => SIGNUPENUM["9"]);
    }
    return array("code" => "0", "message" => SIGNUPENUM["0"]);
}

function formFilter($str): string
{
    $str = trim($str);
    $str = stripslashes($str);
    return htmlspecialchars($str);
}

function checkImage($file): array
{
    if ($file["error"] != 0) {
        return array("code" => "1", "message" => ADMINOPRATION["1"]);
    }

    $fileType = explode('/', $file['type']);
    if ($fileType[0] != "image") {
        return array("code" => "2", "message" => ADMINOPRATION["2"]);
    }
    $imageTypeArray = UPLOAD_IMAGE_TYPES;
    if (!in_array($fileType[1], $imageTypeArray)) {
        return array("code" => "3", "message" => ADMINOPRATION["3"]);
    }
    $imageSize = $file['size'];
    if ($imageSize > IMAGE_UPLOAD_MAX_SIZE) {
        return array("code"=>"4", "message" => ADMINOPRATION["4"]);
    }

    return array("code"=>"0", "message"=>ADMINOPRATION["0"]);
}
function checkBookDetails($str): array {
    $str = formFilter($str);
    $reg = '/[\w\x{4e00}-\x{9fa5}]+/u';

    if (empty($str || strlen($str) > 100 || !preg_match($reg, $str))) {
        return array("code" => "6", "message" => ADMINOPRATION["6"]);
    }
    return array("code" => "0", "message" => ADMINOPRATION["0"]);
}
function checkBookLanguage($str):array {
    $str = formFilter($str);
    if (empty($str || !in_array($str, BOOK_LANGUAGES))) {
        return array("code" => "7", "message" => ADMINOPRATION["7"]);
    }
    return array("code" => "0", "message" => ADMINOPRATION["0"]);
}
function checkBookCategory($str):array {
    $str = formFilter($str);
    if (empty($str || !in_array($str, BOOK_CATEGORIES))) {
        return array("code" => "8", "message" => ADMINOPRATION["8"]);
    }
    return array("code" => "0", "message" => ADMINOPRATION["0"]);
}