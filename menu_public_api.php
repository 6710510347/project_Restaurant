<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'db_connect.php';

$sql = "SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, name";
$result = $conn->query($sql);

$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}

echo json_encode($menu);