<?php

// update_item_status.php
// รับ order_item_id + สถานะใหม่ แล้วอัปเดตลงฐานข้อมูล
$allowed_roles = ['kitchen', 'admin'];
$is_page = false;

require_once 'auth_guard.php';
header('Content-Type: application/json; charset=utf-8');
require_once 'db_connect.php';
 
$data = json_decode(file_get_contents('php://input'), true);
$orderItemId = $data['order_item_id'] ?? null;
$newStatus   = $data['new_status'] ?? null;
 
// สถานะที่ยอมให้ตั้งได้เท่านั้น (กันคนส่งค่าแปลกๆ มาแก้ฐานข้อมูล)
$allowedStatus = ['pending', 'preparing', 'ready', 'completed', 'cancelled'];
 
if (!$orderItemId || !in_array($newStatus, $allowedStatus)) {
    echo json_encode(['error' => 'ข้อมูลไม่ถูกต้อง']);
    exit();
}
 
$stmt = $conn->prepare("UPDATE order_item SET cook_status = ? WHERE order_item_id = ?");
$stmt->bind_param("si", $newStatus, $orderItemId);
$stmt->execute();
 
if ($stmt->affected_rows === 0) {
    echo json_encode(['error' => 'ไม่พบรายการนี้ หรือสถานะไม่เปลี่ยนแปลง']);
    exit();
}
 
echo json_encode(['success' => true]);
 