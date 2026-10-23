<?php

require_once 'session_boot.php';
$authorized = false;
foreach ($allowed_roles as $r) {

    // ข้าม role ที่ไม่มี cookie เลย จะได้ไม่สร้างไฟล์ session ขยะ
    if (!isset($_COOKIE['PPG_' . strtoupper($r)])) continue;
    start_role_session($r);
    if (($_SESSION['role'] ?? '') === $r) { $authorized = true; break; }
}
 
if (!$authorized) {
    if (!empty($is_page)) {
        header('Location: Login-Signin.html');
        exit();
    }

    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'ไม่มีสิทธิ์เข้าถึงส่วนนี้']);
    exit();
}
 