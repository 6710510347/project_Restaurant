<?php
// redemptions_api.php - ให้แอดมินดูประวัติการแลกของรางวัลของสมาชิก
$allowed_roles = ['admin'];
require_once 'auth_guard.php';   // ไม่ตั้ง $is_page => ถ้าไม่มีสิทธิ์จะตอบ JSON 403
require_once 'db_connect.php';

header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT r.id, r.member_id, u.name, u.phone,
               r.reward_name, r.points_used, r.created_at,
               r.used_order_id, r.used_at, o.discount_amount AS used_discount,
               (r.used_order_id IS NOT NULL) AS is_used
        FROM redemptions r
        LEFT JOIN users u ON u.user_id = r.member_id
        LEFT JOIN orders o ON o.order_id = r.used_order_id
        ORDER BY r.created_at DESC, r.id DESC";

$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'query failed']);
    exit();
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
echo json_encode(['success' => true, 'data' => $rows]);