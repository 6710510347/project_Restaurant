<?php
// group = ชื่อ role ตรง ๆ: 'customer' | 'kitchen' | 'admin'
function start_role_session(string $group): void {
    $allowed = ['customer', 'kitchen', 'admin'];
    if (!in_array($group, $allowed, true)) $group = 'customer';
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    session_name('PPG_' . strtoupper($group));
    session_start();
}