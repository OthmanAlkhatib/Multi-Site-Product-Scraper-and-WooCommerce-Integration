<?php
include("./connect.php");
header('Access-Control-Allow-Origin: *');

$sql_select = "SELECT * FROM `products`";
$result = $con->query($sql_select);
$sql_data = array();

While($row = $result->fetch_assoc()) {
    $sql_data[] = ['product_url'=>$row['product_url'], 'actual_price'=>$row['actual_price']];
}
echo json_encode($sql_data);
?>