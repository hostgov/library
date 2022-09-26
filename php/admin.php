<?php
include_once './DataEnum.php';
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include_once './ExceptionInfo.php';


    if (empty($_POST['memberId'])) {
        echo json_encode(array("code" => "14", "message" => ADMINOPRATION["14"]));
        die();
    }
    $memberId = (int)$_POST['memberId'];
    session_start();
    if (!$_SESSION['member'] || $_SESSION['member'] != "true" || !$_SESSION['memberData'] || $_SESSION['memberData']['memberType'] != MEMBER_TYPE_ADMIN) {
        echo json_encode(array("code" => "14", "message" => ADMINOPRATION["14"]));
        die();
    }
    if ($memberId != $_SESSION['memberData']['memberId']) {
        echo json_encode(array("code" => "14", "message" => ADMINOPRATION["14"]));
        die();
    }

    if (isset($_SESSION["lastUpdateTime"]) && $_SESSION["lastUpdateTime"] != "") {
        if (floor((time() - (int)$_SESSION["lastUpdateTime"]) % 86400) < 10) {
            $arr = array("code" => "11", "message" => ADMINOPRATION["11"]);
            echo json_encode($arr);
            die();
        }
    }
    if (empty($_POST['updateType']) || !in_array($_POST['updateType'], ADMIN_UPDATE_TYPE)) {
        echo json_encode(array("code" => "15", "message" => ADMINOPRATION["15"]));
        die();
    }


    $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME, DB_PORT);
    if ($con->connect_error) {
        $arr = array("code" => "9", "message" => ADMINOPRATION["9"]);
        echo json_encode($arr);
        die();
    }

    $con->set_charset(DB_CHARSET);

    $stmt = $con->stmt_init();

    $updateType = $_POST['updateType'];

    if ($updateType == 'delete' || $updateType == 'return') {
        if (empty($_POST["bookId"])) {
            echo json_encode(array("code" => "16", "message" => ADMINOPRATION["16"]));
            die();
        }
        $bookId = $_POST["bookId"];
        if ($updateType == 'delete') {
            if(!$stmt->prepare("UPDATE book_status SET `status`='Deleted', `member_id`=? where `book_id`=? ")){
                echo json_encode(array("code" => "17", "message" => ADMINOPRATION["17"]));
                die();
            }
        } else if ($updateType == 'return') {
            if(!$stmt->prepare("UPDATE book_status SET `status`='Available', `member_id`=? where `book_id`=? ")){
                echo json_encode(array("code" => "17", "message" => ADMINOPRATION["17"]));
                die();
            }
        }

        $stmt -> bind_param('ii', $memberId,$bookId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows == 1) {
                echo json_encode(array('code' => '0', 'message' => ADMINOPRATION["0"]));
            } else {
                echo json_encode(array('code' => '18', 'message' => ADMINOPRATION["18"]));
                die();
            }
        } else {
            echo json_encode(array('code' => '18', 'message' => ADMINOPRATION["18"]));
            die();
        }


    } else if ($updateType == 'add' || $updateType == 'edit') {
        include_once './validate.php';

        $file = '';
        $imgName = '';
        $bookTitle = $_POST['bookTitle'];
        $author = $_POST['author'];
        $publisher = $_POST['publisher'];
        $language = $_POST['language'];
        $category = $_POST['category'];
        $currBookImage = $_POST['currBookImage'];
        if (!empty($_POST['bookId'])) {
            $bookId = (int)$_POST['bookId'];
        }


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

        $haveImage = false;
        if (array_key_exists("image", $_FILES)) {
            $haveImage = true;
            $file = $_FILES["image"];
            $check = checkImage($file);
            if ($check["code"] != "0") {
                echo json_encode($check);
                die();
            }
            $fileType = explode('/', $file['type']);
            $imgBasicName = '/files/' . time() . '.' . $fileType[1];
            $imgSaveName = $_SERVER["DOCUMENT_ROOT"] . '/library' . $imgBasicName;
            $imgHttpName = BASEURL . $imgBasicName;
            if (!move_uploaded_file($file['tmp_name'], $imgSaveName)) {
                $arr = array("code" => "5", "message" => ADMINOPRATION["5"]);
                echo json_encode($arr);
                die();
            }
            if ($currBookImage != "") {
//      http://localhost/library/files/1661074651.png
                $pathArr = explode('/', $currBookImage);
                $currBookImageSavePath = $_SERVER["DOCUMENT_ROOT"] . '/' . $pathArr[count($pathArr) - 3] . '/' . $pathArr[count($pathArr) - 2] . '/' . $pathArr[count($pathArr) - 1];
                unlink($currBookImageSavePath);
            }
        }

        if ($updateType == "add") {
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
                if ($con->query('ROLLBACK')) {
                    $arr = array("code" => "12", "message" => ADMINOPRATION["12"]);
                } else {
                    $arr = array("code" => "13", "message" => ADMINOPRATION["13"]);
                }
                echo json_encode($arr);
                unlink($imgSaveName);
                die();
            }
            if ($con->query('COMMIT')) {
                $arr = array("code" => "0", "message" => ADMINOPRATION["0"]);
                echo json_encode($arr);
            }
        } else if ($updateType == "edit") {
            if ($haveImage) {
                $stmt = $con->prepare("update book set `book_title`=?, `author`=?, `publisher`=?, `language`=?, `category`=?,`image`=? where `id`=?");
                $stmt->bind_param("ssssssi", $bookTitle, $author, $publisher, $language, $category, $imgHttpName, $bookId);
            } else {
                $stmt = $con->prepare("update book set `book_title`=?, `author`=?, `publisher`=?, `language`=?, `category`=? where `id`=?");
                $stmt->bind_param("sssssi", $bookTitle, $author, $publisher, $language, $category, $bookId);
            }
            if ($stmt->execute() && $stmt->affected_rows == 1) {
                echo json_encode(array("code" => "0", "message" => ADMINOPRATION["0"]));
            } else {
                echo json_encode(array("code"=>"19", "message"=>ADMINOPRATION["19"]));
            }
        }
    }





    $stmt->close();
    $con->close();


}

