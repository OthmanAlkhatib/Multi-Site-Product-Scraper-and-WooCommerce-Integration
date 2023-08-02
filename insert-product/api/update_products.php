<?php
require_once "./connect.php";
header('Access-Control-Allow-Origin: *');
header("Content-Type:application/json; charset=utf-8");

try{
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    
    $con -> query("SET autocommit = 0; START TRANSACTION;");
    
    foreach($data as $product){
        $new_price = $product["new_price"];
        $product_id = $product["id"];
        $sql_update = "UPDATE products SET actual_price='$new_price' where id='$product_id';";
        $con -> query($sql_update);
    }
    
    $commit = $con -> query("COMMIT;");
    
    $sql_update_not = "UPDATE notify SET is_changed = 'false'";
    if($commit) {
        $result = $con->query($sql_update_not);
        $response['status']=200;
        $response['message']="Products Updated Successfully";
        http_response_code(200);
        echo json_encode($response);
        exit;
    }
    else {
        $response['status']=422;
        $response['message']='Error while updating products';
        http_response_code(422);
        echo json_encode($response);
        exit;
    }
}
catch(Exception $e) {
    $response['status']=500;
    $response['error']= 'Server error';
    http_response_code(500);
    echo json_encode($response);
    exit; 
}