<?php
    $allowed_roles = ['admin'];
    $is_page = true;
    require_once 'auth_guard.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerPuffGirls Restaurant - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="auto_refresh.js"></script>
</head>
<body class="admin">
    <!-- admin_guard.js ต้องโหลดก่อนอย่างอื่นเสมอ -->
    <script>window.ALLOWED_ROLES = ['admin'];</script>

    <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php">PowerPuffGirls Restaurant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav w-100">
                    <a class="nav-link active" aria-current="page" href="admin.php">Dashboard</a>
                    <a class="nav-link" href="admin_members.php">สมาชิก</a>
                    <a class="nav-link" href="admin_rewards.php">ของรางวัล</a>
                    <a href="Login-Signin.html" id="admin-logout-btn" class="btn btn-primary btn-sm mt-2 mt-sm-0 ms-auto">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4 mb-5 px-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">สรุปภาพรวมวันนี้</h2>
            <small class="text-muted" id="updated-at"></small>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card text-center">
                    <p class="text-muted mb-1">ยอดขายวันนี้</p>
                    <p><span id="today-revenue">-</span> บาท</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card text-center">
                    <p class="text-muted mb-1">จำนวนออเดอร์วันนี้</p>
                    <p><span id="today-orders">-</span> ออเดอร์</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card text-center">
                    <p class="text-muted mb-1">ออเดอร์ที่เสร็จสิ้น</p>
                    <p><span id="completed-count">-</span> ออเดอร์</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("admin-logout-btn").addEventListener("click", function (e) {
            e.preventDefault();
            fetch("auth_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "action=logout&group=admin"
            }).then(() => { window.location.href = "Login-Signin.html"; });
        });
        // ป้องกัน XSS ตอนเอาข้อมูลจาก DB ไปใส่ innerHTML
        function esc(s) {
            return String(s ?? "").replace(/[&<>"\x27]/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","\x27":"&#39;"}[c]));
        }
    </script>
    
    <script>
        async function loadDashboard() {
            try {
                const res  = await fetch('dashboard_api.php');
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'error');
                const d = json.data;
                document.getElementById('today-revenue').textContent  = d.revenue.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('today-orders').textContent   = d.orders.toLocaleString('th-TH');
                document.getElementById('completed-count').textContent = d.completed.toLocaleString('th-TH');
                document.getElementById('updated-at').textContent = 'อัปเดตล่าสุด ' + new Date().toLocaleTimeString('th-TH');
            } catch (err) {
                console.error(err);
                document.getElementById('updated-at').textContent = 'โหลดข้อมูลไม่สำเร็จ';
            }
        }
        loadDashboard();
        autoRefresh(loadDashboard, 30000);
    </script>
</body>
</html>