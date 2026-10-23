<?php
    $allowed_roles = ['kitchen'];
    $is_page = true;
    require_once 'auth_guard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerPuffGirls Restaurant - Kitchen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="kitchen">
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">PowerPuffGirls Restaurant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav  w-100">
                    <a class="nav-link active" aria-current="page" href="kitchen.php">จัดการออเดอร์</a>
                    <a class="nav-link" href="admin_menu.php">จัดการเมนู</a>
                    <a class="nav-link" aria-current="page" href="admin_inventory.php">จัดการสต็อก</a>
                    <a href="Login-Signin.html" id="admin-logout-btn" class="btn btn-primary btn-sm ms-auto">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-5 mt-4 mb-5">
        <div class="page-title">
            <h1>จัดการออเดอร์</h1>
            <p>ติดตามและเปลี่ยนสถานะออเดอร์ของลูกค้า</p>
        </div>
        <div class="mb-4"></div>

        <div class="mb-4 nav nav- border-0 d-flex justify-content-center">
            <div class="btn-group flex-wrap status-filter-bg" role="group">
                <button class="btn btn-outline-secondary status-filter-btn active" data-status="">ทั้งหมด</button>
                <button class="btn btn-outline-secondary status-filter-btn" data-status="pending">รอดำเนินการ</button>
                <button class="btn btn-outline-secondary status-filter-btn" data-status="preparing">กำลังเตรียม</button>
                <button class="btn btn-outline-secondary status-filter-btn" data-status="ready">พร้อมส่ง</button>
                <button class="btn btn-outline-secondary status-filter-btn" data-status="completed">เสร็จสิ้น</button>
                <button class="btn btn-outline-secondary status-filter-btn" data-status="cancelled">ยกเลิก</button>
            </div>
        </div>

        <div id="orders-list"></div>
    </div>

    <div class="modal fade" id="orderDetailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">รายละเอียดออเดอร์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="order-detail-body"></div>
            </div>
        </div>
    </div>

    <div class="container my-4" style="max-width: 700px;"></div>

    <script src="auto_refresh.js"></script>
    <script src="kitchen.js"></script>
</body>
</html>