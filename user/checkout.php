<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$items       = [];
$total_harga = 0;

foreach ($_SESSION['cart'] as $prod_id => $qty) {
    $res = mysqli_query($conn, "SELECT * FROM products WHERE id = " . intval($prod_id));
    $p   = mysqli_fetch_assoc($res);
    if ($p) {
        $subtotal     = $p['harga'] * $qty;
        $total_harga += $subtotal;
        $items[]      = array_merge($p, ['qty' => $qty, 'subtotal' => $subtotal]);
    }
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_penerima = trim($_POST['nama_penerima']);
    $alamat        = trim($_POST['alamat']);

    if (empty($nama_penerima) || empty($alamat)) {
        $error = "Nama penerima dan alamat wajib diisi!";
    } else {
        $user_id = $_SESSION['user_id'];
        $sql_order = "INSERT INTO orders (user_id, nama_penerima, alamat, total_harga, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt_order = mysqli_prepare($conn, $sql_order);
        mysqli_stmt_bind_param($stmt_order, "issd", $user_id, $nama_penerima, $alamat, $total_harga);

        if (mysqli_stmt_execute($stmt_order)) {
            $order_id = mysqli_insert_id($conn);

            foreach ($items as $item) {
                $sql_item = "INSERT INTO order_items (order_id, product_id, jumlah, harga_satuan) VALUES (?, ?, ?, ?)";
                $stmt_item = mysqli_prepare($conn, $sql_item);
                mysqli_stmt_bind_param($stmt_item, "iiid", $order_id, $item['id'], $item['qty'], $item['harga']);
                mysqli_stmt_execute($stmt_item);
                mysqli_stmt_close($stmt_item);

                mysqli_query($conn, "UPDATE products SET stok = stok - {$item['qty']} WHERE id = {$item['id']}");
            }

            $_SESSION['cart'] = [];
            header('Location: pesanan_saya.php?status=berhasil');
            exit();
        } else {
            $error = "Gagal memproses pesanan. Silakan coba lagi.";
        }
    }
}

$cart_count = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — TokoBaju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="dashboard.php" class="brand">Toko<span>Baju</span> 👕</a>
    <nav>
        <a href="dashboard.php">Produk</a>
        <a href="pesanan_saya.php">Pesanan Saya</a>
        <a href="../logout.php">Logout</a>
        <a href="cart.php" class="cart-icon">🛒</a>
    </nav>
</nav>

<main>
<div class="container" style="padding-top:2rem; max-width:800px;">
    <div class="page-header">
        <h1>📝 Checkout</h1>
        <a href="cart.php" class="btn btn-outline">← Kembali ke Keranjang</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">📋 Ringkasan Pesanan</div>
        <div class="card-body" style="padding:0;">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                        <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td><?= $item['qty'] ?></td>
                        <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="background:#f0fdf4;">
                        <td colspan="3"><strong>Total</strong></td>
                        <td><strong style="color:#f4a261; font-size:1.1rem;">Rp <?= number_format($total_harga, 0, ',', '.') ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">🚚 Informasi Pengiriman</div>
        <div class="card-body">
            <form action="checkout.php" method="POST">
                <div class="form-group">
                    <label for="nama_penerima">Nama Penerima *</label>
                    <input type="text" id="nama_penerima" name="nama_penerima" 
                           placeholder="Nama lengkap penerima" required
                           value="<?= htmlspecialchars($_POST['nama_penerima'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat Pengiriman Lengkap *</label>
                    <textarea id="alamat" name="alamat" rows="4" 
                              placeholder="Jalan, nomor rumah, kota, kode pos..."
                              required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                </div>
                <div style="background:#fef9f0; border:1px solid #fde68a; border-radius:8px; padding:1rem; margin-bottom:1.5rem;">
                    <p style="font-size:0.9rem; color:#92400e;">⚠️ <strong>Catatan:</strong> Pembayaran dilakukan melalui transfer bank setelah pesanan dikonfirmasi oleh admin.</p>
                </div>
                <button type="submit" class="btn btn-accent btn-block" style="font-size:1rem; padding:0.9rem;">
                    ✅ Konfirmasi Pesanan — Rp <?= number_format($total_harga, 0, ',', '.') ?>
                </button>
            </form>
        </div>
    </div>
</div>

</div>
</main>

<footer>
    <p>© 2024 TokoBaju — Projek Sekolah PHP Native</p>
</footer>
<script src="../js/script.js"></script>
</body>
</html>
