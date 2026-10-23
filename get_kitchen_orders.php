<?php

// get_kitchen_orders.php

// ดึงรายการอาหารที่ครัวต้องทำ (สถานะ pending หรือ cooking เท่านั้น)

$allowed_roles = ['kitchen'];
require_once 'auth_guard.php';

header('Content-Type: application/json; charset=utf-8');
require_once 'db_connect.php';
 
$sql = "SELECT 
            oi.order_item_id,
            oi.order_id,
            oi.menu_id,
            oi.qty,
            oi.cook_status,
            m.name AS menu_name,
            o.created_at,
            o.order_type,
            rt.table_number
        FROM order_item oi
        JOIN orders o        ON oi.order_id = o.order_id
        JOIN menu_items m    ON oi.menu_id = m.menu_id
        LEFT JOIN restaurant_tables rt ON o.table_id = rt.table_id
        WHERE oi.cook_status IN ('pending', 'preparing')
        ORDER BY o.created_at ASC";

$result = $conn->query($sql); 
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
 