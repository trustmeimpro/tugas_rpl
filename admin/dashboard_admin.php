<?php
require_once '../koneksi.php';
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login_form.php");
    exit();
}

$barang = [];
$query = "SELECT * FROM barang";
$result = mysqli_query($koneksi, $query);
while($row = mysqli_fetch_assoc($result)) {
    $barang[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .admin-table th, .admin-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .admin-table th {
            background-color: #f2f2f2;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .edit-btn:hover {
            background-color: #45a049;
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
            width: 50%;
            max-width: 500px;
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        .modal-footer button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .modal-footer button:hover {
            background-color: #45a049;
        }

        .modal-footer button[type='button'] {
            background-color: #f44336;
            margin-right: 10px;
        }

        .modal-footer button[type='button']:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
<?php include 'sidebar_admin.php'; ?>
    
    <div class="main-content">
        <h1>Manajemen Barang</h1>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($barang as $item): ?>
                <tr>
                    <td><?= $item['id_barang'] ?></td>
                    <td><?= $item['nama_barang'] ?></td>
                    <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td><?= $item['stok'] ?></td>
                    <td>
                        <button class="edit-btn" data-id="<?= $item['id_barang'] ?>">Edit</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Barang</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editId" name="id">
                    <div class="form-group">
                        <label for="editNama">Nama Barang:</label>
                        <input type="text" id="editNama" name="nama_barang" required>
                    </div>
                    <div class="form-group">
                        <label for="editHarga">Harga:</label>
                        <input type="number" id="editHarga" name="harga" required>
                    </div>
                    <div class="form-group">
                        <label for="editStok">Stok:</label>
                        <input type="number" id="editStok" name="stok" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()">Batal</button>
                <button type="submit" form="editForm">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <script>
        // JavaScript untuk modal edit
        const editModal = document.getElementById('editModal');
        const closeBtn = document.querySelector('.close');
        const editForm = document.getElementById('editForm');
        
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                
                // Ambil data barang via AJAX
                fetch(`get_barang.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('editId').value = data.id_barang;
                        document.getElementById('editNama').value = data.nama_barang;
                        document.getElementById('editHarga').value = data.harga;
                        document.getElementById('editStok').value = data.stok;
                        editModal.style.display = 'block';
                        editModal.classList.add('show');
                    });
            });
        });
        
        closeBtn.addEventListener('click', closeModal);
        
        function closeModal() {
            editModal.classList.remove('show');
            setTimeout(() => {
                editModal.style.display = 'none';
            }, 300);
        }
        
        window.addEventListener('click', function(event) {
            if (event.target === editModal) {
                closeModal();
            }
        });
        
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('update_barang.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Data berhasil diperbarui!');
                    location.reload();
                } else {
                    alert('Gagal memperbarui data: ' + data.error);
                }
            });
        });
    </script>
</body>
</html>