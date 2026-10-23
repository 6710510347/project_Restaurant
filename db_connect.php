<?php

$db_host = "localhost";
$db_user = "root";
$bd_pass = "";
$db_name = "project";

$conn = new mysqli($db_host,$db_user,$bd_pass,$db_name);

if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>