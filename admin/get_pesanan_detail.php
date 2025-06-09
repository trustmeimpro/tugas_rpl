<?php
require_once '../koneksi.php';

if (isset($_GET['id'])) {
    $id_pesanan = $_GET['id'];
    
    // Ambil data pesanan
    $query = "SELECT p.*, SUM(dp.harga_satuan * dp.jumlah) as total_harga FROM pesanan p LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan WHERE p.id_pesanan = ? GROUP BY p.id_pesanan";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_pesanan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $pesanan = mysqli_fetch_assoc($result);
    
    // Ambil detail pesanan
    $query = "SELECT dp.*, b.nama_barang 
              FROM detail_pesanan dp 
              JOIN barang b ON dp.id_barang = b.id_barang 
              WHERE dp.id_pesanan = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_pesanan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $detail = [];
    while($row = mysqli_fetch_assoc($result)) {
        $detail[] = $row;
    }
    
    echo json_encode([
        'pesanan' => $pesanan,
        'detail' => $detail
    ]);
}
?>
