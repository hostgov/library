<?php
include_once './DataEnum.php';
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  session_start();
  if (isset($_SESSION["lastAddTime"]) && $_SESSION["lastAddTime"] != "") {
    if (floor((time() - (int)$_SESSION["lastAddTime"]) % 86400) < 10) {
      $arr = array("code" => "11", "message" => ADDNEWBOOK["11"]);
      echo json_encode($arr);
      die();
    }
  }


  include_once './validate.php';
  $file = $_FILES["image"];
  $imgName = '';
  $bookTitle = $_POST['bookTitle'];
  $author = $_POST['author'];
  $publisher = $_POST['publisher'];
  $language = $_POST['language'];
  $category = $_POST['category'];
//    todo session control
  $memberId = $_POST['memberId'];

  $list = array($bookTitle, $author, $publisher);
  for ($i = 0; $i < sizeof($list); $i++) {
    $check = checkBookDetails($list[$i]);
    if ($check["code"] != "0") {
      echo json_encode($check);
      die();
    }
  }

  $check = checkBookLanguage($language);
  if ($check["code"] != "0") {
    echo json_encode($check);
    die();
  }
  $check = checkBookCategory($category);
  if ($check["code"] != "0") {
    echo json_encode($check);
    die();
  }

  $check = checkImage($file);
  if ($check["code"] != "0") {
    echo json_encode($check);
    die();
  }
  $fileType = explode('/', $file['type']);
  $imgBasicName = '/files/' . time() . '.' . $fileType[1];
  $imgSaveName = $_SERVER["DOCUMENT_ROOT"].'/library'.$imgBasicName;
  $imgHttpName = BASEURL . $imgBasicName;
  if (!move_uploaded_file($file['tmp_name'], $imgSaveName)) {
    $arr = array("code" => "5", "message" => ADDNEWBOOK["5"]);
    echo json_encode($arr);
    die();
  }


  $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME, DB_PORT);
  if ($con->connect_error) {
    $arr = array("code" => "9", "message" => ADDNEWBOOK["9"]);
    echo json_encode($arr);
    die();
  }

  $con->set_charset(DB_CHARSET);

  $stmt = $con->stmt_init();
  $sqlRes1 = $con->query('START TRANSACTION');
  $stmt = $con->prepare('insert into `book` (`book_title`,`author`,`publisher`,`language`,`category`,`image`) VALUES(?,?,?,?,?,?)');
  $stmt->bind_param("ssssss", $bookTitle, $author, $publisher, $language, $category, $imgHttpName);
  $sqlRes2 = $stmt->execute();
  $bookId = $stmt->insert_id;
  $status = 'Available';
  $stmt = $con->prepare('insert into `book_status` (`book_id`,`status`) VALUES(?,?)');
  $stmt->bind_param("is", $bookId, $status);
  $sqlRes3 = $stmt->execute();

  if (!$sqlRes1 || !$sqlRes2 || !$sqlRes3) {
      if($con->query('ROLLBACK')) {
          $arr = array("code"=>"12", "message"=>ADDNEWBOOK["12"]);
      } else {
          $arr = array("code"=>"13", "message"=>ADDNEWBOOK["13"]);
      }
      echo json_encode($arr);
      unlink($imgSaveName);
      die();
  }
  if ($con->query('COMMIT')) {
      $arr = array("code" => "0", "message" => ADDNEWBOOK["0"]);
      echo json_encode($arr);
  }


  $stmt->close();
  $con->close();


}
?>
