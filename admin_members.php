<?php
    $allowed_roles = ['admin'];
    $is_page = true;
    require_once 'auth_guard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerPuffGirls Restaurant - Admin_Members</title>
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
    <script src="auto_refresh.js"></script>
</head>
<body class="admin">
    <script>window.ALLOWED_ROLES = ['admin'];</script>

    <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
                <a class="navbar-brand" href="#">PowerPuffGirls Restaurant</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav w-100">
                        <a href="admin.php" class="nav-link">Dashboard</a>
                        <a class="nav-link" href="admin_members.php">สมาชิก</a>
                        <a class="nav-link" href="admin_rewards.php">ของรางวัล</a>
                        <a href="Login-Signin.html" id="admin-logout-btn" class="btn btn-primary btn-sm mt-2 mt-sm-0 ms-auto">Logout</a>
                    </div>
                </div>
            </div>
    </nav>

        <div class="container-fluid px-5 mt-4 mb-5" id="tab-members">
            <h4 class="mb-3">รายชื่อสมาชิก</h4>
            <div class="table-responsive">
                <table class="table table-hover bg-white">
                    <thead><tr><th>ชื่อ</th><th>เบอร์โทร</th><th>แต้มสะสม</th><th>ใช้แต้มไปแล้ว</th><th>สมัครเมื่อ</th><th>ประวัติการแลก</th><th>ประวัติการใช้คูปอง</th></tr></thead>
                    <tbody id="member-list-body"></tbody>
                </table>
            </div>
        </div>
    
    <div class="modal fade" id="redeemModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="redeem-modal-title">ประวัติการแลกของรางวัล</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <thead><tr><th>ของรางวัล</th><th class="text-end text-nowrap">แต้มที่ใช้</th><th class="text-nowrap ps-4">วัน/เวลา</th><th>สถานะคูปอง</th></tr></thead>
                        <tbody id="redeem-modal-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="couponModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="coupon-modal-title">ประวัติการใช้คูปอง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <thead><tr><th>คูปอง</th><th>ใช้กับออเดอร์</th><th>วัน/เวลาที่ใช้</th><th class="text-end">ส่วนลดที่ได้</th></tr></thead>
                        <tbody id="coupon-modal-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function esc(s) {
            return String(s ?? "").replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;"}[c]));
        }

        const fmtDT = s => s ? new Date(s.replace(' ', 'T')).toLocaleString('th-TH') : '-';
        let redeemsById = {};   // { user_id: [รายการแลก...] }

        async function loadRedemptions() {
            try {
                const res  = await fetch('redemptions_api.php');
                const json = await res.json();
                if (!json.success) throw new Error(json.message || json.error || 'error');
                redeemsById = {};
                json.data.forEach(r => {
                    (redeemsById[r.member_id] = redeemsById[r.member_id] || []).push(r);
                });
            } catch (err) {
                console.error(err);
            }
        }

        async function loadMembers() {
            const tbody = document.getElementById('member-list-body');
            try {
                await loadRedemptions();
                const res  = await fetch('members_api.php?action=list');
                const json = await res.json();
                if (!json.success) throw new Error(json.message || json.error || 'error');

                if (!json.data.length) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">ยังไม่มีสมาชิก</td></tr>';
                    return;
                }

                tbody.innerHTML = json.data.map(m => {
                    const list  = redeemsById[m.id] || [];
                    const used  = list.reduce((sum, r) => sum + Number(r.points_used), 0);
                    const btn   = list.length
                        ? `<button class="btn btn-sm btn-outline-secondary redeem-btn" data-id="${esc(m.id)}" data-name="${esc(m.name)}">ดู (${list.length})</button>`
                        : '<span class="text-muted">-</span>';
                    const usedList = list.filter(r => Number(r.is_used));
                    const cbtn  = usedList.length
                        ? `<button class="btn btn-sm btn-outline-secondary coupon-btn" data-id="${esc(m.id)}" data-name="${esc(m.name)}">ดู (${usedList.length})</button>`
                        : '<span class="text-muted">-</span>';
                    return `
                    <tr>
                        <td>${esc(m.name)}</td>
                        <td>${esc(m.phone)}</td>
                        <td>${Number(m.points).toLocaleString('th-TH')}</td>
                        <td>${used.toLocaleString('th-TH')}</td>
                        <td>${esc(m.created_at ? new Date(m.created_at.replace(' ', 'T')).toLocaleDateString('th-TH') : '-')}</td>
                        <td>${btn}</td>
                        <td>${cbtn}</td>
                    </tr>`;
                }).join('');
            } catch (err) {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">โหลดข้อมูลไม่สำเร็จ</td></tr>';
            }
        }

        // กดปุ่ม "ดู" -> เปิด modal แสดงรายการที่แลก
        document.getElementById('member-list-body').addEventListener('click', e => {
            // ปุ่มประวัติการใช้คูปอง -> เปิด modal แสดงคูปองที่ถูกใช้กับออเดอร์
            const cbtn = e.target.closest('button.coupon-btn');
            if (cbtn) {
                const used = (redeemsById[cbtn.dataset.id] || [])
                    .filter(r => Number(r.is_used))
                    .sort((a, b) => String(b.used_at).localeCompare(String(a.used_at)));
                document.getElementById('coupon-modal-title').textContent = 'ประวัติการใช้คูปอง - ' + cbtn.dataset.name;
                document.getElementById('coupon-modal-body').innerHTML = used.map(r =>
                    '<tr><td>' + esc(r.reward_name) + '</td>' +
                    '<td>ออเดอร์ #' + esc(r.used_order_id) + '</td>' +
                    '<td>' + esc(fmtDT(r.used_at)) + '</td>' +
                    '<td class="text-end">' + (Number(r.used_discount) > 0
                        ? '-' + Number(r.used_discount).toLocaleString('th-TH') + ' บาท'
                        : '<span class="text-muted">ไม่มีส่วนลด (ของรางวัล)</span>') + '</td></tr>'
                ).join('');
                new bootstrap.Modal(document.getElementById('couponModal')).show();
                return;
            }

            const btn = e.target.closest('button.redeem-btn');
            if (!btn) return;
            const list = redeemsById[btn.dataset.id] || [];
            document.getElementById('redeem-modal-title').textContent = 'ประวัติการแลกของรางวัล - ' + btn.dataset.name;
            document.getElementById('redeem-modal-body').innerHTML = list.map(r =>
                '<tr><td>' + esc(r.reward_name) + '</td>' +
                '<td class="text-end">' + Number(r.points_used).toLocaleString('th-TH') + '</td>' +
                '<td class="text-nowrap ps-4">' + esc(fmtDT(r.created_at)) + '</td>' +
                '<td>' + (Number(r.is_used)
                    ? '<span class="badge bg-secondary">ใช้แล้ว (ออเดอร์ #' + esc(r.used_order_id) + ')</span>'
                    : '<span class="badge bg-success">ยังไม่ใช้</span>') + '</td></tr>'
            ).join('');
            new bootstrap.Modal(document.getElementById('redeemModal')).show();
        });

        loadMembers();
        autoRefresh(loadMembers, 30000);

        document.getElementById('admin-logout-btn').addEventListener('click', function (e) {
            e.preventDefault();
            fetch('auth_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=logout&group=admin'
            }).then(() => { window.location.href = 'Login-Signin.html'; });
        });
    </script>
</body>
</html>