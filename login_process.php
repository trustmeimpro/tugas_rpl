<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Logging untuk debugging
    error_log("Login attempt - Username: $username, Password: $password");
    
    // Cek di tabel admin
    $query = "SELECT * FROM login_admin WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    error_log("Query executed. Number of rows: " . mysqli_num_rows($result));
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = 'admin';
        header("Location: admin/dashboard_admin.php");
        exit();
    } else {
        // Cek apakah username ada
        $query = "SELECT * FROM login_admin WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            error_log("Username found, password mismatch");
            echo "<script>alert('Password salah!'); window.location.href = 'login_form.php';</script>";
        } else {
            error_log("Username not found");
            echo "<script>alert('Username tidak ditemukan!'); window.location.href = 'login_form.php';</script>";
        }
    }
}
?>
