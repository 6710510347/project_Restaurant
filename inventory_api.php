<?php
$allowed_roles = ['kitchen', 'admin'];
$is_page = false;
require_once 'auth_guard.php';
require_once 'db_connect.php';
header('Content-Type: application/json; charset=utf-8');
 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
 
$action = $_GET['action'] ?? '';
$in = json_decode(file_get_contents('php://input'), true) ?? [];
 
function fail(string $msg): void {
    echo json_encode(['error' => $msg]);
    exit();
}

function ok(array $extra = []): void {
    echo json_encode(['success' => true] + $extra);
    exit();
}
 
try {
    switch ($action) {
 
        // ---------- วัตถุดิบ ----------
        case 'list_ingredients':
            $res = $conn->query("SELECT ingredient_id, name, unit, stock_qty, low_stock_threshold
                FROM ingredients ORDER BY ingredient_id");
            echo json_encode($res->fetch_all(MYSQLI_ASSOC));
            exit();
 
        case 'add_ingredient':
            $name = trim($in['name'] ?? '');
            $unit = trim($in['unit'] ?? '');
            $stock = (float)($in['stock_qty'] ?? 0);
            $low = (float)($in['low_stock_threshold'] ?? 0);
            if ($name === '' || $unit === '') fail('กรุณากรอกชื่อและหน่วย');
            if ($stock < 0 || $low < 0) fail('ค่าต้องไม่ติดลบ');
            $st = $conn->prepare("INSERT INTO ingredients (name, unit, stock_qty, low_stock_threshold) VALUES (?, ?, ?, ?)");
            $st->bind_param("ssdd", $name, $unit, $stock, $low);
            $st->execute();
            ok(['id' => $st->insert_id]);
 
        case 'update_ingredient':
            $id = (int)($in['ingredient_id'] ?? 0);
            $name = trim($in['name'] ?? '');
            $unit = trim($in['unit'] ?? '');
            $stock = (float)($in['stock_qty'] ?? 0);
            $low = (float)($in['low_stock_threshold'] ?? 0);

            if (!$id || $name === '' || $unit === '') fail('ข้อมูลไม่ครบ');
            if ($stock < 0 || $low < 0) fail('ค่าต้องไม่ติดลบ');

            $st = $conn->prepare("UPDATE ingredients SET name=?, unit=?, stock_qty=?, low_stock_threshold=? WHERE ingredient_id=?");
            $st->bind_param("ssddi", $name, $unit, $stock, $low, $id);
            $st->execute();
            ok();
 
        case 'adjust_stock':
            $id = (int)($in['ingredient_id'] ?? 0);
            $delta = (float)($in['delta'] ?? 0);

            if (!$id || $delta == 0) fail('กรุณาระบุจำนวนที่ต้องการเพิ่ม/ลด');

            $st = $conn->prepare("UPDATE ingredients SET stock_qty = stock_qty + ?
                WHERE ingredient_id = ? AND stock_qty + ? >= 0");

            $st->bind_param("did", $delta, $id, $delta);
            $st->execute();

            if ($st->affected_rows === 0) fail('ปรับไม่ได้ (สต็อกจะติดลบ หรือไม่พบวัตถุดิบ)');
            ok();
 
        case 'delete_ingredient':
            $id = (int)($in['ingredient_id'] ?? 0);
            if (!$id) fail('ข้อมูลไม่ครบ');

            $st = $conn->prepare("DELETE FROM ingredients WHERE ingredient_id = ?");
            $st->bind_param("i", $id);
            $st->execute();
            ok();
 
        // ---------- สูตรเมนู ----------

        case 'list_menus':
            $res = $conn->query("SELECT menu_id, name, category FROM menu_items WHERE is_deleted = 0 ORDER BY menu_id");
            echo json_encode($res->fetch_all(MYSQLI_ASSOC));
            exit();
        case 'get_recipe':
            $menuId = (int)($_GET['menu_id'] ?? 0);
            $st = $conn->prepare(
                "SELECT mi.ingredient_id, i.name, i.unit, mi.qty_used, mi.temp_type, mi.is_sweetener
                 FROM menu_ingredients mi
                 JOIN ingredients i ON i.ingredient_id = mi.ingredient_id
                 WHERE mi.menu_id = ?
                 ORDER BY FIELD(mi.temp_type,'all','hot','cold'), mi.is_sweetener, i.name"
            );

            $st->bind_param("i", $menuId);
            $st->execute();
            echo json_encode($st->get_result()->fetch_all(MYSQLI_ASSOC));
            exit();
 
        case 'add_recipe':
            $menuId = (int)($in['menu_id'] ?? 0);
            $ingId  = (int)($in['ingredient_id'] ?? 0);
            $qty    = (float)($in['qty_used'] ?? 0);
            $temp   = in_array($in['temp_type'] ?? '', ['all', 'hot', 'cold'], true) ? $in['temp_type'] : 'all';
            $sweet  = !empty($in['is_sweetener']) ? 1 : 0;

            if (!$menuId || !$ingId || $qty <= 0) fail('กรุณาเลือกวัตถุดิบและใส่ปริมาณที่มากกว่า 0');

            $st = $conn->prepare("INSERT INTO menu_ingredients (menu_id, ingredient_id, qty_used, temp_type, is_sweetener) VALUES (?, ?, ?, ?, ?)");
            $st->bind_param("iidsi", $menuId, $ingId, $qty, $temp, $sweet);
            $st->execute();
            ok();
 
        case 'update_recipe':
            $menuId = (int)($in['menu_id'] ?? 0);
            $ingId  = (int)($in['ingredient_id'] ?? 0);
            $qty    = (float)($in['qty_used'] ?? 0);
            $temp = in_array($in['temp_type'] ?? '', ['all','hot','cold'], true) ? $in['temp_type'] : 'all';
            $sweet  = !empty($in['is_sweetener']) ? 1 : 0;

            if (!$menuId || !$ingId || $qty <= 0) fail('ปริมาณต้องมากกว่า 0');
            $st = $conn->prepare("UPDATE menu_ingredients SET qty_used = ?
                WHERE menu_id = ? AND ingredient_id = ? AND temp_type = ? AND is_sweetener = ?");
            $st->bind_param("diisi", $qty, $menuId, $ingId, $temp, $sweet);
            $st->execute();
            ok();
 
        case 'delete_recipe':
            $menuId = (int)($in['menu_id'] ?? 0);
            $ingId  = (int)($in['ingredient_id'] ?? 0);
            $temp = in_array($in['temp_type'] ?? '', ['all','hot','cold'], true) ? $in['temp_type'] : 'all';
            $sweet  = !empty($in['is_sweetener']) ? 1 : 0;
            $st = $conn->prepare("DELETE FROM menu_ingredients
                WHERE menu_id = ? AND ingredient_id = ? AND temp_type = ? AND is_sweetener = ?");
            $st->bind_param("iisi", $menuId, $ingId, $temp, $sweet);
            $st->execute();
            ok();
 
        default:
            fail('ไม่รู้จัก action นี้');
    }

} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1451) fail('ลบไม่ได้ เพราะวัตถุดิบนี้ถูกใช้อยู่ในสูตรเมนู กรุณาลบออกจากสูตรก่อน');
    if ($e->getCode() === 1062) fail('มีวัตถุดิบนี้ในสูตร (ประเภท/ความหวานเดียวกัน) อยู่แล้ว');
    error_log('inventory_api: ' . $e->getMessage());
    fail('เกิดข้อผิดพลาดของฐานข้อมูล');
}
 