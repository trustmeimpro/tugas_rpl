<?php
session_start();

// Hapus semua data sesi
$_SESSION = array();
session_destroy();

// Arahkan kembali ke halaman login
header("Location: login_form.php");
exit();
?>
