<?php
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pesanan'])) {
    $id_pesanan = $_POST['id_pesanan'];
    
    // Mulai transaksi
    mysqli_begin_transaction($koneksi);
    
    try {
        // Hapus detail pesanan terlebih dahulu
        $query = "DELETE FROM detail_pesanan WHERE id_pesanan = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id_pesanan);
        mysqli_stmt_execute($stmt);
        
        // Hapus pesanan
        $query = "DELETE FROM pesanan WHERE id_pesanan = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id_pesanan);
        mysqli_stmt_execute($stmt);
        
        // Commit transaksi
        mysqli_commit($koneksi);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($koneksi);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
