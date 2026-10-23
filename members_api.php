<?php
/**
 * members_api.php — API รายชื่อสมาชิก (เฉพาะแอดมิน) เวอร์ชัน mysqli
 *
 * GET members_api.php?action=list          -> รายชื่อสมาชิกทั้งหมด
 * GET members_api.php?action=list&q=สมชาย  -> ค้นหาจากชื่อ/เบอร์
 */
$allowed_roles = ['admin'];
$is_page = false;
require_once 'auth_guard.php';
require_once 'db_connect.php';

header('Content-Type: application/json; charset=utf-8');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

function respond($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            $q = trim($_GET['q'] ?? '');

            $sql = "SELECT u.user_id AS id, u.name, u.phone, u.created_at,
                           COALESCE((SELECT SUM(p.points_amount) FROM points p WHERE p.customer_id = u.user_id), 0) AS points
                    FROM users u
                    WHERE u.role = 'customer'";

            if ($q !== '') {
                $sql .= " AND (u.name LIKE ? OR u.phone LIKE ?)";
                $sql .= " ORDER BY u.created_at DESC";
                $like = '%' . $q . '%';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $like, $like);
            } else {
                $sql .= " ORDER BY u.created_at DESC";
                $stmt = $conn->prepare($sql);
            }

            $stmt->execute();
            respond(['success' => true, 'data' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
            break;

        default:
            respond(['success' => false, 'message' => 'Unknown action'], 400);
    }
} catch (Throwable $e) {
    error_log('members_api: ' . $e->getMessage());
    respond(['success' => false, 'message' => 'Server error'], 500);
}