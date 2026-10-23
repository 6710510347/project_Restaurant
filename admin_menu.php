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
    <script src="auto_refresh.js"></script>
</head>
<body class="admin_menu">
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">PowerPuffGirls Restaurant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav  w-100">
                    <a class="nav-link" aria-current="page" href="kitchen.php">จัดการออเดอร์</a>
                    <a class="nav-link active" href="admin_menu.php">จัดการเมนู</a>
                    <a class="nav-link" aria-current="page" href="admin_inventory.php">จัดการสต็อก</a>
                    <a href="Login-Signin.html" id="admin-logout-btn" class="btn btn-primary btn-sm ms-auto">Logout</a>
                </div>
            </div>
        </div>
    </nav>
 
<main>
    <div class="head text-center mb-4">
        <h2>จัดการเมนู</h2>
        <button class="btn" id="btnAdd">+ เพิ่มเมนูใหม่</button>
    </div>
 
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>รูป</th><th>ชื่อเมนู</th><th>หมวดหมู่</th><th>ราคา</th><th>สถานะ</th><th>จัดการ</th></tr>
            </thead>
            <tbody id="menuBody"><tr><td colspan="6" class="empty">กำลังโหลด...</td></tr></tbody>
        </table>
    </div>
    </main>
        
    <!-- Modal เพิ่ม/แก้ไขเมนู -->
    <dialog id="menuDlg">
        <form method="dialog" id="menuForm">
            <h3 id="dlgTitle">เพิ่มเมนูใหม่</h3>
            <input type="hidden" id="fId">
                <div class="f"><label for="fName">ชื่อเมนู</label><input type="text" id="fName" required></div>
                <div class="f">
                    <label for="fCat">หมวดหมู่</label>
                    <input type="text" id="fCat" list="catList" placeholder="เช่น เครื่องดื่ม, ของหวาน, อาหาร">
                    <datalist id="catList"></datalist>
                </div>
                <div class="f"><label for="fDesc">คำอธิบาย</label><textarea id="fDesc"></textarea></div>
                <div class="f">
                    <label for="fImg">ลิงก์รูปภาพ</label>
                    <input type="text" id="fImg" placeholder="ลิงก์รูปภาพ">
                    <img id="imgPrev" class="preview" alt="" hidden>
                </div>
                <div class="row">
                    <div class="f"><label for="fPrice">ราคา (บาท)</label><input type="number" id="fPrice" min="0" step="0.01" required></div>
                    <div class="f"><label for="fHot">ราคาร้อน</label><input type="number" id="fHot" min="0" step="0.01"></div>
                    <div class="f"><label for="fCold">ราคาเย็น</label><input type="number" id="fCold" min="0" step="0.01"></div>
                </div>
                <p class="hint">ราคาร้อน/เย็น กรอกเฉพาะเครื่องดื่มที่ลูกค้าเลือกอุณหภูมิได้ ถ้าไม่ใช่ปล่อยว่างไว้</p>
                <div class="f check"><input type="checkbox" id="fAvail" checked><label for="fAvail" style="margin:0">เปิดขาย</label></div>
                <div class="btns">
                    <button type="button" class="btn-o" id="dlgCancel">ยกเลิก</button>
                    <button type="submit" class="btn">บันทึก</button>
                </div>
        </form>
    </dialog>
        
    <div id="toast" role="status" aria-live="polite"></div>
        
    <script>
        const API = 'menu_api.php';
        let menus = [];
        
        const $ = id => document.getElementById(id);
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        const fmt = n => Number(n) % 1 === 0 ? Number(n).toFixed(0) : Number(n).toFixed(2);
        
        function toast(msg, isErr = false) {
            const t = $('toast');
            t.textContent = msg;
            t.className = 'show' + (isErr ? ' err' : '');
            clearTimeout(toast.t);
            toast.t = setTimeout(() => t.className = '', 2800);
        }
        
        async function api(action, body) {
            const opt = body ? { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) } : {};
            const res = await fetch(`${API}?action=${action}`, opt);
            if (res.status === 403) { toast('ไม่มีสิทธิ์ กรุณาเข้าสู่ระบบใหม่', true); throw new Error('403'); }
            const data = await res.json();
            if (data && data.error) { toast(data.error, true); throw new Error(data.error); }
            return data;
        }
        
        function priceHtml(m) {
            const hasHC = m.price_hot !== null || m.price_cold !== null;
            if (!hasHC) return `${fmt(m.price)} บาท`;
            const parts = [];
            if (m.price_hot !== null)  parts.push(`ร้อน ${fmt(m.price_hot)}`);
            if (m.price_cold !== null) parts.push(`เย็น ${fmt(m.price_cold)}`);
            return `${parts.join(' / ')} บาท`;
        }
        
        function thumbHtml(m) {
            const initial = esc((m.name || '?').trim().charAt(0));
            if (!m.image_url) return `<div class="thumb ph">${initial}</div>`;
            return `<img class="thumb" src="${esc(m.image_url)}" alt="${esc(m.name)}" loading="lazy"
                    onerror="this.outerHTML='<div class=&quot;thumb ph&quot;>${initial}</div>'">`;
        }
        
        async function load() {
            menus = await api('list');
            const body = $('menuBody');
            if (!menus.length) {
                body.innerHTML = '<tr><td colspan="6" class="empty">ยังไม่มีเมนู กดปุ่ม “เพิ่มเมนูใหม่” ได้เลย</td></tr>';
        } else {
            body.innerHTML = menus.map(m => {
                const on = +m.is_available === 1;
                return `<tr class="${on ? '' : 'off'}">
        <td>${thumbHtml(m)}</td>
        <td>${esc(m.name)}</td>
        <td>${esc(m.category || '-')}</td>
        <td class="price">${priceHtml(m)}</td>
        <td><span class="badge ${on ? 'ok' : 'off'}">${on ? 'เปิดขาย' : 'ปิดขาย'}</span></td>
        <td><div class="actions">
            <button class="btn-o" data-edit="${m.menu_id}">แก้ไข</button>
            <button class="btn-o ${on ? 'yellow' : 'green'}" data-toggle="${m.menu_id}">${on ? 'ปิดขาย' : 'เปิดขาย'}</button>
            <button class="btn-o red" data-del="${m.menu_id}">ลบ</button>
        </div></td>
        </tr>`;
            }).join('');
        }
        // รายการหมวดหมู่ที่มีอยู่ ใช้เป็นตัวเลือกใน popup (กันพิมพ์ผิด)
        const cats = [...new Set(menus.map(m => m.category).filter(Boolean))];
        $('catList').innerHTML = cats.map(c => `<option value="${esc(c)}">`).join('');
        }
        
        $('menuBody').addEventListener('click', async e => {
        const b = e.target.closest('button'); if (!b) return;
        if (b.dataset.edit) {
            openDlg(menus.find(m => m.menu_id == b.dataset.edit));
        } else if (b.dataset.toggle) {
            const m = menus.find(x => x.menu_id == b.dataset.toggle);
            const next = +m.is_available === 1 ? 0 : 1;
            await api('toggle', { menu_id: +m.menu_id, is_available: next });
            toast(next ? 'เปิดขายแล้ว' : 'ปิดขายแล้ว');
            load();
        } else if (b.dataset.del) {
            const m = menus.find(x => x.menu_id == b.dataset.del);
            if (!confirm(`ลบเมนู “${m.name}” ใช่หรือไม่?\nสูตรวัตถุดิบของเมนูนี้จะถูกลบไปด้วย`)) return;
            await api('delete', { menu_id: +m.menu_id });
            toast('ลบเมนูแล้ว');
            load();
        }
        });
        
        /* Modal */
        function setPreview(url) {
            const p = $('imgPrev');
            if (url) { p.src = url; p.hidden = false; } else { p.hidden = true; }
        }
        $('imgPrev').onerror = () => { $('imgPrev').hidden = true; };
        $('fImg').addEventListener('input', e => setPreview(e.target.value.trim()));
        
        function openDlg(m) {
            $('dlgTitle').textContent = m ? 'แก้ไขเมนู' : 'เพิ่มเมนูใหม่';
            $('fId').value    = m ? m.menu_id : '';
            $('fName').value  = m ? m.name : '';
            $('fCat').value   = m ? (m.category || '') : '';
            $('fDesc').value  = m ? (m.description || '') : '';
            $('fImg').value   = m ? (m.image_url || '') : '';
            $('fPrice').value = m ? m.price : '';
            $('fHot').value   = m && m.price_hot !== null ? m.price_hot : '';
            $('fCold').value  = m && m.price_cold !== null ? m.price_cold : '';
            $('fAvail').checked = m ? +m.is_available === 1 : true;
            setPreview($('fImg').value.trim());
            $('menuDlg').showModal();
            $('fName').focus();
        }
        $('btnAdd').onclick = () => openDlg(null);
        $('dlgCancel').onclick = () => $('menuDlg').close();
        
        $('menuForm').addEventListener('submit', async e => {
            e.preventDefault();
            const id = $('fId').value;
            const payload = {
                menu_id: id ? +id : undefined,
                name: $('fName').value.trim(),
                category: $('fCat').value.trim(),
                description: $('fDesc').value.trim(),
                image_url: $('fImg').value.trim(),
                price: $('fPrice').value,
                price_hot: $('fHot').value,
                price_cold: $('fCold').value,
                is_available: $('fAvail').checked ? 1 : 0
        };
        try {
            await api(id ? 'update' : 'add', payload);
            toast(id ? 'บันทึกการแก้ไขแล้ว' : 'เพิ่มเมนูแล้ว');
            $('menuDlg').close();
            load();
        } catch (_) {}
        });
        
        load().catch(() => {
            $('menuBody').innerHTML = '<tr><td colspan="6" class="empty">โหลดข้อมูลไม่ได้ ตรวจสอบว่าเข้าสู่ระบบด้วยบัญชี admin/kitchen แล้ว</td></tr>';
        });

        autoRefresh(load, 15000, () => $('menuDlg').open);

        document.getElementById('admin-logout-btn').addEventListener('click', function (e) {
            e.preventDefault();
            fetch('auth_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=logout&group=kitchen'
            }).then(() => { window.location.href = 'Login-Signin.html'; });
        });
    </script>
</body>
</html>