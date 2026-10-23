<?php
// get_tables.php
// ดึงรายการโต๊ะทั้งหมด (หรือเฉพาะที่ว่าง) ส่งกลับเป็น JSON
header('Content-Type: application/json');
 
require_once 'db_connect.php'; // เปลี่ยนชื่อไฟล์ connect ให้ตรงกับของพี่จริง
 
$sql = "SELECT table_id, table_number, zone, seats, status FROM restaurant_tables ORDER BY table_number ASC";
$result = $conn->query($sql); // ถ้าใช้ mysqli แบบ OOP; ถ้าใช้ PDO ต้องปรับ syntax
 
$tables = [];
while ($row = $result->fetch_assoc()) {
    $tables[] = $row;
}
 
echo json_encode($tables);