<?php
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once './base_params.php';
    include_once './ExceptionInfo.php';
    include_once './validate.php';
    $searchType = '';
    if (!$_POST['type']) {
        echo json_encode(array('code' => '1', 'message' => UPDATEBOOKSDATA["1"]));
        die();
    } else {
        $searchType = formFilter($_POST['type']);
        if (!$searchType || $searchType == '' || !in_array($searchType, ADMIN_SEARCH_TYPE)) {
            echo json_encode(array('code' => '1', 'message' => UPDATEBOOKSDATA["1"]));
            die();
        }
    }

    $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME, DB_PORT);
    if ($con->connect_error) {
        $arr = array("code" => "2", "message" => UPDATEBOOKSDATA["2"]);
        echo json_encode($arr);
        die();
    }
    $con->set_charset(DB_CHARSET);
    $stmt = $con->stmt_init();

    if ($searchType == "borrow") {
        $bookId = (int)$_POST["bookId"];
        $memberId = (int)$_POST["memberId"];
        if (!$bookId || !$memberId || $bookId == '' || $memberId == '') {
            echo json_encode(array('code' => '1', 'message' => UPDATEBOOKSDATA["1"]));
            die();
        }
        $stmt = $con->prepare(
            "UPDATE book_status SET `status`='Onloan', `member_id`=? where `book_id`=?"
        );
        $stmt -> bind_param('ii', $memberId,$bookId);
    }
    else {
        echo json_encode(array('code' => '3', 'message' => UPDATEBOOKSDATA["3"]));
        die();
    }


    if ($stmt->execute()) {
        if ($stmt->affected_rows == 1) {
            echo json_encode(array('code' => '0', 'message' => UPDATEBOOKSDATA["0"]));
        } else {
            echo json_encode(array('code' => '4', 'message' => UPDATEBOOKSDATA["4"]));
        }
    }



    $stmt->close();
    $con->close();
}