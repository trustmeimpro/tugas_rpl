<?php
require_once '../koneksi.php';
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login_form.php");
    exit();
}

// Ambil data pesanan dari database
$pesanan = [];
$query = "SELECT p.id_pesanan, p.nama_pemesan, SUM(dp.harga_satuan * dp.jumlah) as total_harga, p.tanggal_pesanan, COUNT(dp.id_barang) as jumlah_item
          FROM pesanan p
          LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
          GROUP BY p.id_pesanan
          ORDER BY p.tanggal_pesanan DESC";
$result = mysqli_query($koneksi, $query);
while($row = mysqli_fetch_assoc($result)) {
    $pesanan[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .pesanan-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .pesanan-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .pesanan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .pesanan-card h3 {
            margin: 0 0 10px;
            font-size: 1.2em;
            color: #333;
        }

        .pesanan-card p {
            margin: 5px 0;
            color: #666;
        }

        .pesanan-card .detail-btn {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 3px;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .pesanan-card .detail-btn:hover {
            background-color: #1976D2;
        }

        .pesanan-card .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 3px;
            margin-top: 10px;
            margin-left: 10px;
            transition: background-color 0.3s ease;
        }

        .pesanan-card .delete-btn:hover {
            background-color: #da190b;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .modal.show {
            opacity: 1;
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 0;
            border-radius: 8px;
            width: 60%;
            max-width: 700px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: scale(0.9);
            transition: transform 0.3s ease-in-out;
        }

        .modal.show .modal-content {
            transform: scale(1);
        }

        .modal-header, .modal-footer {
            padding: 10px 20px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ddd;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .modal-footer {
            border-top: none;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            text-align: right;
        }

        .modal-body {
            padding: 20px;
            max-height: 500px;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #000;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .detail-table th, .detail-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .detail-table th {
            background-color: #f2f2f2;
        }

        .modal-footer button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .modal-footer button:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
    <?php include 'sidebar_admin.php'; ?>
    
    <div class="main-content">
        <h1>Kelola Pesanan</h1>
        
        <div class="pesanan-container">
            <?php foreach($pesanan as $order): ?>
            <div class="pesanan-card">
                <h3>Pesanan #<?php echo $order['id_pesanan']; ?></h3>
                <p><strong>Nama Pemesan:</strong> <?php echo $order['nama_pemesan']; ?></p>
                <p><strong>Tanggal:</strong> <?php echo date('d-m-Y H:i', strtotime($order['tanggal_pesanan'])); ?></p>
                <p><strong>Total Harga:</strong> Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></p>
                <p><strong>Jumlah Item:</strong> <?php echo $order['jumlah_item']; ?></p>
                <button class="detail-btn" data-id="<?php echo $order['id_pesanan']; ?>">Lihat Detail</button>
                <button class="delete-btn" data-id="<?php echo $order['id_pesanan']; ?>">Hapus</button>
            </div>
            <?php endforeach; ?>
            <?php if(empty($pesanan)): ?>
            <p>Tidak ada pesanan yang ditemukan.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Detail Pesanan -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detail Pesanan</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Konten detail akan diisi melalui AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal konfirmasi hapus pesanan -->
    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Konfirmasi Hapus Pesanan</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeConfirmDeleteModal()">Batal</button>
                <button type="button" id="confirmDeleteButton">Hapus</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        // JavaScript untuk modal detail
        const detailModal = document.getElementById('detailModal');
        const closeDetailBtn = detailModal.querySelector('.close');
        
        document.querySelectorAll('.detail-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                
                // Ambil detail pesanan via AJAX
                fetch(`get_pesanan_detail.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        const detailContent = document.getElementById('detailContent');
                        let totalHarga = 0;
                        detailContent.innerHTML = `
                            <p><strong>Nama Pemesan:</strong> ${data.pesanan.nama_pemesan}</p>
                            <p><strong>Tanggal Pesanan:</strong> ${new Date(data.pesanan.tanggal_pesanan).toLocaleString('id-ID')}</p>
                            <h3>Item Pesanan</h3>
                            <table class="detail-table">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        
                        data.detail.forEach(item => {
                            const subtotal = item.harga_satuan * item.jumlah;
                            totalHarga += subtotal;
                            detailContent.innerHTML += `
                                <tr>
                                    <td>${item.nama_barang}</td>
                                    <td>${item.jumlah}</td>
                                    <td>Rp ${parseFloat(item.harga_satuan).toLocaleString('id-ID')}</td>
                                    <td>Rp ${parseFloat(subtotal).toLocaleString('id-ID')}</td>
                                </tr>
                            `;
                        });
                        
                        detailContent.innerHTML += `
                                </tbody>
                            </table>
                            <p><strong>Total Harga:</strong> Rp ${parseFloat(totalHarga).toLocaleString('id-ID')}</p>
                        `;
                        
                        detailModal.style.display = 'block';
                        detailModal.classList.add('show');
                    });
            });
        });
        
        closeDetailBtn.addEventListener('click', closeDetailModal);
        
        function closeDetailModal() {
            detailModal.classList.remove('show');
            setTimeout(() => {
                detailModal.style.display = 'none';
            }, 300);
        }
        
        window.addEventListener('click', function(event) {
            if (event.target === detailModal) {
                closeDetailModal();
            }
        });

        // JavaScript untuk modal konfirmasi hapus
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        const closeConfirmDeleteBtn = confirmDeleteModal.querySelector('.close');
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                confirmDeleteButton.setAttribute('data-id', id);
                confirmDeleteModal.style.display = 'block';
                confirmDeleteModal.classList.add('show');
            });
        });
        
        closeConfirmDeleteBtn.addEventListener('click', closeConfirmDeleteModal);
        
        function closeConfirmDeleteModal() {
            confirmDeleteModal.classList.remove('show');
            setTimeout(() => {
                confirmDeleteModal.style.display = 'none';
            }, 300);
        }
        
        window.addEventListener('click', function(event) {
            if (event.target === confirmDeleteModal) {
                closeConfirmDeleteModal();
            }
        });

        // JavaScript untuk menghapus pesanan
        confirmDeleteButton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`delete_pesanan.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pesanan berhasil dihapus.');
                        location.reload();
                    } else {
                        alert('Gagal menghapus pesanan: ' + data.message);
                    }
                    closeConfirmDeleteModal();
                });
        });
    </script>
</body>
</html>