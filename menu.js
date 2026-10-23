// พอโหลดหน้าเสร็จ ให้ไปดึงเมนูจาก API มาแสดงทันที
const esc = s => String(s ?? '').replace(/[&<>"']/g, c =>
  ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let menuSig = '';

async function loadMenu() {
    const menu = await (await fetch('menu_public_api.php')).json();
    const sig = JSON.stringify(menu);
    if (sig === menuSig) return;          // ไม่เปลี่ยน ไม่ต้องวาดใหม่
    menuSig = sig;

    // จำตัวเลือกที่ลูกค้าเลือกค้างไว้
    const temps = {}, sweets = {};
    document.querySelectorAll('input[name^="temperature-"]:checked').forEach(r => temps[r.name] = r.value);
    document.querySelectorAll('select[id^="sweetness-"]').forEach(s => sweets[s.id] = s.value);

    renderMenu('Drinks_menu',  menu.filter(i => i.category === 'เครื่องดื่ม'), true);
    renderMenu('Dessert_menu', menu.filter(i => i.category === 'ของหวาน'), false);
    renderMenu('Food_menu',    menu.filter(i => i.category === 'อาหาร'), false);

    // คืนค่าที่จำไว้
    Object.entries(temps).forEach(([name, v]) => {
        const r = document.querySelector(`input[name="${name}"][value="${v}"]`);
        if (r) r.checked = true;
    });
    Object.entries(sweets).forEach(([id, v]) => {
        const s = document.getElementById(id);
        if (s) s.value = v;
    });

    attachOrderButtons();
}
loadMenu();
autoRefresh(loadMenu, 20000);
 
function renderMenu(sectionId, items, hasDrinkOptions) {
    // หา container ของหมวดนั้น (div.row ที่อยู่ข้างในแต่ละ section)
    const container = document.querySelector(`#${sectionId} .row`);
    container.innerHTML = ''; // เคลียร์ของเดิมทิ้งก่อน (กันซ้ำกับที่พิมพ์ตายตัวไว้)
 
    items.forEach(item => {
        let optionsHtml = '';
 
        if (hasDrinkOptions) {
            // ถ้าเป็นเครื่องดื่ม ใส่ตัวเลือกร้อน/เย็น + ความหวาน
            optionsHtml = `
                <div class="temp-choice">
                    <label class="circle-option">
                        <input type="radio" name="temperature-${item.menu_id}" value="hot" checked>
                        <span class="label-text"><b>ร้อน ${item.price_hot} บาท</b></span>
                    </label>
                    <label class="circle-option">
                        <input type="radio" name="temperature-${item.menu_id}" value="cold">
                        <span class="label-text"><b>เย็น ${item.price_cold} บาท</b></span>
                    </label>
                </div>
                <label for="sweetness-${item.menu_id}">ความหวาน :</label>
                <select id="sweetness-${item.menu_id}" name="sweetness">
                    <option value="less">น้อย</option>
                    <option value="normal" selected>ปกติ</option>
                    <option value="extra">มาก</option>
                </select>
            `;
        }
 
        const priceText = item.price ? `${item.price} บาท` : '';
 
        const cardHtml = `
            <div class="col-6 col-md-4 col-lg-3 mb-3">
                <div class="card">
                    <img src="${esc(item.image_url)}" class="card-img-top" alt="${esc(item.name)}">
                    <div class="card-body">
                        <h5 class="card-title">${esc(item.name)}</h5>
                        <p class="card-text">${esc(item.description ?? '')}</p>
                        <p><b>${priceText}</b></p>
                        ${optionsHtml}
                        <div class="modal-footer">
                            <a class="btn btn-primary order-btn" data-id="${item.menu_id}">Order Now</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
 
        container.insertAdjacentHTML('beforeend', cardHtml);
    });
}

// ---------- ส่วนจัดการตะกร้า ----------
 
// ผูก event ให้ปุ่ม "Order Now" ทุกปุ่ม (ต้องเรียกหลังจากสร้างการ์ดเสร็จแล้ว)
function attachOrderButtons() {
    document.querySelectorAll('.order-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const menuId = this.dataset.id;
            const card = this.closest('.card');
 
            // ดึงชื่อเมนูจากการ์ด
            const name = card.querySelector('.card-title').textContent;
 
            // เช็คว่าการ์ดนี้มีตัวเลือกร้อน/เย็น+ความหวานไหม (เฉพาะเครื่องดื่ม)
            const tempInput = card.querySelector(`input[name="temperature-${menuId}"]:checked`);
            const sweetSelect = card.querySelector(`#sweetness-${menuId}`);
 
            let price = 0;
            let temperature = null;
            let sweetness = null;
 
            if (tempInput) {
                // เป็นเครื่องดื่ม: ราคาขึ้นกับร้อน/เย็นที่เลือก
                temperature = tempInput.value; // "hot" หรือ "cold"
                price = parseFloat(tempInput.closest('label').querySelector('.label-text b').textContent.replace(/[^0-9.]/g, ''));
                sweetness = sweetSelect ? sweetSelect.value : null;
            } else {
                // ไม่ใช่เครื่องดื่ม: ดึงราคาปกติจากข้อความในการ์ด
                const priceText = card.querySelector('p b').textContent;
                price = parseFloat(priceText.replace(/[^0-9.]/g, ''));
            }
 
            addToCart({ menu_id: menuId, name, price, qty: 1, temperature, sweetness });
        });
    });
}
 
// เพิ่มรายการลง localStorage
function addToCart(item) {
    // ดึงตะกร้าเดิมออกมาก่อน (ถ้ายังไม่มี ให้เป็น array ว่าง)
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
 
    cart.push(item);
 
    // บันทึกกลับเข้า localStorage (ต้องแปลงเป็น string ก่อนเพราะเก็บได้แค่ text)
    localStorage.setItem('cart', JSON.stringify(cart));
 
    renderCart();
    
    alert(`เพิ่ม "${item.name}" ลงตะกร้าแล้ว`);
}

// ---------- ดึงรายการโต๊ะมาใส่ dropdown ----------
async function loadTables() {
    const tables = await (await fetch('get_tables.php')).json();
    const sel = document.getElementById('table-id');
    const cur = sel.value;
    sel.innerHTML = '<option value="">-- เลือกโต๊ะ --</option>';
    tables.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t.table_id;
        opt.textContent = `โต๊ะ ${t.table_number} โซน${t.zone} (${t.seats} ที่นั่ง)` +
            (t.status !== 'available' ? ' - ไม่ว่าง' : '');
        sel.appendChild(opt);
    });
    sel.value = cur;
}
loadTables();
autoRefresh(loadTables, 20000, () => document.activeElement === document.getElementById('table-id'));