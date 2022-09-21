<?php
//header('Access-Control-Allow-Origin:*');
//header('Content-type: application/json');
//$response = json_decode(file_get_contents('php://input'),true);
//
//$firstname = $response["firstName"];
//var_dump($firstname);

//if ($_SERVER["REQUEST_METHOD"] == "POST") {
// var_dump($_FILES);
//print_r($_FILES);
//
//}
//if (($_SERVER["REQUEST_METHOD"]=== "POST")) {
//  session_start();
////  $_SESSION["token"]="12345";
//  var_dump($_SESSION["token"]);
//  echo "session_id: ".session_id();
//}
//$str = "aksjd我 kjk!. -?#-@=_$%&(~)\<script>\</script>";
//echo $str;
//
//$reg = '/[\w\x{4e00}-\x{9fa5}]+/u';
//$bool = preg_match($reg, $str);
//echo $bool;
$a = 'http://localhost/library/files/1663672907.jpeg';
$b = explode('/', $a);
$currBookImageSavePath = $_SERVER["DOCUMENT_ROOT"]. '/'.$b[count($b) - 3]. '/'.$b[count($b) - 2]. '/'.$b[count($b) - 1];
echo $currBookImageSavePath;
if (unlink($currBookImageSavePath)) {
    echo 'ok';
} else {
    echo 'failed';
}