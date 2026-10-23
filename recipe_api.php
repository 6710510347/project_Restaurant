<?php
$allowed_roles = ['kitchen', 'admin'];
$is_page = false;
require_once 'auth_guard.php';
require_once 'db_connect.php';
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
 
$role = $_SESSION['role'] ?? '';   // เก็บไว้ เพราะข้างล่างใช้เช็คว่าแก้สูตรได้เฉพาะ admin
 
$action = $_GET['action'] ?? $_POST['action'] ?? '';
 
if ($action === 'menus') {
    $r = $conn->query("SELECT menu_id, name, category FROM menu_items ORDER BY menu_id");
    echo json_encode($r->fetch_all(MYSQLI_ASSOC));
    exit();
}
 
if ($action === 'ingredients') {
    $r = $conn->query("SELECT ingredient_id, name, unit FROM ingredients ORDER BY name");
    echo json_encode($r->fetch_all(MYSQLI_ASSOC));
    exit();
}
 
if ($action === 'list') {
    $menuId = (int)($_GET['menu_id'] ?? 0);
    $stmt = $conn->prepare(
        "SELECT mi.ingredient_id, i.name, i.unit, mi.qty_used, mi.temp_type, mi.is_sweetener
         FROM menu_ingredients mi
         JOIN ingredients i ON i.ingredient_id = mi.ingredient_id
         WHERE mi.menu_id = ?
         ORDER BY mi.is_sweetener DESC, i.name, mi.temp_type"
    );
    $stmt->bind_param("i", $menuId);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    exit();
}
 
// ต่อจากนี้เฉพาะ admin เท่านั้น
if ($role !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'เฉพาะแอดมินเท่านั้นที่แก้สูตรได้']);
    exit();
}
 
if ($action === 'save') {
    $menuId = (int)($_POST['menu_id'] ?? 0);
    $ingId  = (int)($_POST['ingredient_id'] ?? 0);
    $qty    = (float)($_POST['qty_used'] ?? 0);
    $temp   = in_array($_POST['temp_type'] ?? '', ['all','hot','cold'], true) ? $_POST['temp_type'] : 'all';
    $sweet  = !empty($_POST['is_sweetener']) ? 1 : 0;
 
    if ($menuId <= 0 || $ingId <= 0 || $qty <= 0) {
        echo json_encode(['error' => 'กรอกข้อมูลไม่ครบ']);
        exit();
    }
    $stmt = $conn->prepare(
        "INSERT INTO menu_ingredients (menu_id, ingredient_id, qty_used, temp_type, is_sweetener)
         VALUES (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE qty_used = VALUES(qty_used), is_sweetener = VALUES(is_sweetener)"
    );
    $stmt->bind_param("iidsi", $menuId, $ingId, $qty, $temp, $sweet);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}
 
if ($action === 'delete') {
    $menuId = (int)($_POST['menu_id'] ?? 0);
    $ingId  = (int)($_POST['ingredient_id'] ?? 0);
    $temp   = in_array($_POST['temp_type'] ?? '', ['all','hot','cold'], true) ? $_POST['temp_type'] : 'all';
    $stmt = $conn->prepare("DELETE FROM menu_ingredients WHERE menu_id = ? AND ingredient_id = ? AND temp_type = ?");
    $stmt->bind_param("iis", $menuId, $ingId, $temp);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}
 
echo json_encode(['error' => 'action ไม่ถูกต้อง']);