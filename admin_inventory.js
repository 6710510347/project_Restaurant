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
  e.preventDefault();
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
    $('ingDlg').showModal?.(); 
    loadIngredients();
    if (currentMenu) loadRecipe();
  } catch (_) {  }
    
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

// รีเฟรชสต็อกอัตโนมัติ (ข้ามรอบถ้า popup เปิดอยู่ หรือกำลังพิมพ์/เลือกในตารางสต็อก)
autoRefresh(loadIngredients, 15000, () =>
  $('ingDlg').open ||
  document.activeElement?.closest('#ingBody') ||
  document.activeElement === $('rIng'));
 
// ---- ออกจากระบบ ----

document.getElementById('admin-logout-btn').addEventListener('click', function (e) {
    e.preventDefault();
    fetch('auth_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=logout&group=kitchen'
    }).then(() => { window.location.href = 'Login-Signin.html'; });
});
 