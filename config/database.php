<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "huongviet";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>