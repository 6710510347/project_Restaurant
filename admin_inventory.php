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
<body class="admin_inventory">
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
                        <a class="nav-link active" aria-current="page" href="admin_inventory.php">จัดการสต็อก</a>
                        <a href="Login-Signin.html" id="admin-logout-btn" class="btn btn-primary btn-sm ms-auto">Logout</a>
                    </div>
                </div>
            </div>
    </nav>

 
  <div class="page-title">
    <h1>จัดการสต็อก</h1>
  </div>
 
  <main>
    <div class="text mb-4">
      <h2>วัตถุดิบ</h2>
      <button class="btn" id="btnAddIng">+ เพิ่มวัตถุดิบใหม่</button>
    </div>
    
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>ชื่อ</th><th>หน่วย</th><th>คงเหลือ</th><th>จุดแจ้งเตือน</th><th>สถานะ</th><th>ปรับสต็อก</th><th>จัดการ</th></tr>
        </thead>
        <tbody id="ingBody"><tr><td colspan="7" class="empty">กำลังโหลด...</td></tr></tbody>
      </table>
    </div>
    
      <!-- ===== สูตรเมนู ===== -->
    <h2 class="center">สูตรเมนู (ผูกวัตถุดิบกับเมนู)</h2>
    <section class="card">
      <label class="lbl" for="menuSel">เลือกเมนูที่ต้องการดู/แก้สูตร</label>
      <select id="menuSel" class="main"><option value="">-- เลือกเมนู --</option></select>
        <p class="sub">วัตถุดิบที่ใช้ในเมนูนี้</p>
          <table class="recipe">
          <thead>
          <tr><th>วัตถุดิบ</th><th>ปริมาณที่ใช้ / จาน-แก้ว</th><th>หน่วย</th><th>ใช้กับ</th><th>ปรับตามความหวาน</th><th>จัดการ</th></tr>
          </thead>
          <tbody id="recipeBody"><tr><td colspan="6" class="empty">เลือกเมนูเพื่อดูสูตร</td></tr></tbody>
          </table>
      
          <div class="add-row" id="addRow">
            <div class="col"><label for="rIng">เพิ่มวัตถุดิบ</label><select id="rIng"></select></div>
            <div class="col"><label for="rQty">ปริมาณที่ใช้</label><input type="number" id="rQty" min="0" step="0.01"></div>
            <div class="col"><label for="rTemp">ใช้กับ</label>
              <select id="rTemp">
                <option value="all">ทั้งร้อนและเย็น</option>
                <option value="hot">เฉพาะร้อน</option>
                <option value="cold">เฉพาะเย็น</option>
              </select>
            </div>
              <label class="check"><input type="checkbox" id="rSweet"> วัตถุดิบความหวาน</label>
              <button class="btn" id="btnAddRecipe">เพิ่มเข้าสูตร</button>
          </div>
        <p class="hint">“วัตถุดิบความหวาน” คือวัตถุดิบที่ปริมาณจะเปลี่ยนตามระดับความหวานที่ลูกค้าเลือก (หวานน้อย ×0.5 / ปกติ ×1 / หวานมาก ×1.5)</p>
    </section>
  </main>
  
  <!-- Modal เพิ่ม/แก้ไขวัตถุดิบ -->
  <dialog id="ingDlg">
    <form method="dialog" id="ingForm">
      <h3 id="dlgTitle">เพิ่มวัตถุดิบใหม่</h3>
      <input type="hidden" id="fId">
        <div class="f"><label for="fName">ชื่อวัตถุดิบ</label><input type="text" id="fName" required></div>
        <div class="f"><label for="fUnit">หน่วย (เช่น กรัม, มิลลิลิตร, ฟอง)</label><input type="text" id="fUnit" required></div>
        <div class="f"><label for="fStock">จำนวนคงเหลือ</label><input type="number" id="fStock" min="0" step="0.01" value="0" required></div>
        <div class="f"><label for="fLow">จุดแจ้งเตือนสต็อกต่ำ</label><input type="number" id="fLow" min="0" step="0.01" value="0" required></div>
        <div class="btns">
          <button type="button" class="btn-o" id="dlgCancel">ยกเลิก</button>
          <button type="submit" class="btn">บันทึก</button>
        </div>
    </form>
  </dialog>
  
  <div id="toast" role="status" aria-live="polite"></div>
  
  <script>
  const API = 'inventory_api.php';
  let ingredients = [];
  let currentMenu = '';
  
  const $ = id => document.getElementById(id);
  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const num = n => Number(n).toFixed(2);
  
  function toast(msg, isErr = false) {
    const t = $('toast');
    t.textContent = msg;
    t.className = 'show' + (isErr ? ' err' : '');
    clearTimeout(toast.t);
    toast.t = setTimeout(() => t.className = '', 2800);
  }
  
  async function api(action, body, query = '') {
    const opt = body ? { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) } : {};
    const res = await fetch(`${API}?action=${action}${query}`, opt);
    if (res.status === 403) { toast('ไม่มีสิทธิ์ กรุณาเข้าสู่ระบบใหม่', true); throw new Error('403'); }
    const data = await res.json();
    if (data && data.error) { toast(data.error, true); throw new Error(data.error); }
    return data;
  }
  
  /* ---------- วัตถุดิบ ---------- */
  function statusOf(i) {
    const s = +i.stock_qty, l = +i.low_stock_threshold;
    if (s <= 0) return ['out', 'หมด'];
    if (s <= l) return ['low', 'ใกล้หมด'];
    return ['ok', 'ปกติ'];
  }
  
  async function loadIngredients() {
    ingredients = await api('list_ingredients');
    const body = $('ingBody');
    if (!ingredients.length) {
      body.innerHTML = '<tr><td colspan="7" class="empty">ยังไม่มีวัตถุดิบ กดปุ่ม “เพิ่มวัตถุดิบใหม่” ได้เลย</td></tr>';
    } else {
      body.innerHTML = ingredients.map(i => {
        const [cls, label] = statusOf(i);
        return `<tr class="row-${cls}">
  <td>${esc(i.name)}</td>
  <td>${esc(i.unit)}</td>
  <td>${num(i.stock_qty)}</td>
  <td>${num(i.low_stock_threshold)}</td>
  <td><span class="badge ${cls}">${label}</span></td>
  <td><div class="adjust">
  <input type="number" step="0.01" placeholder="+/- จำนวน" id="adj${i.ingredient_id}" aria-label="ปรับสต็อก ${esc(i.name)}">
  <button class="btn-o" data-adjust="${i.ingredient_id}">ปรับ</button>
  </div></td>
  <td><div class="actions">
  <button class="btn-o" data-edit="${i.ingredient_id}">แก้ไข</button>
  <button class="btn-o red" data-del="${i.ingredient_id}">ลบ</button>
  </div></td>
  </tr>`;
      }).join('');
    }

    const prevIng = $('rIng').value;   // จำวัตถุดิบที่เลือกไว้
    $('rIng').innerHTML = ingredients.map(i => `<option value="${i.ingredient_id}">${esc(i.name)} (${esc(i.unit)})</option>`).join('');
    if (prevIng && ingredients.some(i => String(i.ingredient_id) === prevIng)) $('rIng').value = prevIng;
    }
  
  $('ingBody').addEventListener('click', async e => {
    const b = e.target.closest('button'); if (!b) return;
    if (b.dataset.adjust) {
      const id = b.dataset.adjust, delta = parseFloat($('adj' + id).value);
      if (!delta) return toast('กรอกจำนวนที่ต้องการเพิ่ม (+) หรือลด (-)', true);
      await api('adjust_stock', { ingredient_id: +id, delta });
      toast('ปรับสต็อกแล้ว');
      loadIngredients();
    } else if (b.dataset.edit) {
      const i = ingredients.find(x => x.ingredient_id == b.dataset.edit);
      openDlg(i);
    } else if (b.dataset.del) {
      const i = ingredients.find(x => x.ingredient_id == b.dataset.del);
      if (!confirm(`ลบวัตถุดิบ “${i.name}” ใช่หรือไม่?`)) return;
      await api('delete_ingredient', { ingredient_id: +i.ingredient_id });
      toast('ลบวัตถุดิบแล้ว');
      loadIngredients();
      if (currentMenu) loadRecipe();
    }
  });
  
  /* Modal */
  function openDlg(i) {
    $('dlgTitle').textContent = i ? 'แก้ไขวัตถุดิบ' : 'เพิ่มวัตถุดิบใหม่';
    $('fId').value = i ? i.ingredient_id : '';
    $('fName').value = i ? i.name : '';
    $('fUnit').value = i ? i.unit : '';
    $('fStock').value = i ? i.stock_qty : 0;
    $('fLow').value = i ? i.low_stock_threshold : 0;
    $('ingDlg').showModal();
    $('fName').focus();
  }
  $('btnAddIng').onclick = () => openDlg(null);
  $('dlgCancel').onclick = () => $('ingDlg').close();
  $('ingForm').addEventListener('submit', async e => {
    const id = $('fId').value;
    const payload = {
      ingredient_id: id ? +id : undefined,
      name: $('fName').value.trim(),
      unit: $('fUnit').value.trim(),
      stock_qty: parseFloat($('fStock').value),
      low_stock_threshold: parseFloat($('fLow').value)
    };
    try {
      await api(id ? 'update_ingredient' : 'add_ingredient', payload);
      toast(id ? 'บันทึกการแก้ไขแล้ว' : 'เพิ่มวัตถุดิบแล้ว');
      loadIngredients();
      if (currentMenu) loadRecipe();
    } catch (_) { e.preventDefault(); $('ingDlg').showModal?.(); }
  });
  
  /* ---------- สูตรเมนู ---------- */
  async function loadMenus() {
    const menus = await api('list_menus');
    $('menuSel').innerHTML = '<option value="">-- เลือกเมนู --</option>' +
      menus.map(m => `<option value="${m.menu_id}">${esc(m.name)}</option>`).join('');
  }
  
  const tempLabel = { all: ['ทั้งหมด', ''], hot: ['ร้อน', 'hot'], cold: ['เย็น', 'cold'] };
  
  async function loadRecipe() {
    const body = $('recipeBody');
    if (!currentMenu) { body.innerHTML = '<tr><td colspan="6" class="empty">เลือกเมนูเพื่อดูสูตร</td></tr>'; return; }
    const rows = await api('get_recipe', null, '&menu_id=' + currentMenu);
    if (!rows.length) { body.innerHTML = '<tr><td colspan="6" class="empty">เมนูนี้ยังไม่มีสูตรที่ผูกไว้</td></tr>'; return; }
    body.innerHTML = rows.map((r, idx) => {
      const [t, c] = tempLabel[r.temp_type];
      return `<tr>
  <td>${esc(r.name)}</td>
  <td><input type="number" class="qty" step="0.01" min="0" value="${num(r.qty_used)}" id="q${idx}" aria-label="ปริมาณ ${esc(r.name)}"></td>
  <td>${esc(r.unit)}</td>
  <td><span class="tag ${c}">${t}</span></td>
  <td>${+r.is_sweetener ? '✓' : '–'}</td>
  <td><div class="actions">
  <button class="btn-o" data-save="${idx}">บันทึก</button>
  <button class="btn-o red" data-rdel="${idx}">ลบ</button>
  </div></td>
  </tr>`;
    }).join('');
    body._rows = rows;
  }
  
  $('recipeBody').addEventListener('click', async e => {
    const b = e.target.closest('button'); if (!b) return;
    const rows = $('recipeBody')._rows || [];
    const key = r => ({ menu_id: +currentMenu, ingredient_id: +r.ingredient_id, temp_type: r.temp_type, is_sweetener: +r.is_sweetener });
    if (b.dataset.save !== undefined) {
      const r = rows[b.dataset.save];
      await api('update_recipe', { ...key(r), qty_used: parseFloat($('q' + b.dataset.save).value) });
      toast('บันทึกปริมาณแล้ว');
      loadRecipe();
    } else if (b.dataset.rdel !== undefined) {
      const r = rows[b.dataset.rdel];
      if (!confirm(`เอา “${r.name}” ออกจากสูตรนี้ใช่หรือไม่?`)) return;
      await api('delete_recipe', key(r));
      toast('ลบออกจากสูตรแล้ว');
      loadRecipe();
    }
  });
  
  $('menuSel').addEventListener('change', e => { currentMenu = e.target.value; loadRecipe(); });
  
  $('btnAddRecipe').onclick = async () => {
    if (!currentMenu) return toast('กรุณาเลือกเมนูก่อน', true);
    const qty = parseFloat($('rQty').value);
    if (!qty || qty <= 0) return toast('กรุณาใส่ปริมาณที่มากกว่า 0', true);
    await api('add_recipe', {
      menu_id: +currentMenu,
      ingredient_id: +$('rIng').value,
      qty_used: qty,
      temp_type: $('rTemp').value,
      is_sweetener: $('rSweet').checked ? 1 : 0
    });
    $('rQty').value = ''; $('rSweet').checked = false;
    toast('เพิ่มเข้าสูตรแล้ว');
    loadRecipe();
  };
  
  /* เริ่มต้น */
  (async () => {
    try { await Promise.all([loadIngredients(), loadMenus()]); }
    catch (_) { $('ingBody').innerHTML = '<tr><td colspan="7" class="empty">โหลดข้อมูลไม่ได้ ตรวจสอบว่าเข้าสู่ระบบด้วยบัญชี admin/kitchen แล้ว</td></tr>'; }
  })();

    autoRefresh(loadIngredients, 15000, () =>
    $('ingDlg').open ||
    document.activeElement?.closest('#ingBody') ||
    document.activeElement === $('rIng'));

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