<?php
require_once "dbcontroller.php";

function json_response($code = 200, $message = null){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Content-Type: application/json; charset=utf-8');
    
    return json_encode(array(
        "status" => $code < 300,
        "message" => $message
        ));
}

//open order API
$dbcontroller = new DBController();

if (isset($_POST['action']) && $_POST['action'] == 'OrderOpen') {
    if (isset($_POST['Login'])) {
        $order = array();
        $order['Login'] = $_POST['Login'];
        $order['Cmd'] = $_POST['Cmd'];
        $order['Symbol'] = $_POST['Symbol'];
        $order['Volume'] = $_POST['Volume'];
        $order['Price'] = $_POST['Price'];
        $order['Sl'] = $_POST['Sl'];
        $order['Tp'] = $_POST['Tp'];
        $order['Expiration'] = $_POST['Expiration'];
        $order['Comment'] = $_POST['Comment'];
        $data = json_encode($order);
        $data_encoded = $dbcontroller->escape($data);

        $store_request_query = 'INSERT INTO requests (`Manager`, `Action`, `Data`) VALUES ("1", "OrderOpen", "' . $data_encoded . '")';
        $request_id = $dbcontroller->executeQueryLastId($store_request_query);
        if ($request_id > 0) {
            $response = set_curl($request_id, $dbcontroller->url);
            
            echo json_response(200, $request_id);
        }
    } else {
        echo json_response(500, 'Fatal Error');
    }
}

//update order API
if (isset($_POST['action']) && $_POST['action'] == 'OrderUpdate') {
    if (isset($_POST['Order'])) {
        $order = array();
        $order['Order'] = $_POST['Order'];
        $order['Price'] = $_POST['Price'];
        $order['Sl'] = $_POST['Sl'];
        $order['Tp'] = $_POST['Tp'];
        $order['Expiration'] = $_POST['Expiration'];
        $order['Comment'] = $_POST['Comment'];
        $data = json_encode($order);
        $data_encoded = $dbcontroller->escape($data);

        $store_request_query = 'INSERT INTO requests (`Manager`, `Action`, `Data`) VALUES ("1", "OrderUpdate", "' . $data_encoded . '")';
        $dbcontroller = new DBController();
        $request_id = $dbcontroller->executeQueryLastId($store_request_query);
        if ($request_id > 0) {
            $response = set_curl($request_id, $dbcontroller->url);
            echo json_response(200, $request_id);
        }
    } else {
        echo json_response(500, 'Fatal Error');
    }
}

//close order API
if (isset($_POST['action']) && $_POST['action'] == 'OrderClose') {
    if (isset($_POST['Orders'])) {
        $order = array();
        $order['Orders'] = $_POST['Orders'];
        $data = json_encode($order);
        $data_encoded = $dbcontroller->escape($data);

        $store_request_query = 'INSERT INTO requests (`Manager`, `Action`, `Data`) VALUES ("1", "OrderClose", "' . $data_encoded . '")';
        $dbcontroller = new DBController();
        $request_id = $dbcontroller->executeQueryLastId($store_request_query);
        if ($request_id > 0) {
            $response = set_curl($request_id, $dbcontroller->url);
            
            echo json_response(200, $request_id);
        }
    } else {
        echo json_response(500, 'Fatal Error');
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'OpenPosition') {
    if (isset($_POST['account_number'])) {
        $query = 'SELECT * FROM trades WHERE Login = ' . $_POST['account_number'] . ' AND OpenTime >= "' . $_POST['from_time'] . '" AND CloseTime="1970-01-01 00:00:00"';
        $dbcontroller = new DBController();
        $results = $dbcontroller->executeSelectQuery($query);
        echo json_response(200, $results);
    } else {
        echo json_response(500, 'Fatal Error');
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'ClosePosition') {
    if (isset($_POST['account_number'])) {
        $query = 'SELECT * FROM trades WHERE Login = ' . $_POST['account_number'] . ' AND OpenTime >= "' . $_POST['from_time'] . '" AND CloseTime!="1970-01-01 00:00:00"';
        $dbcontroller = new DBController();
        $results = $dbcontroller->executeSelectQuery($query);
        echo json_response(200, $results);
    } else {
        echo json_response(500, 'Fatal Error');
    }
}

function set_curl($id, $url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('RequestId' => $id)));
    $response = json_decode(curl_exec($curl), true);
    curl_close($curl);

    return $response;
}
