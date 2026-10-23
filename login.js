// ---------- ส่วนของหน้า Signin (สมัครสมาชิก) ----------
const signinForm = document.getElementById('SigninForm');

if (signinForm) {
    signinForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const tel = document.getElementById('tel').value.trim();
        const password = document.getElementById('password').value;

        if (!/^[0-9]{10}$/.test(tel)) {
            alert('กรุณากรอกเบอร์โทรให้ครบ 10 หลัก');
            return;
        }

        if (password.length < 8) {
            alert('รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร');
            return;
        }

        const formData = new URLSearchParams();
        formData.append('action', 'register');
        formData.append('username', document.getElementById('username').value.trim());
        formData.append('tel', tel);
        formData.append('password', password);

        fetch('auth_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                window.location.href = 'membership.php';
            })
            .catch(() => alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ ลองใหม่อีกครั้ง'));  // <-- ย้ายมาตรงนี้
    });
}

// ---------- ส่วนของหน้า Login ----------
const loginForm = document.getElementById('loginForm');

if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new URLSearchParams();
        formData.append('action', 'login');
        formData.append('username', document.getElementById('username').value.trim());
        formData.append('password', document.getElementById('password').value);

        fetch('auth_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                if (data.role === 'admin') {
                    window.location.href = 'admin.php';
                } else if (data.role === 'kitchen') {
                    window.location.href = 'kitchen.php';
                } else {
                    window.location.href = 'membership.php';
                }
            })
            .catch(() => alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ ลองใหม่อีกครั้ง'));
    }); 
} 