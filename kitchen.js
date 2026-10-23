// ---- admin_orders.js ----

let currentFilter = '';

// ป้องกัน XSS ตอนเอาข้อมูลจาก DB ใส่ innerHTML
function esc(v) {
    return String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

document.addEventListener('DOMContentLoaded', function () {
    loadOrders();
    autoRefresh(loadOrders, 5000);
});
// ---- ออกจากระบบ ----
document.getElementById('admin-logout-btn').addEventListener('click', function (e) {
    e.preventDefault();
    fetch('auth_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=logout&group=kitchen'
    }).then(() => { window.location.href = 'Login-Signin.html'; });
});

// ---- กด filter สถานะ ----
document.querySelectorAll('.status-filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.status-filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.status;
        loadOrders();
    });
});

// ---- โหลดรายการออเดอร์ ----
function loadOrders() {
    const url = currentFilter
        ? `order_api.php?action=list&status=${currentFilter}`
        : `order_api.php?action=list`;

    return fetch(url)
        .then(res => {
            if (res.status === 403) { window.location.href = 'Login-Signin.html'; throw new Error('unauthorized'); }
            return res.json();
        })
        .then(data => renderOrders(data))
        .catch(error => console.error('โหลดออเดอร์ไม่สำเร็จ:', error));
}

// ---- แผนที่ label/สี ----
const statusLabels = {
    pending: 'รอดำเนินการ',
    preparing: 'กำลังเตรียม',
    ready: 'พร้อมส่ง',
    completed: 'เสร็จสิ้น',
    cancelled: 'ยกเลิก'
};
const statusColors = {
    pending: '#FF9800',
    preparing: '#ebeb00',
    ready: '#0dcaf0',
    completed: '#198754',
    cancelled: '#dc3545'
};
const nextStatus = {
    pending: 'preparing',
    preparing: 'ready',
    ready: 'completed'
};

const orderTypeLabels = {
    dine_in_walkin: 'ทานที่ร้าน',
    takeaway: 'รับกลับบ้าน',
    pre_order: 'สั่งล่วงหน้า',
    dine_in_reserved: 'จองโต๊ะล่วงหน้า'
};

const orderTypeColors = {
    dine_in_walkin: '#a7626b',   // ทานที่ร้าน
    takeaway: '#a7626b',         // รับกลับบ้าน
    pre_order: '#a7626b',        // สั่งล่วงหน้า
    dine_in_reserved: '#795138'  // จองโต๊ะล่วงหน้า
};

const nextLabel = {
    pending: 'เริ่มเตรียม',
    preparing: 'พร้อมส่งแล้ว',
    ready: 'ส่งมอบแล้ว'
};

// ---- วาดรายการออเดอร์ ----
function renderOrders(orders) {
    const container = document.getElementById('orders-list');

    if (orders.length === 0) {
        container.innerHTML = '<p class="text-muted">ไม่มีออเดอร์ในสถานะนี้</p>';
        return;
    }

    container.innerHTML = '';
    orders.forEach(order => {
        const div = document.createElement('div');
        div.className = 'card p-3 mb-2';

        const canAdvance = nextStatus[order.status];
        const canCancel = order.status === 'pending';

        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <p class="mb-1 fw-bold">
                        ออเดอร์ #${order.id}
                        <span class="badge" style="background-color:${statusColors[order.status]}; color:#fff;">${statusLabels[order.status]}</span>                        <span class="badge" style="background-color:${orderTypeColors[order.order_type] ?? '#ffffff'}; color:#fff;">${orderTypeLabels[order.order_type] ?? order.order_type}</span>
                        ${order.coupon_name ? `<span class="badge" style="background-color:#ab92bf; color:#fff;">ใช้คูปอง: ${esc(order.coupon_name)}</span>` : ''}
                    </p>
                    <p class="mb-0 text-muted">
                        ${Number(order.discount_amount) > 0
                            ? `ยอดสุทธิ ${order.total_price} บาท (ลด ${order.discount_amount} บาท)`
                            : `ยอดรวม ${order.total_price} บาท`}
                        ${order.pickup_time ? ' — รับเวลา ' + order.pickup_time : ''}
                        — สั่งเมื่อ ${order.order_date}
                    </p>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary detail-btn me-1" data-id="${order.id}">ดูรายละเอียด</button>
                    ${canAdvance ? `<button class="btn btn-sm advance-btn me-1" data-id="${order.id}" data-next="${canAdvance}"
                        style="background-color:#6b2c3e; color:#faf6f0;">${nextLabel[order.status]}</button>` : ''}
                    ${canCancel ? `<button class="btn btn-sm btn-outline-danger cancel-btn" data-id="${order.id}">ยกเลิก</button>` : ''}
                </div>
            </div>
        `;
        container.appendChild(div);
    });

    document.querySelectorAll('.detail-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            showOrderDetail(this.dataset.id);
        });
    });
    document.querySelectorAll('.advance-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            updateOrderStatus(this.dataset.id, this.dataset.next);
        });
    });
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (confirm(`ยืนยันยกเลิกออเดอร์ #${this.dataset.id} ใช่ไหม?`)) {
                updateOrderStatus(this.dataset.id, 'cancelled');
            }
        });
    });
}

// ---- แสดงรายละเอียดออเดอร์ใน modal ----
function showOrderDetail(orderId) {
    fetch(`order_api.php?action=detail&id=${orderId}`)
        .then(response => response.json())
        .then(order => {
            const body = document.getElementById('order-detail-body');
            const itemRows = order.items.map(item => `
                <tr>
                    <td>${item.menu_name}${formatDrinkOptions(item)}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price_at_order} บาท</td>
                    <td>${item.quantity * item.price_at_order} บาท</td>
                </tr>
            `).join('');

            body.innerHTML = `
                <p><strong>ออเดอร์ #${order.id}</strong></p>
                <p>สถานะ: <span class="badge" style="background-color:${statusColors[order.status]}; color:#fff;">${statusLabels[order.status]}</span></p>
                <p>ประเภท: ${orderTypeLabels[order.order_type] ?? order.order_type}</p>
                ${order.table_number ? `<p>โต๊ะ: ${esc(order.table_number)} โซน${esc(order.zone)}</p>` : ''}
                ${order.guest_name ? `<p>ชื่อผู้สั่ง: ${esc(order.guest_name)}</p>` : ''}
                ${order.pickup_time ? `<p>เวลารับ: ${esc(order.pickup_time)}</p>` : ''}
                <p>วันที่สั่ง: ${order.order_date}</p>
                ${order.coupon_name ? `
                <div class="alert alert-success py-2">
                    <strong>ลูกค้าใช้คูปอง:</strong> ${esc(order.coupon_name)}
                    ${order.coupon_type === 'gift' ? '<br>👉 พนักงานหยิบของรางวัลให้ลูกค้า (ไม่มีผลต่อราคา)' : ''}
                    ${order.coupon_type === 'free_item' ? '<br>👉 เครื่องดื่ม 1 แก้วในออเดอร์นี้ฟรี (หักราคาแล้ว)' : ''}
                </div>` : ''}
                <hr>
                <table class="table table-sm">
                    <thead>
                        <tr><th>เมนู</th><th>จำนวน</th><th>ราคา/หน่วย</th><th>รวม</th></tr>
                    </thead>
                    <tbody>${itemRows}</tbody>
                </table>
                ${Number(order.discount_amount) > 0 ? `
                <p class="text-end mb-1">ยอดก่อนลด: ${order.subtotal} บาท</p>
                <p class="text-end mb-1 text-success">ส่วนลด (${esc(order.coupon_name)}): -${order.discount_amount} บาท</p>
                <p class="text-end fw-bold">ยอดสุทธิที่ต้องชำระ: ${order.total_price} บาท</p>`
                : `<p class="text-end fw-bold">ยอดรวมทั้งหมด: ${order.total_price} บาท</p>`}
            `;

            const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
            modal.show();
        })
        .catch(error => console.error('โหลดรายละเอียดไม่สำเร็จ:', error));
}

// ---- เปลี่ยนสถานะออเดอร์ ----
function updateOrderStatus(orderId, newStatus) {
    const formData = new URLSearchParams();
    formData.append('action', 'update_status');
    formData.append('id', orderId);
    formData.append('status', newStatus);

    fetch('order_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('เกิดข้อผิดพลาด: ' + data.error);
                return;
            }
            loadOrders();
        })
        .catch(() => alert('เกิดข้อผิดพลาด กรุณาลองใหม่'));
}

function formatDrinkOptions(item) {
    if (!item.temperature) return '';
    const sweetMap = { less: 'น้อย', normal: 'ปกติ', extra: 'มาก' };
    const tempText = item.temperature === 'hot' ? 'ร้อน' : 'เย็น';
    const sweetText = sweetMap[item.sweetness] ?? '';
    return ` <br><span class="text-muted small">(${tempText}, หวาน${sweetText})</span>`;
}