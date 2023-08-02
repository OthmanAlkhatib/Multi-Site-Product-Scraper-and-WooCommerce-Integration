<?php
include("./connect.php");
header('Access-Control-Allow-Origin: *');

$sql_update = "UPDATE notify SET is_changed = 'true'";
$result = $con->query($sql_update);

if($result)
    echo "DONE";
else
    echo "ERROR";