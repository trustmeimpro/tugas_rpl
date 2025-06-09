<?php
require_once '../koneksi.php';

$id = $_GET['id'];
$query = "SELECT * FROM barang WHERE id_barang = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$barang = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode($barang);
?>
