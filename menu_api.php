<?php
$allowed_roles = ['kitchen', 'admin'];
$is_page = false;
require_once 'auth_guard.php';
require_once 'db_connect.php';
header('Content-Type: application/json; charset=utf-8');
 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$action = $_GET['action'] ?? '';
$in     = json_decode(file_get_contents('php://input'), true) ?? [];

// แปลงค่าว่างจากฟอร์มให้เป็น NULL (ราคาร้อน/เย็น)
function nullableNum($v) {
    return ($v === '' || $v === null) ? null : (float)$v;
}

try {
    if ($action === 'list') {
    $res = $conn->query("SELECT * FROM menu_items WHERE is_deleted = 0 ORDER BY category, name");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
    exit();
}

    if ($action === 'toggle') {
        $id = (int)($in['menu_id'] ?? 0);
        $av = (int)($in['is_available'] ?? 0) ? 1 : 0;
        $stmt = $conn->prepare("UPDATE menu_items SET is_available = ? WHERE menu_id = ?");
        $stmt->bind_param("ii", $av, $id);
        $stmt->execute();
        echo json_encode(['success' => true]);
        exit();
    }

    if ($action === 'add' || $action === 'update') {
        $name  = trim($in['name'] ?? '');
        if ($name === '') {
            echo json_encode(['error' => 'กรุณากรอกชื่อเมนู']);
            exit();
        }
        $cat   = trim($in['category'] ?? '');
        $desc  = trim($in['description'] ?? '');
        $img   = trim($in['image_url'] ?? '');
        $price = (float)($in['price'] ?? 0);
        $hot   = nullableNum($in['price_hot'] ?? null);
        $cold  = nullableNum($in['price_cold'] ?? null);
        $av    = (int)($in['is_available'] ?? 1) ? 1 : 0;
        if (!in_array($cat, ['เครื่องดื่ม','ของหวาน','อาหาร'], true)) { echo json_encode(['error'=>'หมวดหมู่ไม่ถูกต้อง']); exit(); }
        if ($price < 0)  { echo json_encode(['error'=>'ราคาต้องไม่ติดลบ']); exit(); }
        if ($cat === 'เครื่องดื่ม' && ($hot === null || $cold === null)) { echo json_encode(['error'=>'เครื่องดื่มต้องกรอกราคาร้อนและเย็น']); exit(); }
        if ($cat !== 'เครื่องดื่ม') { $hot = $cold = null; }

        if ($action === 'add') {
            $stmt = $conn->prepare(
                "INSERT INTO menu_items (name, category, description, image_url, price, price_hot, price_cold, is_available)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssssdddi", $name, $cat, $desc, $img, $price, $hot, $cold, $av);
        } else {
            $id = (int)($in['menu_id'] ?? 0);
            $stmt = $conn->prepare(
                "UPDATE menu_items
                 SET name=?, category=?, description=?, image_url=?, price=?, price_hot=?, price_cold=?, is_available=?
                 WHERE menu_id=?"
            );
            $stmt->bind_param("ssssdddii", $name, $cat, $desc, $img, $price, $hot, $cold, $av, $id);
        }
        $stmt->execute();
        echo json_encode(['success' => true]);
        exit();
    }

    if ($action === 'delete') {
    $id = (int)($in['menu_id'] ?? 0);

    // เช็คว่าเมนูนี้เคยถูกสั่งหรือยัง
    $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM order_item WHERE menu_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $used = (int)$stmt->get_result()->fetch_assoc()['c'];

    $conn->begin_transaction();
    if ($used > 0) {
        // เคยมีออเดอร์ -> ซ่อน + ปิดขาย (เก็บประวัติออเดอร์ไว้)
        $stmt = $conn->prepare("UPDATE menu_items SET is_deleted = 1, is_available = 0 WHERE menu_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } else {
        // ไม่เคยถูกสั่ง -> ลบจริงได้ (ลบสูตรก่อน)
        $stmt = $conn->prepare("DELETE FROM menu_ingredients WHERE menu_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt = $conn->prepare("DELETE FROM menu_items WHERE menu_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    $conn->commit();
    echo json_encode(['success' => true]);
    exit();
}

    echo json_encode(['error' => 'action ไม่ถูกต้อง']);
} catch (Throwable $e) {

    try { $conn->rollback(); } catch (Throwable $_) {}
    error_log('menu_api: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'เกิดข้อผิดพลาดของเซิร์ฟเวอร์']);
}
 