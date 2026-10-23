<?php
/**
 * dashboard_api.php — ตัวเลขสรุปของวันนี้สำหรับหน้า admin.php (เวอร์ชัน mysqli)
 *  ตาราง orders ไม่มียอดรวม จึงคำนวณจาก SUM(qty * unit_price)
 */
$allowed_roles = ['admin'];
$is_page = false;
require_once 'auth_guard.php';
require_once 'db_connect.php';

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Bangkok');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

// true  = ยอดขายนับเฉพาะออเดอร์ที่ชำระเงินแล้ว (payment_status = 'paid')
// false = นับทุกออเดอร์ที่ไม่ถูกยกเลิก
$REVENUE_ONLY_PAID = false;

try {
    $start = date('Y-m-d 00:00:00');
    $end   = date('Y-m-d 00:00:00', strtotime('+1 day'));

    // จำนวนออเดอร์ / ออเดอร์ที่เสร็จสิ้น
    $s = $conn->prepare(
        "SELECT COALESCE(SUM(status <> 'cancelled'), 0) AS orders,
                COALESCE(SUM(status = 'completed'), 0)  AS completed
         FROM orders
         WHERE created_at >= ? AND created_at < ?"
    );
    $s->bind_param('ss', $start, $end);
    $s->execute();
    $cnt = $s->get_result()->fetch_assoc();

    // ยอดขาย
    $sql = "SELECT COALESCE(SUM(GREATEST(t.sub - t.disc, 0)), 0) FROM (
            SELECT o.order_id,
                   SUM(oi.qty * oi.unit_price) AS sub,
                   COALESCE(o.discount_amount, 0) AS disc
            FROM orders o
            JOIN order_item oi ON oi.order_id = o.order_id
            WHERE o.created_at >= ? AND o.created_at < ?
              AND o.status <> 'cancelled'" .
            ($REVENUE_ONLY_PAID ? " AND o.payment_status = 'paid'" : "") . "
            GROUP BY o.order_id
        ) t";
 
    if ($REVENUE_ONLY_PAID) $sql .= " AND o.payment_status = 'paid'";
    $s = $conn->prepare($sql);
    $s->bind_param('ss', $start, $end);
    $s->execute();
    $revenue = (float)$s->get_result()->fetch_row()[0];

    echo json_encode([
        'success' => true,
        'data' => [
            'revenue'   => $revenue,
            'orders'    => (int)$cnt['orders'],
            'completed' => (int)$cnt['completed'],
        ],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('dashboard_api: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}