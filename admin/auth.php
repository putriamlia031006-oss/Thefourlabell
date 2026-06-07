<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/* Cek sudah login atau belum */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

/* Cek role harus admin */
if (!isset($_SESSION['role']) || strtolower(trim($_SESSION['role'])) != "admin") {
    header("Location: ../index.php");
    exit;
}
?>