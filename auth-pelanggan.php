<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/* Kalau belum login, halaman tetap boleh dibuka */
if (!isset($_SESSION['user'])) {
    return;
}

$role = "";

if (isset($_SESSION['role'])) {
    $role = strtolower(trim($_SESSION['role']));
} elseif (isset($_SESSION['user']['role'])) {
    $role = strtolower(trim($_SESSION['user']['role']));
}

/* Kalau admin masuk web pelanggan, lempar ke dashboard admin */
if ($role == "admin") {
    header("Location: admin/index.php");
    exit;
}
?>