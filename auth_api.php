<?php
 
// บอก browser ว่าข้อมูลที่ตอบกลับไปเป็น JSON (ไม่ใช่ HTML) ป้องกันปัญหาภาษาไทยเพี้ยนด้วย charset=utf-8
header('Content-Type: application/json; charset=utf-8');
 
// ดึงไฟล์เชื่อมต่อฐานข้อมูลมาใช้ (จะได้ตัวแปร $conn มาใช้ query ได้เลย)
require_once 'db_connect.php';
require_once 'session_boot.php';
 
// เช็คว่า JS ฝั่งหน้าเว็บส่ง action อะไรมา (register/login/logout)
// ใช้ ?? '' กันไว้เผื่อไม่มีการส่ง action มาเลย จะได้ไม่ error
$action = $_POST['action'] ?? '';
 
if ($action === 'register') {
 
    // ดึงค่าจากฟอร์มที่ส่งมา trim() ตัดช่องว่างหน้า-หลังออก กันเผลอเคาะ space
    $name  = trim($_POST['username']);
    $phone = trim($_POST['tel']);
    $password = $_POST['password'];
 
    // เช็คว่ากรอกครบไหม ถ้าไม่ครบ ตอบ error กลับไปทันที ไม่ต้องทำอะไรต่อ
    if (!$name || !$phone || !$password) {
        echo json_encode(['error' => 'กรุณากรอกข้อมูลให้ครบ']);
        exit(); // หยุดการทำงานทันที ไม่ให้รันโค้ดข้างล่างต่อ
    }

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo json_encode(['error' => 'กรุณากรอกเบอร์โทรศัพท์ให้ครบ 10 หลัก']);
        exit();
    }
 
    // เช็คว่าเบอร์นี้เคยสมัครไปแล้วหรือยัง (กันสมัครซ้ำ)
    // ใช้ prepare + bind_param เพื่อป้องกัน SQL Injection (ไม่เขียน SQL ต่อ string ตรงๆ)
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE phone = ?");
    $stmt->bind_param("s", $phone); // "s" หมายถึงค่าที่ผูกเป็นชนิด string
    $stmt->execute();
    $stmt->store_result(); // เก็บผลลัพธ์ไว้เช็คจำนวนแถว
 
    if ($stmt->num_rows > 0) {
        // ถ้าเจอแถวที่มีเบอร์นี้อยู่แล้ว แปลว่ามีคนสมัครไปแล้ว
        echo json_encode(['error' => 'เบอร์นี้สมัครสมาชิกไปแล้ว']);
        exit();
    }
    $stmt->close(); // ปิดการใช้งาน statement นี้ ก่อนจะเปิดอันใหม่

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result(); 
    
    if ($stmt->num_rows > 0) {
        echo json_encode(['error' => 'ชื่อผู้ใช้นี้ถูกใช้แล้ว']);    
        exit();
    }
    $stmt->close();
 
    // เข้ารหัส password ก่อนเก็บ ห้ามเก็บเป็นตัวหนังสือตรงๆ เด็ดขาด (เพื่อความปลอดภัย)
    $hashed = password_hash($password, PASSWORD_DEFAULT);
 
    // เตรียมคำสั่ง insert ข้อมูลผู้ใช้ใหม่ ตั้ง role เป็น 'customer' ตายตัว
    // (เพราะฟอร์มนี้เปิดให้ลูกค้าสมัครเองเท่านั้น ไม่ให้เลือก role เอง)
    $stmt = $conn->prepare("INSERT INTO users (name, phone, password, role) VALUES (?, ?, ?, 'customer')");
    $stmt->bind_param("sss", $name, $phone, $hashed); // "sss" = ผูกค่า 3 ตัว เป็น string ทั้งหมด
    $stmt->execute();
    $newId = $conn->insert_id;          // ← ที่ขาดอยู่
    
    start_role_session('customer');
    session_regenerate_id(true);
    $_SESSION['user_id'] = $newId;
    $_SESSION['name']    = $name;
    $_SESSION['role']    = 'customer';
    
    echo json_encode(['success' => true]);
 
} elseif ($action === 'login') {
 
    $name = trim($_POST['username']);
    $password = $_POST['password'];
 
    // ดึงข้อมูลผู้ใช้ที่มีชื่อนี้ออกมาจากฐานข้อมูล
    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // ดึงมาเป็น array แบบมี key ชื่อคอลัมน์ (ถ้าไม่เจอ จะได้ null)
 
    // เช็ค 2 อย่างพร้อมกัน: หาผู้ใช้เจอไหม (!$user) และรหัสผ่านตรงกับที่เก็บ hash ไว้ไหม
    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['error' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
        exit();
    }

    start_role_session($user['role']); 

    session_regenerate_id(true); // สร้าง session id ใหม่ทุกครั้งที่ login เพื่อความปลอดภัย
 
    // ถ้าถูกต้อง เก็บข้อมูลไว้ใน session จำไว้ว่าใคร login อยู่ และมี role อะไร
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
 
    echo json_encode(['success' => true, 'role' => $user['role']]);
 
} elseif ($action === 'logout') {

    $group = $_POST['group'] ?? 'customer';
    start_role_session($group);
    $_SESSION = [];
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    session_destroy();
    echo json_encode(['success' => true]);

} else {

    // ถ้า action ที่ส่งมาไม่ตรงกับ 3 แบบข้างบนเลย แปลว่าเรียกผิด
    echo json_encode(['error' => 'invalid action']);
}
