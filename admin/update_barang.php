<?php
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    $query = "UPDATE barang SET 
                nama_barang = ?,
                harga = ?,
                stok = ?
              WHERE id_barang = ?";
              
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'siii', $nama, $harga, $stok, $id);
    
    if(mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($koneksi)]);
    }
}
?>
