// ต้องประกาศไว้บนสุด เพราะ renderCart() ด้านล่างถูกเรียกทันทีตอนโหลดไฟล์
let coupons = [];   // คูปองที่ลูกค้ายังไม่ได้ใช้ (ดึงจาก member_api.php?action=coupons)

  // พอเปิดหน้ามาปุ๊บ ดึงตะกร้าจาก localStorage มาแสดงทันที
renderCart();

flatpickr('#pickup-time', {
    enableTime: true,
    time_24hr: true,
    dateFormat: 'Y-m-d\\TH:i',
    altInput: true,
    altFormat: 'd/m/Y H:i น.',
    locale: 'th',
    minDate: 'today',
    minuteIncrement: 5
});
 
function renderCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const container = document.getElementById('cart-list');
    container.innerHTML = '';
 
    if (cart.length === 0) {
        container.innerHTML = '<p class="text-muted">ยังไม่มีรายการในตะกร้า</p>';
        document.getElementById('cart-total').textContent = '0';
        updateCouponSummary(cart, 0);
        return;
    }
 
    let total = 0;
 
    cart.forEach((item, index) => {
        total += item.price * item.qty;
        
        const sweetMap = { less: 'น้อย', normal: 'ปกติ', extra: 'มาก'};

        const optionText = item.temperature
            ? `(${item.temperature === 'hot' ? 'ร้อน' : 'เย็น'}, หวาน${sweetMap[item.sweetness] ?? ''})`
            : '';
 
        const row = document.createElement('div');
        row.className = 'd-flex justify-content-between align-items-center border-bottom py-2';
        row.innerHTML = `

        <div>
            <b>${item.name}</b> ${optionText}
            <br><span class="text-muted">${item.price} บาท x ${item.qty}</span>
        </div>
        <button class="btn btn-sm btn-outline-danger remove-btn" data-index="${index}">ลบ</button>
        `;
        container.appendChild(row);
    });
 
    document.getElementById('cart-total').textContent = total;
    updateCouponSummary(cart, total);
 
    // ผูกปุ่มลบให้ทำงาน
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            removeFromCart(parseInt(this.dataset.index));
        });
    });
}
 
// ---------- คูปอง ----------

// คำนวณส่วนลดเพื่อ "แสดงตัวอย่าง" เท่านั้น ตัวจริงคำนวณที่เซิร์ฟเวอร์ใน order_api.php
function calcDiscount(cart, total, coupon) {
    if (!coupon) return 0;
    if (coupon.type === 'percent') return Math.round(total * Number(coupon.value)) / 100;
    if (coupon.type === 'fixed') return Math.min(Number(coupon.value), total);
    if (coupon.type === 'free_item') {
        // เครื่องดื่มคือรายการที่มี temperature
        return cart.reduce((best, it) => it.temperature ? Math.max(best, Number(it.price)) : best, 0);
    }
    return 0; // gift: ไม่มีส่วนลด พนักงานหยิบของให้
}

function getSelectedCoupon() {
    const id = document.getElementById('coupon-id').value;
    return coupons.find(c => String(c.id) === id) || null;
}

function updateCouponSummary(cart, total) {
    const coupon = getSelectedCoupon();
    const discount = Math.min(calcDiscount(cart, total, coupon), total);
    const hint = document.getElementById('coupon-hint');

    hint.textContent = '';
    if (coupon && coupon.type === 'gift') {
        hint.textContent = 'รับของรางวัลจากพนักงานเมื่อสั่งออเดอร์นี้ (ไม่มีผลต่อราคา)';
    } else if (coupon && coupon.type === 'free_item' && discount === 0) {
        hint.textContent = 'ต้องมีเครื่องดื่มอย่างน้อย 1 แก้วในตะกร้าจึงจะใช้คูปองนี้ได้';
    }

    document.getElementById('discount-row').style.display = discount > 0 ? 'block' : 'none';
    document.getElementById('net-row').style.display = discount > 0 ? 'block' : 'none';
    document.getElementById('cart-discount').textContent = discount;
    document.getElementById('cart-net').textContent = Math.max(total - discount, 0);
}

function loadCoupons() {
    return fetch('member_api.php?action=coupons')
        .then(res => res.json())
        .then(json => {
            coupons = json.success ? json.data : [];
        })
        .catch(() => { coupons = []; })
        .then(() => {
            const select = document.getElementById('coupon-id');
            const prev = select.value;   // จำคูปองที่เลือกไว้
            select.innerHTML = '<option value="">-- ไม่ใช้คูปอง --</option>';
            coupons.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;   // textContent กัน XSS
                select.appendChild(opt);
            });
            if (coupons.some(c => String(c.id) === prev)) select.value = prev;
            // ไม่มีคูปองก็ซ่อนช่องนี้ไปเลย
            document.getElementById('coupon-wrapper').style.display = coupons.length ? 'block' : 'none';
            renderCart();
        });
}

document.getElementById('coupon-id').addEventListener('change', renderCart);
loadCoupons();
autoRefresh(loadCoupons, 30000, () => document.activeElement?.id === 'coupon-id');

// ลบรายการออกจากตะกร้า ตาม index ที่กด
function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1); // ลบ 1 รายการตรงตำแหน่งนี้ออก
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart(); // แสดงผลใหม่หลังลบ
}
 
// กดยืนยันสั่งอาหาร
// ใหม่
document.getElementById('checkout-btn').addEventListener('click', function () {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    if (cart.length === 0) {
        alert('ตะกร้าว่างอยู่ กรุณาเลือกเมนูก่อน');
        return;
    }

    const orderType = document.getElementById('order-type').value;
    const tableId = document.getElementById('table-id').value;

    // validate ฝั่งหน้าบ้านก่อนยิง request (กันเคสชัดๆ ไว้ก่อน)
    if (orderType === 'dine_in_walkin' && !tableId) {
        alert('กรุณาเลือกหมายเลขโต๊ะ');
        return;
    }

    fetch('order_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            cart,        customer_name: document.getElementById('customer-name').value.trim(),
            order_type: orderType,
            table_id: orderType === 'dine_in_walkin' ? tableId : null,
            pickup_time: document.getElementById('pickup-time').value || null,
            redemption_id: document.getElementById('coupon-id').value || null
        })
    })
        .then(res => res.json())
        // ใหม่
        .then(data => {
            const resultBox = document.getElementById('order-result');

            if (data.error) {
                resultBox.innerHTML = `<div class="alert alert-danger mb-0">${data.error}</div>`;
                return;
            }

            const paid = data.discount > 0
                ? `<br>ส่วนลด ${data.discount} บาท ยอดที่ต้องชำระ ${data.total} บาท`
                : '';
            resultBox.innerHTML = `<div class="alert alert-success mb-0">สั่งอาหารสำเร็จ! หมายเลขออเดอร์ #${data.order_id}${paid}</div>`;
            localStorage.removeItem('cart'); // เคลียร์ตะกร้าทิ้งหลังสั่งสำเร็จ
            loadCoupons();                   // คูปองที่ใช้แล้วจะหายจากรายการ (และ renderCart ใหม่ให้)
        })
        .catch(err => {
            console.error(err);
            alert('เกิดข้อผิดพลาด กรุณาลองใหม่');
        });
});

document.getElementById('order-type').addEventListener('change', function () {
    document.getElementById('pickup-time-wrapper').style.display =
        this.value === 'pre_order' ? 'block' : 'none';
    document.getElementById('table-wrapper').style.display =
        this.value === 'dine_in_walkin' ? 'block' : 'none';
});

// เรียกครั้งแรกตอนโหลดหน้า เผื่อ default เป็น dine_in_walkin อยู่แล้ว
document.getElementById('order-type').dispatchEvent(new Event('change'));