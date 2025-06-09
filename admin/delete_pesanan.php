<?php
require_once '../koneksi.php';

// Check for id_pesanan in either POST or GET
$id_pesanan = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pesanan'])) {
    $id_pesanan = $_POST['id_pesanan'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id_pesanan = $_GET['id'];
}

if ($id_pesanan !== null) {
    // Check if order exists
    $query = "SELECT COUNT(*) as count FROM pesanan WHERE id_pesanan = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_pesanan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] == 0) {
        echo json_encode(['success' => false, 'message' => 'Pesanan dengan ID tersebut tidak ditemukan']);
        exit();
    }
    
    // Mulai transaksi
    mysqli_begin_transaction($koneksi);
    
    try {
        // Hapus detail pesanan terlebih dahulu
        $query = "DELETE FROM detail_pesanan WHERE id_pesanan = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id_pesanan);
        mysqli_stmt_execute($stmt);
        $detail_deleted_rows = mysqli_stmt_affected_rows($stmt);
        
        // Hapus pesanan
        $query = "DELETE FROM pesanan WHERE id_pesanan = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id_pesanan);
        mysqli_stmt_execute($stmt);
        $pesanan_deleted_rows = mysqli_stmt_affected_rows($stmt);
        
        // Commit transaksi
        mysqli_commit($koneksi);
        
        if ($pesanan_deleted_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Pesanan berhasil dihapus', 'detail_rows' => $detail_deleted_rows, 'pesanan_rows' => $pesanan_deleted_rows]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tidak ada pesanan yang dihapus, ID tidak ditemukan']);
            mysqli_rollback($koneksi);
        }
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($koneksi);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
