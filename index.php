<?php
require_once 'koneksi.php';

$menu_items = [];
$query = "SELECT * FROM barang";
$result = mysqli_query($koneksi, $query);
while($row = mysqli_fetch_assoc($result)) {
    $menu_items[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_order') {
    $nama_pemesan = $_POST['nama_pemesan'];
    $order_data = json_decode($_POST['order_data'], true);
    $total_harga = 0;
    $order_valid = true;
    $error_message = '';

    // Validasi stok
    foreach ($order_data as $item) {
        $id_barang = $item['id'];
        $qty = $item['qty'];
        $query = "SELECT stok FROM barang WHERE id_barang = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id_barang);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $barang = mysqli_fetch_assoc($result);

        if ($barang['stok'] < $qty) {
            $order_valid = false;
            $error_message = "Stok tidak cukup untuk item ID {$id_barang}";
            break;
        }
        $total_harga += $item['price'] * $qty;
    }

    if ($order_valid) {
        // Simpan pesanan ke tabel pesanan
        $query = "INSERT INTO pesanan (nama_pemesan, tanggal_pesanan) VALUES (?, NOW())";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 's', $nama_pemesan);
        mysqli_stmt_execute($stmt);
        $id_pesanan = mysqli_insert_id($koneksi);

        // Simpan detail pesanan
        foreach ($order_data as $item) {
            $id_barang = $item['id'];
            $qty = $item['qty'];
            $harga_satuan = $item['price'];

            $query = "INSERT INTO detail_pesanan (id_pesanan, id_barang, jumlah, harga_satuan) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, 'iiid', $id_pesanan, $id_barang, $qty, $harga_satuan);
            mysqli_stmt_execute($stmt);

            // Update stok
            $query = "UPDATE barang SET stok = stok - ? WHERE id_barang = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, 'ii', $qty, $id_barang);
            mysqli_stmt_execute($stmt);
        }

        echo json_encode(['success' => true, 'message' => 'Pesanan berhasil disimpan', 'id_pesanan' => $id_pesanan]);
    } else {
        echo json_encode(['success' => false, 'message' => $error_message]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Beverages Section -->
        <div class="menu-section">
            <div class="section-title drinks">
                <h2>ANEKA MINUMAN</h2>
            </div>
            
            <div class="menu-items">
                <!-- Teh Panas -->
                <div class="menu-item">
                    <img src="assets/images/teh_panas.jpg" alt="Teh Panas">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Teh Panas</div>
                        <div class="menu-item-desc">
                            Teh kental manis panas dengan aroma melati yang wangi dan lembut akan memberi efek relaksasi pada otak dan pikiran anda.
                        </div>
                        <div class="menu-item-price">Rp 5.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="6" data-price="5000" data-name="Teh Panas">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Kopi Rempah -->
                <div class="menu-item">
                    <img src="assets/images/kopi_rempah.jpg" alt="Kopi Rempah">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Kopi Rempah</div>
                        <div class="menu-item-desc">
                            Kopi robusta ijen dengan campuran rempah-rempah pilihan membuat citarasa kopi ini begitu spesial.
                        </div>
                        <div class="menu-item-price">Rp 10.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="7" data-price="10000" data-name="Kopi Rempah">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Coklat Panas -->
                <div class="menu-item">
                    <img src="assets/images/coklat_panas.jpg" alt="Coklat Panas">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Coklat Panas</div>
                        <div class="menu-item-desc">
                            Coklat panas jawa dengan cream yang lembut sungguh menciptakan citarasa coklat yang tiada duanya.
                        </div>
                        <div class="menu-item-price">Rp 15.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="8" data-price="15000" data-name="Coklat Panas">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Green Tea Lattee -->
                <div class="menu-item">
                    <img src="assets/images/gren_tea_latte.jpg" alt="Green Tea Lattee">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Green Tea Lattee</div>
                        <div class="menu-item-desc">
                            Green Tea Lattee adalah racikan teh hijau dengan cream susu dan sedikit gula.
                        </div>
                        <div class="menu-item-price">Rp 20.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="9" data-price="20000" data-name="Green Tea Lattee">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Cappucino -->
                <div class="menu-item">
                    <img src="assets/images/cappuccino.jpg" alt="Cappucino">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Cappucino</div>
                        <div class="menu-item-desc">
                            Cappucino adalah racikan minuman dari kopi, coklat dan cream susu sert sedikit gula.
                        </div>
                        <div class="menu-item-price">Rp 20.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="10" data-price="20000" data-name="Cappucino">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Choco Marsmellow -->
                <div class="menu-item">
                    <img src="assets/images/marsmelo.jpg" alt="Choco Marsmellow">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Choco Marsmellow</div>
                        <div class="menu-item-desc">
                            Minuman coklat panas dengan taburan marsmellow yang lembut dan manis di atasnya.
                        </div>
                        <div class="menu-item-price">Rp 25.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="11" data-price="25000" data-name="Choco Marsmellow">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Dishes Section -->
        <div class="menu-section">
            <div class="section-title food">
                <h2>HIDANGAN UTAMA</h2>
            </div>
            
            <div class="menu-items">
                
                <!-- Nasi Soto Ayam -->
                <div class="menu-item">
                    <img src="assets/images/Soto_ayam.jpg" alt="Nasi Soto Ayam">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Nasi Soto Ayam</div>
                        <div class="menu-item-desc">
                            Nasi Soto Ayam istimewa pakai telur dengan taburan tauge segar dan bawah goreng yang gurih.
                        </div>
                        <div class="menu-item-price">Rp 20.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="1" data-price="20000" data-name="Nasi Soto Ayam">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Nasi Rawon -->
                <div class="menu-item">
                    <img src="assets/images/nasi_rawon.jpg" alt="Nasi Rawon">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Nasi Rawon</div>
                        <div class="menu-item-desc">
                            Nasi Rawon Daging Sapi dengan telor asin yang masir. Nikmat dan lezat.
                        </div>
                        <div class="menu-item-price">Rp 23.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="2" data-price="23000" data-name="Nasi Rawon">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Nasi Ketela Sambal Matah -->
                <div class="menu-item">
                    <img src="assets/images/sambal_matah.jpg" alt="Nasi Ketela Sambal Matah">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Nasi Ketela Sambal Matah</div>
                        <div class="menu-item-desc">
                            Nasi punel dengan campuran ketela ungu yang wangi dengan lauk ayam goreng krispi, dan lalapan sambal matah yang menggugah selera.
                        </div>
                        <div class="menu-item-price">Rp 20.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="3" data-price="20000" data-name="Nasi Ketela Sambal Matah">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Nasi Goreng Seafood -->
                <div class="menu-item">
                    <img src="assets/images/nasi_goreng_seafood.jpg" alt="Nasi Goreng Seafood">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Nasi Goreng Seafood</div>
                        <div class="menu-item-desc">
                            Nasi goreng seafood dengan campuran daging udang, kerang, cumi dan kepiting yang lezat.
                        </div>
                        <div class="menu-item-price">Rp 25.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="4" data-price="25000" data-name="Nasi Goreng Seafood">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Sayur Lodeh -->
                <div class="menu-item">
                    <img src="assets/images/sayur_lodeh.jpg" alt="Sayur Lodeh">
                    <div class="menu-item-info">
                        <div class="menu-item-title">Sayur Lodeh</div>
                        <div class="menu-item-desc">
                            Sayur lodeh tempe dengan tambahan udang, telur puyuh, petai dan kacang panjang membuat hidangan ini begitu lezatnya.
                        </div>
                        <div class="menu-item-price">Rp 20.000</div>
                        <div class="quantity-selector">
                            <button class="decrease-qty">-</button>
                            <input type="number" min="0" value="0" class="item-qty" data-id="5" data-price="20000" data-name="Sayur Lodeh">
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sticky Next Button -->
    <div class="sticky-next">
        <div class="order-summary">
            <span id="selected-items">0</span> item dipilih | Total: Rp <span id="total-price">0</span>
        </div>
        <button id="next-button" disabled>Lanjutkan <i class="fas fa-arrow-right"></i></button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const decreaseButtons = document.querySelectorAll('.decrease-qty');
            const increaseButtons = document.querySelectorAll('.increase-qty');
            const quantityInputs = document.querySelectorAll('.item-qty');
            const nextButton = document.getElementById('next-button');
            const selectedItemsElement = document.getElementById('selected-items');
            const totalPriceElement = document.getElementById('total-price');
            
            // Function to update order summary
            function updateOrderSummary() {
                let totalItems = 0;
                let totalPrice = 0;
                
                quantityInputs.forEach(input => {
                    const quantity = parseInt(input.value);
                    const price = parseInt(input.getAttribute('data-price'));
                    
                    if (quantity > 0) {
                        totalItems += quantity;
                        totalPrice += quantity * price;
                    }
                });
                
                selectedItemsElement.textContent = totalItems;
                totalPriceElement.textContent = totalPrice.toLocaleString('id-ID');
                
                // Enable or disable next button
                nextButton.disabled = totalItems === 0;
            }
            
            // Add event listeners for decrease buttons
            decreaseButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.nextElementSibling;
                    let value = parseInt(input.value);
                    if (value > 0) {
                        input.value = value - 1;
                        updateOrderSummary();
                    }
                });
            });
            
            // Add event listeners for increase buttons
            increaseButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    let value = parseInt(input.value);
                    input.value = value + 1;
                    updateOrderSummary();
                });
            });
            
            // Add event listeners for manual input changes
            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value < 0) this.value = 0;
                    updateOrderSummary();
                });
            });
            
            // Add event listener for next button
            nextButton.addEventListener('click', function() {
                const selectedItems = [];
                quantityInputs.forEach(input => {
                    const quantity = parseInt(input.value);
                    if (quantity > 0) {
                        selectedItems.push({
                            id: parseInt(input.getAttribute('data-id')),
                            name: input.getAttribute('data-name'),
                            qty: quantity,
                            price: parseInt(input.getAttribute('data-price'))
                        });
                    }
                });
                if (selectedItems.length === 0) return;
                // Tampilkan pesanan di modal
                let html = "<table><tr><th>Menu</th><th>Jumlah</th><th>Harga</th></tr>";
                let totalPrice = 0;
                selectedItems.forEach(item => {
                    const subtotal = item.price * item.qty;
                    totalPrice += subtotal;
                    html += `<tr><td>${item.name}</td><td style='text-align:center;'>${item.qty}</td><td style='text-align:right;'>Rp ${subtotal.toLocaleString('id-ID')}</td></tr>`;
                });
                html += `<tr><td colspan='2' style='font-weight:bold; text-align:right;'>Total:</td><td style='font-weight:bold; text-align:right;'>Rp ${totalPrice.toLocaleString('id-ID')}</td></tr>`;
                html += "</table>";
                orderItemsDiv.innerHTML = html;
                modal.style.display = "flex";
                modalError.textContent = "";
            });
            
            const modal = document.getElementById('orderModal');
            const closeBtn = document.querySelector('.modal .close');
            const orderForm = document.getElementById('orderForm');
            const orderItemsDiv = document.getElementById('orderItems');
            const modalError = document.getElementById('modalError');
            
            closeBtn.onclick = function() {
                modal.style.display = "none";
            };
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
                const successModal = document.getElementById('successModal');
                if (event.target == successModal) {
                    successModal.style.display = "none";
                }
            };
            orderForm.onsubmit = function(e) {
                e.preventDefault();
                const nama_pemesan = document.getElementById('nama_pemesan').value;
                const items = [];
                document.querySelectorAll('.item-qty').forEach(input => {
                    const qty = parseInt(input.value);
                    if (qty > 0) {
                        items.push({
                            id: parseInt(input.getAttribute('data-id')),
                            name: input.getAttribute('data-name'),
                            qty: qty,
                            price: parseInt(input.getAttribute('data-price'))
                        });
                    }
                });

                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'action': 'submit_order',
                        'nama_pemesan': nama_pemesan,
                        'order_data': JSON.stringify(items)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modal.style.display = 'none';
                        const successModal = document.getElementById('successModal');
                        successModal.style.display = 'flex';
                        document.getElementById('successOkButton').onclick = function() {
                            successModal.style.display = 'none';
                            // Reset form dan input quantity
                            document.querySelectorAll('.item-qty').forEach(input => {
                                input.value = 0;
                            });
                            updateOrderSummary();
                        };
                        document.querySelector('.closeSuccess').onclick = function() {
                            successModal.style.display = 'none';
                            // Reset form dan input quantity
                            document.querySelectorAll('.item-qty').forEach(input => {
                                input.value = 0;
                            });
                            updateOrderSummary();
                        };
                    } else {
                        modalError.textContent = data.message;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalError.textContent = 'Terjadi kesalahan saat menyimpan pesanan.';
                });
            };

            // Initialize order summary
            updateOrderSummary();
        });
    </script>
<!-- Modal Popup -->
<div id="orderModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Konfirmasi Pesanan</h2>
    <div id="orderItems"></div>
    <p id="modalError"></p>
    <form id="orderForm">
      <label for="nama_pemesan">Nama Pemesan:</label><br>
      <input type="text" id="nama_pemesan" name="nama_pemesan" required>
      <input type="submit" value="Submit Pesanan">
    </form>
  </div>
</div>

<!-- Modal Popup Pemesanan Berhasil -->
<div id="successModal" class="modal">
  <div class="modal-content">
    <span class="closeSuccess">&times;</span>
    <h2>Pemesanan Berhasil</h2>
    <p>Pesanan Anda telah berhasil disimpan.</p>
    <button id="successOkButton">OK</button>
  </div>
</div>
</body>
</html>
