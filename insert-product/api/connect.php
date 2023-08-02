<?php
// Create Connection
require_once __DIR__."./../../../../wp-config.php";
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
// Check Connection
if (!$con) {
    die("Connecting to Database Has Been Failed" . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8");

?>