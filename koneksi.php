<?php
// Konfigurasi Database
$host = "localhost";
$username = "root";
$password = "root";
$database = "pemesananbarcode";

// Membuat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Memeriksa koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

