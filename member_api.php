<?php
/**
 * member_api.php — API ฝั่งลูกค้า: ดูคะแนน / ประวัติ / แลกของรางวัล (เวอร์ชัน mysqli)
 *
 * GET  member_api.php?action=profile      -> ชื่อ + คะแนนคงเหลือ
 * GET  member_api.php?action=orders       -> ประวัติการสั่งอาหาร (50 รายการล่าสุด)
 * GET  member_api.php?action=rewards      -> ของรางวัลที่เปิดใช้งาน
 * GET  member_api.php?action=redemptions  -> ประวัติการใช้คะแนน
 * GET  member_api.php?action=coupons      -> คูปองที่แลกแล้วและยังไม่ได้ใช้
 * POST action=redeem&reward_id=..         -> แลกของรางวัล
 */
$allowed_roles = ['customer'];
$is_page = false;
require_once 'auth_guard.php';
require_once 'db_connect.php';

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Bangkok');

// ให้ mysqli โยน exception เวลา SQL ผิด (จะได้เข้า catch)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

function respond($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function get_member_id(): int {
    return (int)($_SESSION['user_id'] ?? $_SESSION['id'] ?? 0);
}

function get_points(mysqli $conn, int $memberId): int {
    $s = $conn->prepare("SELECT COALESCE(SUM(points_amount), 0) FROM points WHERE customer_id = ?");
    $s->bind_param('i', $memberId);
    $s->execute();
    return (int)$s->get_result()->fetch_row()[0];
}

$memberId = get_member_id();
if ($memberId <= 0) respond(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ'], 401);

$action = $_GET['action'] ?? $_POST['action'] ?? 'profile';

if ($action === 'redeem' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    switch ($action) {

        case 'profile':
            $stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
            $stmt->bind_param('i', $memberId);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if (!$row) respond(['success' => false, 'message' => 'ไม่พบสมาชิก'], 404);
            respond(['success' => true, 'data' => ['name' => $row['name'], 'points' => get_points($conn, $memberId)]]);
            break;

        case 'orders':
            $stmt = $conn->prepare(
                "SELECT mi.name AS name, oi.qty AS quantity, oi.unit_price AS price, o.created_at
                 FROM orders o
                 JOIN order_item oi ON oi.order_id = o.order_id
                 LEFT JOIN menu_items mi ON mi.menu_id = oi.menu_id
                 WHERE o.customer_id = ? AND o.status <> 'cancelled'
                 ORDER BY o.created_at DESC, oi.order_item_id DESC
                 LIMIT 50"
            );
            $stmt->bind_param('i', $memberId);
            $stmt->execute();
            respond(['success' => true, 'data' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
            break;

        case 'rewards':
            $result = $conn->query(
                "SELECT id, name, points_required AS points, stock
                 FROM rewards WHERE is_active = 1
                 ORDER BY points_required ASC, id ASC"
            );
            respond(['success' => true, 'data' => $result->fetch_all(MYSQLI_ASSOC)]);
            break;

        case 'coupons':
            // คูปองที่แลกไว้และยังไม่ได้ใช้ (ใช้ตอนสั่งอาหารในหน้า Menu)
            $stmt = $conn->prepare(
                "SELECT r.id, r.reward_name AS name, rw.type, rw.value
                 FROM redemptions r
                 JOIN rewards rw ON rw.id = r.reward_id
                 WHERE r.member_id = ? AND r.used_at IS NULL AND r.used_order_id IS NULL
                 ORDER BY r.created_at ASC, r.id ASC"
            );
            $stmt->bind_param('i', $memberId);
            $stmt->execute();
            respond(['success' => true, 'data' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
            break;

        case 'redemptions':
            $stmt = $conn->prepare(
                "SELECT reward_name AS name, points_used AS points, created_at,
                        (used_at IS NOT NULL) AS used
                 FROM redemptions WHERE member_id = ?
                 ORDER BY created_at DESC, id DESC LIMIT 50"
            );
            $stmt->bind_param('i', $memberId);
            $stmt->execute();
            respond(['success' => true, 'data' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
            break;

        case 'redeem':
            $rewardId = (int)($_POST['reward_id'] ?? 0);
            if ($rewardId <= 0) respond(['success' => false, 'message' => 'ไม่พบของรางวัล'], 422);

            $conn->begin_transaction();
            try {
                // ล็อกแถวผู้ใช้ กันกดแลกพร้อมกันแล้วแต้มติดลบ
                $s = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? FOR UPDATE");
                $s->bind_param('i', $memberId);
                $s->execute();
                if (!$s->get_result()->fetch_row()) {
                    $conn->rollback();
                    respond(['success' => false, 'message' => 'ไม่พบสมาชิก'], 404);
                }

                $s = $conn->prepare("SELECT id, name, points_required, stock, is_active FROM rewards WHERE id = ? FOR UPDATE");
                $s->bind_param('i', $rewardId);
                $s->execute();
                $reward = $s->get_result()->fetch_assoc();

                if (!$reward || (int)$reward['is_active'] !== 1) {
                    $conn->rollback();
                    respond(['success' => false, 'message' => 'ของรางวัลนี้ไม่พร้อมให้แลกแล้ว'], 404);
                }
                if ((int)$reward['stock'] === 0) {
                    $conn->rollback();
                    respond(['success' => false, 'message' => 'ของรางวัลหมดแล้ว'], 409);
                }

                $cost    = (int)$reward['points_required'];
                $balance = get_points($conn, $memberId);
                if ($balance < $cost) {
                    $conn->rollback();
                    respond(['success' => false, 'message' => 'คะแนนสะสมไม่เพียงพอ'], 409);
                }

                // ตัดแต้ม = เพิ่มรายการค่าลบในตาราง points
                $reason = mb_substr('แลกของรางวัล: ' . $reward['name'], 0, 100);
                $neg    = -$cost;
                $s = $conn->prepare("INSERT INTO points (customer_id, order_id, points_amount, reason) VALUES (?, NULL, ?, ?)");
                $s->bind_param('iis', $memberId, $neg, $reason);
                $s->execute();

                if ((int)$reward['stock'] > 0) {   // -1 = ไม่จำกัด
                    $s = $conn->prepare("UPDATE rewards SET stock = stock - 1 WHERE id = ?");
                    $s->bind_param('i', $rewardId);
                    $s->execute();
                }

                $s = $conn->prepare("INSERT INTO redemptions (member_id, reward_id, reward_name, points_used) VALUES (?, ?, ?, ?)");
                $s->bind_param('iisi', $memberId, $rewardId, $reward['name'], $cost);
                $s->execute();

                $conn->commit();
            } catch (Throwable $e) {
                $conn->rollback();
                throw $e;
            }

            respond([
                'success' => true,
                'message' => 'แลก "' . $reward['name'] . '" เรียบร้อยแล้ว',
                'points'  => $balance - $cost,
            ]);
            break;

        default:
            respond(['success' => false, 'message' => 'Unknown action'], 400);
    }
} catch (Throwable $e) {
    error_log('member_api: ' . $e->getMessage());
    // ตอนแก้บั๊ก เปิดบรรทัดล่างแทนบรรทัดบนเพื่อดู error จริง:
    // respond(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    respond(['success' => false, 'message' => 'Server error'], 500);
}