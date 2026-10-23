<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'db_connect.php';

ini_set('display_errors', '0');   // กัน PHP พ่น HTML error ปนกับ JSON
error_reporting(E_ALL);

// error ที่ตั้งใจให้ลูกค้า/พนักงานเห็นข้อความ (คูปอง, วัตถุดิบไม่พอ ฯลฯ)
class UserError extends Exception {}
 
function deductStock(mysqli $conn, array $items): void {
    $multMap = ['less' => 0.5, 'normal' => 1, 'extra' => 1.5];
    $sel = $conn->prepare("SELECT ingredient_id, qty_used, is_sweetener
                           FROM menu_ingredients
                           WHERE menu_id = ? AND temp_type IN ('all', ?)");
    $upd = $conn->prepare("UPDATE ingredients
                           SET stock_qty = stock_qty - ?
                           WHERE ingredient_id = ? AND stock_qty >= ?");
    foreach ($items as $it) {
        $menuId = (int)$it['menu_id'];
        $qty    = max(1, (int)($it['qty'] ?? 1));
        $temp   = in_array($it['temperature'] ?? null, ['hot','cold'], true) ? $it['temperature'] : 'all';
        $mult   = $multMap[$it['sweetness'] ?? ''] ?? 1;
        $sel->bind_param("is", $menuId, $temp);
        $sel->execute();
        $rows = $sel->get_result()->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $r) {
            $deduct = $r['qty_used'] * ($r['is_sweetener'] ? $mult : 1) * $qty;
            $upd->bind_param("did", $deduct, $r['ingredient_id'], $deduct);
            $upd->execute();
            if ($upd->affected_rows === 0) {
                throw new UserError("วัตถุดิบไม่พอ (ingredient_id {$r['ingredient_id']})");
            }
        }
    }
}
 
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ===== เลือก session ให้ถูก role (ใช้ชุดเดียวกับ auth_guard.php / session_boot.php) =====
// - ฝั่งครัว/แอดมิน: ลอง session ของ kitchen ก่อน แล้ว admin (ถ้าไม่เจอค่อยถอยไปใช้ session เดิม)
// - ฝั่งลูกค้าสั่งอาหาร: ใช้ session ของ customer เท่านั้น ไม่งั้นออเดอร์ไปผูกกับบัญชีครัว/แอดมินที่ล็อกอินค้างไว้
require_once 'session_boot.php';
function boot_session(array $groups, bool $legacyFallback = false): void {
    foreach ($groups as $g) {
        start_role_session($g);
        if (($_SESSION['role'] ?? null) === $g) return;
    }
    if ($legacyFallback) {
        if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
        session_name('PHPSESSID');
        session_start();
    }
}
if (in_array($action, ['list', 'detail', 'update_status'], true)) {
    boot_session(['kitchen', 'admin'], true);
} else {
    boot_session(['customer']);
}
 
// ========== ส่วนของหน้า Admin จัดการออเดอร์ ==========
if ($action === 'list') {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'kitchen'])) {
        http_response_code(403);
        echo json_encode(['error' => 'ไม่มีสิทธิ์เข้าถึง']);
        exit();
    }
 
    $status = $_GET['status'] ?? '';
 
    $sql = "SELECT o.order_id AS id, o.order_type, o.pickup_time,
                   o.created_at AS order_date, o.status,
                   o.guest_name, o.discount_amount, r.reward_name AS coupon_name,
                   GREATEST(COALESCE(SUM(oi.qty * oi.unit_price), 0) - o.discount_amount, 0) AS total_price
            FROM orders o
            LEFT JOIN order_item oi ON oi.order_id = o.order_id
            LEFT JOIN redemptions r ON r.id = o.redemption_id";
    if ($status !== '') {
        $sql .= " WHERE o.status = ?";
    }
    $sql .= " GROUP BY o.order_id, o.discount_amount, r.reward_name ORDER BY o.created_at DESC";
 
    $stmt = $conn->prepare($sql);
    if ($status !== '') {
        $stmt->bind_param("s", $status);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode($orders);
    exit();
}
 
if ($action === 'detail') {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'kitchen'])) {
        http_response_code(403);
        echo json_encode(['error' => 'ไม่มีสิทธิ์เข้าถึง']);
        exit();
    }
 
    $orderId = (int)($_GET['id'] ?? 0);
 
    $stmt = $conn->prepare(
        "SELECT o.order_id AS id, o.order_type, o.pickup_time, o.created_at AS order_date,
             o.status, o.guest_name, o.discount_amount, r.reward_name AS coupon_name,
             rw.type AS coupon_type, rt.table_number, rt.zone
      FROM orders o
      LEFT JOIN restaurant_tables rt ON rt.table_id = o.table_id
      LEFT JOIN redemptions r ON r.id = o.redemption_id
      LEFT JOIN rewards rw ON rw.id = r.reward_id
      WHERE o.order_id = ?"
    );
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
 
    if (!$order) {
        echo json_encode(['error' => 'ไม่พบออเดอร์นี้']);
        exit();
    }
 
    $stmt = $conn->prepare(
        "SELECT m.name AS menu_name, oi.qty AS quantity, oi.unit_price AS price_at_order,
           oi.temperature, oi.sweetness
         FROM order_item oi
         JOIN menu_items m ON m.menu_id = oi.menu_id
         WHERE oi.order_id = ?"
    );
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
 
    $items = [];
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
        $total += $row['quantity'] * $row['price_at_order'];
    }
    $order['items'] = $items;
    $order['subtotal'] = $total;   // ยอดรวมก่อนหักส่วนลด
    $order['total_price'] = max($total - (float)$order['discount_amount'], 0);   // ยอดสุทธิ
 
    echo json_encode($order);
    exit();
}
 
if ($action === 'update_status') {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'kitchen'])) {
        http_response_code(403);
        echo json_encode(['error' => 'ไม่มีสิทธิ์เข้าถึง']);
        exit();
    }
 
    $orderId   = (int)($_POST['id'] ?? 0);
    $newStatus = $_POST['status'] ?? '';
 
    if (!in_array($newStatus, ['pending', 'preparing', 'ready', 'completed', 'cancelled'])) {
        echo json_encode(['error' => 'สถานะไม่ถูกต้อง']);
        exit();
    }

    // ยกเลิกออเดอร์ได้เฉพาะครัวเท่านั้น (แอดมินยกเลิกไม่ได้)
    if ($newStatus === 'cancelled' && $_SESSION['role'] !== 'kitchen') {
        http_response_code(403);
        echo json_encode(['error' => 'เฉพาะครัวเท่านั้นที่ยกเลิกออเดอร์ได้']);
        exit();
    }

    date_default_timezone_set('Asia/Bangkok');
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn->begin_transaction();
    try {
        // ล็อกแถวออเดอร์ กันกดซ้ำพร้อมกันแล้วตัดสต็อก 2 รอบ
        $stmt = $conn->prepare("SELECT status, redemption_id FROM orders WHERE order_id = ? FOR UPDATE");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $cur = $stmt->get_result()->fetch_assoc();
        if (!$cur) {
            $conn->rollback();
            echo json_encode(['error' => 'ไม่พบออเดอร์นี้']);
            exit();
        }

        // ออเดอร์ที่ยกเลิกหรือเสร็จสิ้นแล้ว ห้ามเปลี่ยนสถานะอีก
        if (in_array($cur['status'], ['cancelled', 'completed'], true)) {
            $conn->rollback();
            echo json_encode(['error' => 'ออเดอร์นี้ปิดแล้ว เปลี่ยนสถานะไม่ได้']);
            exit();
        }

        $allowed = ['pending'=>['preparing','cancelled'], 'preparing'=>['ready'], 'ready'=>['completed']];
        if (!in_array($newStatus, $allowed[$cur['status']] ?? [], true)) {
        $conn->rollback();
        echo json_encode(['error' => 'เปลี่ยนสถานะข้ามขั้นหรือย้อนกลับไม่ได้']);
        exit();
    }

        // ยกเลิก -> คืนคูปองที่ลูกค้าใช้ในออเดอร์นี้ (คูปองกลับมาใช้ได้ใหม่ ไม่คืนแต้มเพราะแต้มถูกหักไปตอนแลก)
        if ($newStatus === 'cancelled' && !empty($cur['redemption_id'])) {
            $rid = (int)$cur['redemption_id'];
            $stmt = $conn->prepare(
                "UPDATE redemptions SET used_order_id = NULL, used_at = NULL
                 WHERE id = ? AND used_order_id = ?"
            );
            $stmt->bind_param("ii", $rid, $orderId);
            $stmt->execute();
            $stmt = $conn->prepare(
                "UPDATE orders SET redemption_id = NULL, discount_amount = 0 WHERE order_id = ?"
            );
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
        }
 
        // ตัดสต็อก "ครั้งเดียว" ตอนออเดอร์ออกจาก pending ไปสถานะที่ไม่ใช่ยกเลิก
        // (ปกติคือกด "เริ่มทำ" = preparing)
        if ($cur['status'] === 'pending' && in_array($newStatus, ['preparing', 'ready', 'completed'])) {
            $stmt = $conn->prepare(
                "SELECT menu_id, qty, temperature, sweetness FROM order_item WHERE order_id = ?"
            );
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            deductStock($conn, $items);
        }
 
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $newStatus, $orderId);
        $stmt->execute();
 
        // ===== ให้แต้มสะสม: ยอด >= 100 บาท ได้ 10 แต้ม, ทุก 50 บาทที่เกิน 100 ได้เพิ่ม 5 แต้ม =====
        $minSpend = 100;   // ยอดขั้นต่ำ (บาท)
        $baseP    = 10;    // แต้มเมื่อยอดถึงขั้นต่ำ
        $stepBaht = 50;    // ทุก ๆ กี่บาทที่เกินขั้นต่ำ
        $stepP    = 5;     // ได้เพิ่มกี่แต้ม

        if ($newStatus === 'completed' && $cur['status'] !== 'completed') {
            $stmt = $conn->prepare(
                "SELECT o.customer_id,
                        GREATEST(COALESCE(SUM(oi.qty * oi.unit_price), 0) - o.discount_amount, 0) AS total
                 FROM orders o
                 LEFT JOIN order_item oi ON oi.order_id = o.order_id
                 WHERE o.order_id = ?
                 GROUP BY o.order_id, o.customer_id, o.discount_amount"
            );
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $o = $stmt->get_result()->fetch_assoc();

            if ($o && !empty($o['customer_id']) && (float)$o['total'] >= $minSpend) {
                // กันให้แต้มซ้ำ ถ้าออเดอร์นี้เคยได้แต้มแล้ว
                $stmt = $conn->prepare("SELECT 1 FROM points WHERE order_id = ? AND points_amount > 0 LIMIT 1");
                $stmt->bind_param("i", $orderId);
                $stmt->execute();
                if (!$stmt->get_result()->fetch_row()) {
                    $total  = (int)floor((float)$o['total']);
                    $earned = $baseP + intdiv($total - $minSpend, $stepBaht) * $stepP;
                    $cid    = (int)$o['customer_id'];
                    $reason = 'ได้รับแต้มจากออเดอร์ #' . $orderId;

                    $stmt = $conn->prepare(
                        "INSERT INTO points (customer_id, order_id, points_amount, reason) VALUES (?, ?, ?, ?)"
                    );
                    $stmt->bind_param("iiis", $cid, $orderId, $earned, $reason);
                    $stmt->execute();
                }
            }
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (UserError $e) {
        $conn->rollback();
        echo json_encode(['error' => $e->getMessage()]);
    } catch (Throwable $e) {
        $conn->rollback();
        error_log('order_api: ' . $e->getMessage());
        echo json_encode(['error' => 'เกิดข้อผิดพลาดของเซิร์ฟเวอร์']);
    }
    exit();
}

// ========== ส่วนเดิม: ลูกค้าสั่งอาหาร (ไม่มี action หรือเรียกแบบ JSON body) ยังอยู่ต่อจากนี้เหมือนเดิมทุกอย่าง ==========
// ต้อง login ก่อนถึงจะสั่งได้
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'customer') !== 'customer') {
    echo json_encode(['error' => 'กรุณาเข้าสู่ระบบด้วยบัญชีลูกค้าก่อนสั่งอาหาร']);
    exit();
}
// รับข้อมูล JSON ที่ cart.js ส่งมา แล้วแปลงเป็น array ของ PHP
$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'] ?? [];
if (empty($cart)) {
    echo json_encode(['error' => 'ตะกร้าว่างอยู่']);
    exit();
}
$customerId = $_SESSION['user_id'];
$orderType  = $data['order_type'] ?? 'dine_in_walkin';
$pickupTime = !empty($data['pickup_time']) ? $data['pickup_time'] : null;
$tableId    = !empty($data['table_id']) ? (int)$data['table_id'] : null; 
$customerName = !empty($data['customer_name']) ? trim($data['customer_name']) : null;
 
date_default_timezone_set('Asia/Bangkok');
$redemptionId = !empty($data['redemption_id']) ? (int)$data['redemption_id'] : null;

$priceStmt = $conn->prepare("SELECT price, price_hot, price_cold, category, is_available FROM menu_items WHERE menu_id = ?");
$lines = [];
 
foreach ($cart as $item) { 
    $menuId = (int)$item['menu_id'];    
    $qty    = max(1, (int)$item['qty']);
 
    $priceStmt->bind_param("i", $menuId);    
    $priceStmt->execute();    
    $row = $priceStmt->get_result()->fetch_assoc();
 
    if (!$row) {
        echo json_encode(['error' => 'ไม่พบเมนูบางรายการ']);
        exit();
    }
    if (!(int)$row['is_available']) {
        echo json_encode(['error' => 'มีเมนูในตะกร้าที่ปิดขายแล้ว กรุณาลบออกก่อนสั่ง']);
        exit();
    }
 
    $isDrink = $row['category'] === 'เครื่องดื่ม';
    $temp  = ($isDrink && in_array($item['temperature'] ?? null, ['hot','cold'], true)) ? $item['temperature'] : null;
    $sweet = ($isDrink && in_array($item['sweetness'] ?? null, ['less','normal','extra'], true)) ? $item['sweetness'] : null;
    $unitPrice = $temp === 'hot' ? $row['price_hot'] : ($temp === 'cold' ? $row['price_cold'] : $row['price']);
    if ($unitPrice === null) { echo json_encode(['error' => 'เมนูนี้ยังไม่ได้ตั้งราคา']); exit(); }
    $lines[] = [$menuId, $qty, $unitPrice, $temp, $sweet, $row['category']];
}

if (!in_array($orderType, ['dine_in_walkin', 'takeaway', 'pre_order'])) {
    echo json_encode(['error' => 'ประเภทการสั่งไม่ถูกต้อง']);    
    exit();
}
if ($orderType !== 'dine_in_walkin') $tableId = null;
if ($orderType !== 'pre_order')      $pickupTime = null;
if ($pickupTime && (($ts = strtotime($pickupTime)) === false || $ts < time())) {
    echo json_encode(['error' => 'เวลารับไม่ถูกต้อง']); exit();
}
 
 
if ($orderType === 'pre_order' && !$pickupTime) {    
    echo json_encode(['error' => 'กรุณาเลือกเวลารับ']);    
    exit();
}
 
if ($orderType === 'dine_in_walkin' && !$tableId) {
    echo json_encode(['error' => 'กรุณาเลือกหมายเลขโต๊ะ']);
    exit();
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->begin_transaction();
try {
    // ===== คำนวณยอดรวม และส่วนลดจากคูปอง (ถ้าลูกค้าเลือกใช้) =====
    $subtotal = 0.0;
    foreach ($lines as [$mId, $q, $uPrice]) {
        $subtotal += $q * (float)$uPrice;
    }

    $discount = 0.0;
    if ($redemptionId) {
        // ล็อกคูปอง กันใช้ซ้ำพร้อมกัน และต้องเป็นของลูกค้าคนนี้ที่ยังไม่ถูกใช้
        $stmt = $conn->prepare(
            "SELECT r.id, rw.type, rw.value
             FROM redemptions r
             JOIN rewards rw ON rw.id = r.reward_id
             WHERE r.id = ? AND r.member_id = ? AND r.used_at IS NULL AND r.used_order_id IS NULL
             FOR UPDATE"
        );
        $stmt->bind_param("ii", $redemptionId, $customerId);
        $stmt->execute();
        $coupon = $stmt->get_result()->fetch_assoc();
        if (!$coupon) {
            throw new UserError('คูปองนี้ใช้ไม่ได้แล้ว หรือไม่ใช่ของคุณ');
        }

        switch ($coupon['type']) {
            case 'percent':
                $discount = round($subtotal * ((int)$coupon['value']) / 100, 2);
                break;
            case 'fixed':
                $discount = min((float)$coupon['value'], $subtotal);
                break;
            case 'free_item':
                // ลดราคาเครื่องดื่ม 1 แก้ว (แก้วที่ราคาสูงสุดในตะกร้า) เหลือ 0 บาท
                $best = 0.0;
                foreach ($lines as [$mId, $q, $uPrice, $t, $s, $cat]) {
                    if ($cat === 'เครื่องดื่ม' && (float)$uPrice > $best) $best = (float)$uPrice;
                }
                if ($best <= 0) {
                    throw new UserError('คูปองเครื่องดื่มฟรีใช้ได้เมื่อมีเครื่องดื่มอย่างน้อย 1 แก้วในออเดอร์');
                }
                $discount = $best;
                break;
            case 'gift':
            default:
                $discount = 0.0;   // ของแถม พนักงานหยิบให้ ไม่เกี่ยวกับราคา
        }
        $discount = min($discount, $subtotal);
    }

    // สร้างออเดอร์หลัก
    $stmt = $conn->prepare(
        "INSERT INTO orders (customer_id, table_id, order_type, order_channel, payment_status, pickup_time, guest_name, discount_amount, redemption_id)
         VALUES (?, ?, ?, 'web_app', 'unpaid', ?, ?, ?, ?)"
    );
    $stmt->bind_param("iisssdi", $customerId, $tableId, $orderType, $pickupTime, $customerName, $discount, $redemptionId);
    $stmt->execute();
    $orderId = $stmt->insert_id;

    // ตัดคูปอง = ผูกกับออเดอร์นี้ + บันทึกเวลาใช้
    if ($redemptionId) {
        $usedAt = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("UPDATE redemptions SET used_order_id = ?, used_at = ? WHERE id = ? AND used_at IS NULL");
        $stmt->bind_param("isi", $orderId, $usedAt, $redemptionId);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            throw new UserError('ใช้คูปองไม่สำเร็จ กรุณาลองใหม่');
        }
    }

    // เพิ่มรายการลง order_item
    $stmt = $conn->prepare(
        "INSERT INTO order_item (order_id, menu_id, qty, unit_price, temperature, sweetness) VALUES (?, ?, ?, ?, ?, ?)"
    );
    foreach ($lines as [$menuId, $qty, $unitPrice, $temperature, $sweetness]) {
        $stmt->bind_param("iiidss", $orderId, $menuId, $qty, $unitPrice, $temperature, $sweetness);
        $stmt->execute();
    }
    // (ไม่ตัดสต็อกตรงนี้แล้ว — จะตัดตอนแอดมิน/ครัวกด "เริ่มทำ" ใน action=update_status)
 
    $conn->commit();
    echo json_encode([
        'success'  => true,
        'order_id' => $orderId,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'total'    => $subtotal - $discount,
    ]);

} catch (UserError $e) {
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
} catch (Throwable $e) {
    $conn->rollback();
    error_log('order_api: ' . $e->getMessage());
    echo json_encode(['error' => 'เกิดข้อผิดพลาดของเซิร์ฟเวอร์']);
}