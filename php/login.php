<?php
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  //todo anti spam
  $email = $_POST['email'];
  //todo validation
  $password = $_POST['password'];
  //todo validation



  $password = password_hash($password, PASSWORD_BCRYPT);
  include_once './base_params.php';

  $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME, DB_PORT);
  if ($con->connect_error) {
    $arr = array("code"=>"1","message"=>SIGNUPENUM["1"]);
    echo json_encode($arr);
    die();
  }

  include_once 'ExceptionInfo.php';

  $con->set_charset(DB_CHARSET);
  $stmt = $con->stmt_init();
  $stmt = $con->prepare('select `member_type`,`first_name`,`last_name`,`email` from `user` where `email` = ? and `password` = ?');
  $stmt->bind_param("ss", $email,  $password);
  if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
      $row = $result->fetch_assoc();
      $memberType = $row['member_type'];
      //todo login success making user session.
      echo json_encode(array('code'=>'0', 'message'=>LOGIN['0']));
    } else {
      echo json_encode(array('code'=>'1', 'message'=>LOGIN['1']));
    }
  }
  $stmt->close();
  $con->close();
}
