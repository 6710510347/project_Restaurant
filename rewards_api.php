<?php
/**
 * rewards_api.php — API จัดการของรางวัล (เฉพาะแอดมิน) เวอร์ชัน mysqli
 * ตาราง `rewards`: id, name, points_required, stock, is_active
 */

$allowed_roles = ['admin'];
$is_page = false;
require_once 'auth_guard.php';
require_once 'db_connect.php';

header('Content-Type: application/json; charset=utf-8');

// ให้ mysqli โยน exception เวลา SQL ผิด (จะได้เข้า catch)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

function respond($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

if (in_array($action, ['save', 'toggle', 'delete'], true) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    switch ($action) {
        case 'list':
            $result = $conn->query(
                "SELECT id, name, points_required AS points, stock, is_active AS active, type, value
                 FROM rewards ORDER BY id DESC"
            );
            respond(['success' => true, 'data' => $result->fetch_all(MYSQLI_ASSOC)]);
            break;

        case 'save':
            $id     = (int)($_POST['id'] ?? 0);
            $name   = trim($_POST['name'] ?? '');
            $points = filter_var($_POST['points'] ?? null, FILTER_VALIDATE_INT);
            $stock  = filter_var($_POST['stock'] ?? -1, FILTER_VALIDATE_INT);
            $type   = $_POST['type'] ?? 'gift';
            $value  = filter_var($_POST['value'] ?? 0, FILTER_VALIDATE_INT);
            $hasType = isset($_POST['type']);   // หน้าเว็บส่ง type มาด้วยหรือไม่

            if (!in_array($type, ['percent', 'fixed', 'free_item', 'gift'], true))
                respond(['success' => false, 'message' => 'ประเภทของรางวัลไม่ถูกต้อง'], 422);
            if ($value === false || $value < 0 || ($type === 'percent' && ($value < 1 || $value > 100))) 
                respond(['success' => false, 'message' => 'ค่าส่วนลดไม่ถูกต้อง'], 422);
            if (in_array($type, ['free_item', 'gift'], true)) $value = 0;
            
            $active = !empty($_POST['active']) && $_POST['active'] !== '0' ? 1 : 0;

            if ($name === '' || mb_strlen($name) > 100) {
                respond(['success' => false, 'message' => 'กรุณากรอกชื่อของรางวัล (ไม่เกิน 100 ตัวอักษร)'], 422);
            }
            if ($points === false || $points < 0) {
                respond(['success' => false, 'message' => 'แต้มที่ต้องใช้ต้องเป็นจำนวนเต็มตั้งแต่ 0 ขึ้นไป'], 422);
            }
            if ($stock === false || $stock < -1) {
                respond(['success' => false, 'message' => 'สต็อกต้องเป็น -1 (ไม่จำกัด) หรือ 0 ขึ้นไป'], 422);
            }

            if ($id > 0) {
                if ($hasType) {
                    $stmt = $conn->prepare(
                        "UPDATE rewards SET name = ?, points_required = ?, stock = ?, is_active = ?, type = ?, value = ? WHERE id = ?"
                    );
                    $stmt->bind_param('siiisii', $name, $points, $stock, $active, $type, $value, $id);
                } else {
                    // ไม่ได้ส่ง type/value มา -> แก้เฉพาะช่องอื่น คง type/value เดิมไว้
                    $stmt = $conn->prepare(
                        "UPDATE rewards SET name = ?, points_required = ?, stock = ?, is_active = ? WHERE id = ?"
                    );
                    $stmt->bind_param('siiii', $name, $points, $stock, $active, $id);
                }
                $stmt->execute();
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO rewards (name, points_required, stock, is_active, type, value) VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param('siiisi', $name, $points, $stock, $active, $type, $value);
                $stmt->execute();
                $id = $conn->insert_id;
            }
            respond(['success' => true, 'id' => $id, 'message' => 'บันทึกเรียบร้อย']);
            break;

        case 'toggle':
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) respond(['success' => false, 'message' => 'ไม่พบรหัสของรางวัล'], 422);

            $stmt = $conn->prepare("UPDATE rewards SET is_active = 1 - is_active WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            respond(['success' => true]);
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) respond(['success' => false, 'message' => 'ไม่พบรหัสของรางวัล'], 422);

            $stmt = $conn->prepare("SELECT COUNT(*) FROM redemptions WHERE reward_id = ? AND used_at IS NULL");
            $stmt->bind_param('i', $id); $stmt->execute();
            if ((int)$stmt->get_result()->fetch_row()[0] > 0) 
                respond(['success' => false, 'message' => 'มีลูกค้าแลกไว้แต่ยังไม่ได้ใช้ ลบไม่ได้ ให้ปิดการแสดงผลแทน'], 409);
            

            $stmt = $conn->prepare("DELETE FROM rewards WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            respond(['success' => true]);
            break;

        default:
            respond(['success' => false, 'message' => 'Unknown action'], 400);
    }
} catch (Throwable $e) {
    error_log('rewards_api: ' . $e->getMessage());
    // ตอนยังแก้บั๊กอยู่ เปิดบรรทัดล่างแทนบรรทัดบนเพื่อดู error จริง:
    // respond(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    respond(['success' => false, 'message' => 'Server error'], 500);
}