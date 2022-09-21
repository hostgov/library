<?php
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once './base_params.php';
    include_once './ExceptionInfo.php';
    include_once './validate.php';
    $searchType = '';

    if (!$_POST['type']) {
        echo json_encode(array('code' => '3', 'message' => GETBOOKSDATA["3"]));
        die();
    } else {
        $searchType = formFilter($_POST['type']);
        if (!$searchType || $searchType == '' || !in_array($searchType, ADMIN_SEARCH_TYPE)) {
            echo json_encode(array('code' => '3', 'message' => GETBOOKSDATA["3"]));
            die();
        }
    }


    $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME, DB_PORT);
    if ($con->connect_error) {
        $arr = array("code" => "2", "message" => GETBOOKSDATA["2"]);
        echo json_encode($arr);
        die();
    }

    $con->set_charset(DB_CHARSET);
    $stmt = $con->stmt_init();

    $pageSize = ADMIN_BOOK_PAGESIZE;
    $total = 0;
    $curPage = 1;
    $offSet = 0;
    if ($searchType != 'one') {
        $curPage = (int)$_POST['pageNumber'];
        $offSet = ($curPage - 1) * $pageSize;
    } else {
        $bookId = (int)$_POST['bookId'];
        if (!$bookId || $bookId == '') {
            echo json_encode(array("code" => "6", "message"=> GETBOOKSDATA["6"]));
        }
    }


    // params {'type': all/exa/fuz, 'colName': columnName, 'value': value}
    if ($searchType == 'all') {
        $stmt = $con->prepare("
SELECT count(*) 
FROM book LEFT JOIN book_status ON book.`id`=book_status.`book_id`
WHERE book_status.`status` != 'Deleted'");
        if($stmt->execute()) {
            $stmt->bind_result($total);
            $stmt->fetch();
        } else {
            echo json_encode(array("code"=>"5", "message"=>GETBOOKSDATA["5"]));
            die();
        }
        if ($total == 0) {
            echo json_encode(array("code"=>"0", "message"=>GETBOOKSDATA["0"]));
            die();
        }

        $stmt = $con->stmt_init();
        $stmt = $con->prepare("
SELECT book.`id`, book.`book_title`,book.`author`,book.`publisher`,book.`language`,book.`category`,book.`image`, book_status.`status` 
FROM book LEFT JOIN book_status ON book.`id`=book_status.`book_id`
WHERE book_status.`status` != 'Deleted' limit ? , ?");
        $stmt->bind_param('ii', $offSet, $pageSize);
    } else if ($searchType == 'one') {
        $stmt = $con->prepare("
SELECT book.`id`, book.`book_title`,book.`author`,book.`publisher`,book.`language`,book.`category`,book.`image`, book_status.`status` 
FROM book LEFT JOIN book_status ON book.`id`=book_status.`book_id`
WHERE book_status.`status` != 'Deleted' and book.`id`=?");
        $stmt -> bind_param('i', $bookId);
    } else {
        $colName = '';
        if (!$_POST['colName']) {
            echo json_encode(array('code' => "4", 'message' => GETBOOKSDATA["4"]));
            die();
        } else {
            $colName = formFilter($_POST['colName']);
            if ($colName == '' || !in_array($colName, BOOK_SEARCH_COLUMN_NAME)) {
                echo json_encode(array('code' => "4", 'message' => GETBOOKSDATA["4"]));
                die();
            }
        }
        $searchValue = '';
        if (!$_POST['searchContent']) {
            echo json_encode(array('code' => "4", 'message' => GETBOOKSDATA["4"]));
            die();
        } else {
            $searchValue = formFilter($_POST['searchContent']);
            if ($searchValue == '') {
                echo json_encode(array('code' => "4", 'message' => GETBOOKSDATA["4"]));
                die();
            }
        }

        if ($searchType == 'exa') {
            $sql = "
SELECT count(*) 
FROM book
LEFT JOIN book_status
ON book.`id`=book_status.`book_id`
WHERE book_status.`status` != 'Deleted'
AND " . $colName . "=?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $searchValue);
            if($stmt->execute()) {
                $stmt->bind_result($total);
                $stmt->fetch();
            } else {
                echo json_encode(array("code"=>"5", "message"=>GETBOOKSDATA["5"]));
                die();
            }
            if ($total == 0) {
                echo json_encode(array("code"=>"0", "message"=>GETBOOKSDATA["0"]));
                die();
            }

            $sql = "
SELECT book.`id`, book.`book_title`,book.`author`,book.`publisher`,book.`language`,book.`category`,book.`image`, book_status.`status` 
FROM book
LEFT JOIN book_status
ON book.`id`=book_status.`book_id`
WHERE book_status.`status` != 'Deleted'
AND " . $colName . "=? limit ?,?";

            $stmt = $con->stmt_init();
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sii", $searchValue, $offSet, $pageSize);
        } else if ($searchType == 'fuz') {
            $searchValue = '%' . $searchValue . '%';
            $stmt = $con->prepare("
SELECT count(*) 
FROM book
LEFT JOIN book_status
ON book.`id`=book_status.`book_id`
WHERE book_status.`status` != 'Deleted'
AND ".$colName." like ?");
            $stmt->bind_param("s", $searchValue);
            if($stmt->execute()) {
                $stmt->bind_result($total);
                $stmt->fetch();
            } else {
                echo json_encode(array("code"=>"5", "message"=>GETBOOKSDATA["5"]));
                die();
            }
            if ($total == 0) {
                echo json_encode(array("code"=>"0", "message"=>GETBOOKSDATA["0"]));
                die();
            }


            $stmt = $con->stmt_init();
            $stmt = $con->prepare("
SELECT book.`id`, book.`book_title`,book.`author`,book.`publisher`,book.`language`,book.`category`,book.`image`, book_status.`status` 
FROM book
LEFT JOIN book_status
ON book.`id`=book_status.`book_id`
WHERE book_status.`status` != 'Deleted'
AND ".$colName.' like ? limit ?, ?');
            $stmt->bind_param("sii", $searchValue, $offSet, $pageSize);
        } else {
            die();
        }
    }
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $totalPage = ceil($total/$pageSize);
        $page = array("curPage"=>$curPage, "total"=> $total, "totalPage"=>$totalPage, "searchType"=> $searchType);
        echo json_encode(array("code" => "0", "message" => GETBOOKSDATA["0"],"page"=>$page, "data" => $data));

    } else {
        echo json_encode(array("code" => "5", "message" => GETBOOKSDATA["5"]));
        die();
    }



    $stmt->close();
    $con->close();
}