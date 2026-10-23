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
    <title>PowerPuffGirls Restaurant - ของรางวัล</title>
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
    <script>window.ALLOWED_ROLES = ['admin'];</script>

    <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php">PowerPuffGirls Restaurant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav w-100">
                    <a class="nav-link" href="admin.php">Dashboard</a>
                    <a class="nav-link" href="admin_members.php">สมาชิก</a>
                    <a class="nav-link active" aria-current="page" href="admin_rewards.php">ของรางวัล</a>
                    <a href="Login-Signin.html" id="admin-logout-btn" class="btn btn-primary btn-sm mt-2 mt-sm-0 ms-auto">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-5 mt-4 mb-5" id="tab-rewards">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">จัดการของรางวัล</h4>
            <button id="add-reward-btn" class="btn">+ เพิ่มของรางวัล</button>
        </div>

        <div id="reward-form-card" class="card p-3 mb-3" style="display:none;">
            <input type="hidden" id="reward-id">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="reward-name">ชื่อของรางวัล</label>
                    <input type="text" id="reward-name" class="form-control" maxlength="100">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" for="reward-points">แต้มที่ต้องใช้</label>
                    <input type="number" id="reward-points" class="form-control" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" for="reward-stock">สต็อก (-1 = ไม่จำกัด)</label>
                    <input type="number" id="reward-stock" class="form-control" min="-1" value="-1">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold d-block">สถานะ</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="reward-active" checked>
                        <label class="form-check-label" for="reward-active">ใช้งาน</label>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button id="save-reward-btn" class="btn me-2">บันทึก</button>
                <button id="cancel-reward-btn" class="btn btn-outline-secondary">ยกเลิก</button>
            </div>
            <div id="reward-form-result" class="mt-2"></div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover bg-white align-middle">
                <thead><tr><th>ชื่อ</th><th class="text-end">แต้มที่ต้องใช้</th><th class="text-end">สต็อก</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
                <tbody id="reward-list-body"><tr><td colspan="5" class="text-center text-muted py-4">กำลังโหลด...</td></tr></tbody>
            </table>
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
        const API = 'rewards_api.php';
        const $ = id => document.getElementById(id);
        let rewards = [];

        async function post(data) {
            const res = await fetch(API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data).toString()
            });
            return res.json();
        }

        async function loadRewards() {
            const body = $('reward-list-body');
            try {
                const res  = await fetch(API + '?action=list');
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'error');
                rewards = json.data;

                if (!rewards.length) {
                    body.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">ยังไม่มีของรางวัล</td></tr>';
                    return;
                }
                body.innerHTML = rewards.map(r => {
                    const on = Number(r.active) === 1;
                    const stock = Number(r.stock) === -1 ? 'ไม่จำกัด' : Number(r.stock).toLocaleString('th-TH');
                    return '<tr' + (on ? '' : ' class="opacity-50"') + '>' +
                        '<td>' + esc(r.name) + '</td>' +
                        '<td class="text-end">' + Number(r.points).toLocaleString('th-TH') + '</td>' +
                        '<td class="text-end">' + stock + '</td>' +
                        '<td><span class="badge ' + (on ? 'text-bg-success' : 'text-bg-secondary') + '">' + (on ? 'ใช้งาน' : 'ปิด') + '</span></td>' +
                        '<td><div class="reward-actions">' +
                            '<div class="d-flex gap-1">' +
                                '<button class="btn btn-sm reward-btn-edit" data-act="edit" data-id="' + r.id + '">แก้ไข</button>' +
                                '<button class="btn btn-sm ' + (on ? 'reward-btn-off' : 'reward-btn-on') + '" data-act="toggle" data-id="' + r.id + '">' + (on ? 'ปิด' : 'เปิด') + '</button>' +
                            '</div>' +
                            '<button class="btn btn-sm reward-btn-delete" data-act="delete" data-id="' + r.id + '">ลบ</button>' +
                        '</div></td></tr>';
                }).join('');
            } catch (err) {
                console.error(err);
                body.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">โหลดข้อมูลไม่สำเร็จ</td></tr>';
            }
        }

        function showForm(r) {
            $('reward-id').value      = r ? r.id : '';
            $('reward-name').value    = r ? r.name : '';
            $('reward-points').value  = r ? r.points : '';
            $('reward-stock').value   = r ? r.stock : -1;
            $('reward-active').checked = r ? Number(r.active) === 1 : true;
            $('reward-form-result').innerHTML = '';
            $('reward-form-card').style.display = 'block';
            $('reward-name').focus();
        }
        function hideForm() { $('reward-form-card').style.display = 'none'; }

        $('add-reward-btn').addEventListener('click', () => showForm(null));
        $('cancel-reward-btn').addEventListener('click', hideForm);

        $('save-reward-btn').addEventListener('click', async () => {
            const result = $('reward-form-result');
            const name = $('reward-name').value.trim();
            if (!name) { result.innerHTML = '<span class="text-danger">กรุณากรอกชื่อของรางวัล</span>'; return; }

            try {
                const json = await post({
                    action: 'save',
                    id:     $('reward-id').value,
                    name:   name,
                    points: $('reward-points').value,
                    stock:  $('reward-stock').value,
                    active: $('reward-active').checked ? 1 : 0
                });
                if (!json.success) { result.innerHTML = '<span class="text-danger">' + esc(json.message) + '</span>'; return; }
                hideForm();
                loadRewards();
            } catch (err) {
                result.innerHTML = '<span class="text-danger">เกิดข้อผิดพลาด ลองใหม่อีกครั้ง</span>';
            }
        });

        // ปุ่มในตาราง (ใช้ event delegation)
        $('reward-list-body').addEventListener('click', async e => {
            const btn = e.target.closest('button[data-act]');
            if (!btn) return;
            const id = btn.dataset.id;
            const r  = rewards.find(x => String(x.id) === id);

            if (btn.dataset.act === 'edit') {
                showForm(r);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else if (btn.dataset.act === 'toggle') {
                await post({ action: 'toggle', id });
                loadRewards();
            } else if (btn.dataset.act === 'delete') {
                if (!confirm('ลบ "' + (r ? r.name : '') + '" ใช่หรือไม่?')) return;
                const json = await post({ action: 'delete', id });
                if (!json.success) alert(json.message || 'ลบไม่สำเร็จ');
                loadRewards();
            }
        });

        loadRewards();
        autoRefresh(loadRewards, 30000);
    </script>
</body>
</html>