<?php
$allowed_roles = ['customer'];
$is_page = true;
require_once 'auth_guard.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerPuffGirls Restaurant - Membership</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="member_common.js"></script>
    <script src="auto_refresh.js"></script>
</head>
<body class="membership">
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.html">PowerPuffGirls Restaurant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav w-100">
                    <a class="nav-link" href="home.html">Home</a>
                    <a class="nav-link" href="Menu.html">Menu</a>
                    <a href="membership.php" class="nav-link active ms-auto me-2" aria-current="page">Membership</a>
                    <a href="Login-Signin.html" id="logout-btn" class="btn btn-primary btn-sm mt-2 mt-sm-0">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    
    <header class="bg-dark text-white pb-2">
        <div class="text-center pt-4 mb-0">
            <h1>ข้อมูลสมาชิก</h1>
            <h3>สวัสดี , คุณ <span id="member-name">...</span></h3>
        </div>
    </header>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-static text-center">
                    <div class="card-body">
                        <h2 class="card-title">คะแนนสะสมของคุณ</h2>
                        <p class="display-6 fw-bold mb-3" style="color: var(--clay-rose);">
                            <span id="points">0</span> แต้ม
                        </p>
                        <a href="usedpoint.html" class="btn btn-primary">
                            ใช้คะแนนสะสม <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="text-center text-muted small mt-3">
                    <p class="mb-0">* ยอดตั้งแต่ 100 บาท/ออเดอร์ รับ 10 แต้ม และ รับเพิ่ม 5 แต้ม ทุก 50 บาท ที่เกิน 100 บาท  *</p>
                </div>
            </div>
        </div>
    </div>
    <br><br>

    <div class="container text-center mt-4 mb-5">
        <h3>ประวัติการสั่งอาหาร</h3>
        <div class="table-responsive mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">รายการอาหาร</th>
                        <th scope="col">จำนวน</th>
                        <th scope="col">ราคา (บาท)</th>
                        <th scope="col">วัน/เวลา</th>
                    </tr>
                </thead>
                <tbody id="order-list-body">
                    <tr><td colspan="4" class="text-muted py-4">กำลังโหลด...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        async function loadOrders() {
            const body = document.getElementById('order-list-body');
            try {
                const json = await api('member_api.php?action=orders');
                if (!json.success) throw new Error(json.message);
                if (!json.data.length) {
                    body.innerHTML = '<tr><td colspan="4" class="text-muted py-4">ยังไม่มีประวัติการสั่งอาหาร</td></tr>';
                    return;
                }
                body.innerHTML = json.data.map(o =>
                    '<tr><td>' + esc(o.name) + '</td>' +
                    '<td>' + Number(o.quantity).toLocaleString('th-TH') + '</td>' +
                    '<td>' + Number(o.price).toLocaleString('th-TH') + '</td>' +
                    '<td>' + fmtDate(o.created_at) + '</td></tr>'
                ).join('');
            } catch (err) {
                if (err.message !== 'unauthorized')
                    body.innerHTML = '<tr><td colspan="4" class="text-danger py-4">โหลดข้อมูลไม่สำเร็จ</td></tr>';
            }
        }

        const refreshMember = () => Promise.all([loadProfile().catch(() => {}), loadOrders()]);
        refreshMember();
        autoRefresh(refreshMember, 15000);

    </script>
</body>
</html>