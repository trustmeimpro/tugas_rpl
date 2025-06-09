<?php
// Memanggil file koneksi
require_once 'koneksi.php';

// Cek koneksi
if ($koneksi) {
    echo "Koneksi ke database berhasil!";
    
    // Mencoba menjalankan query sederhana
    $query = "SHOW TABLES";
    $result = jalankanQuery($koneksi, $query);
    
    if ($result) {
        echo "<br>Tabel dalam database pemesananbarcode:<br>";
        if (hitungBaris($result) > 0) {
            while ($row = ambilData($result)) {
                echo "- " . $row['Tables_in_pemesananbarcode'] . "<br>";
            }
        } else {
            echo "Database kosong, belum ada tabel.";
        }
    } else {
        echo "<br>Gagal menjalankan query: " . mysqli_error($koneksi);
    }
    
    // Menutup koneksi
    tutupKoneksi($koneksi);
} else {
    echo "Koneksi ke database gagal!";
}
?> 