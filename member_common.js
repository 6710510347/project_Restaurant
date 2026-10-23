// ฟังก์ชันที่ใช้ร่วมกันในหน้า membership.php และ usedpoint.php

// ป้องกัน XSS ตอนเอาข้อมูลจาก DB ใส่ innerHTML
function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

function fmtDate(s) {
    if (!s) return '-';
    const d = new Date(String(s).replace(' ', 'T'));
    return isNaN(d) ? '-' : d.toLocaleString('th-TH', { dateStyle: 'medium', timeStyle: 'short' });
}

// เรียก API; ถ้ายังไม่ได้ล็อกอินให้เด้งไปหน้า Login
async function api(url, data) {
    const opts = data ? {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data).toString()
    } : {};
    const res = await fetch(url, opts);
    if (res.status === 401 || res.status === 403) {
        window.location.href = 'Login-Signin.html';
        throw new Error('unauthorized');
    }
    return res.json();
}

async function loadProfile() {
    const json = await api('member_api.php?action=profile');
    if (!json.success) throw new Error(json.message);
    document.getElementById('member-name').textContent = json.data.name;
    document.getElementById('points').textContent = Number(json.data.points).toLocaleString('th-TH');
    return json.data.points;
}

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('logout-btn');
    if (btn) btn.addEventListener('click', e => {
        e.preventDefault();
        fetch('auth_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=logout&group=customer'
        }).then(() => { window.location.href = 'Login-Signin.html'; });
    });
});