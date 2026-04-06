<?php
// user/pesanan_saya.php — Riwayat Pesanan User
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$user_id    = $_SESSION['user_id'];
$pesanan    = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya — TokoBaju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="dashboard.php" class="brand">Toko<span>Baju</span> 👕</a>
    <nav>
        <a href="dashboard.php">Produk</a>
        <a href="pesanan_saya.php" class="active">Pesanan Saya</a>
        <a href="../logout.php">Logout</a>
        <a href="cart.php" class="cart-icon">
            🛒
            <?php if ($cart_count > 0): ?>
                <span class="cart-badge"><?= $cart_count ?></span>
            <?php endif; ?>
        </a>
    </nav>
</nav>

<main>
<div class="container" style="padding-top:2rem;">
    <div class="page-header">
        <h1>📦 Pesanan Saya</h1>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'berhasil'): ?>
        <div class="alert alert-success alert-auto">
            🎉 Pesanan berhasil dibuat! Silakan tunggu konfirmasi dari admin.
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($pesanan) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($pesanan)): ?>
        <div class="card" style="margin-bottom:1rem;">
            <div class="card-body">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:0.5rem;">
                    <div>
                        <strong style="font-size:1.05rem;">Pesanan #<?= $row['id'] ?></strong>
                        <p style="color:#6b7280; font-size:0.85rem; margin-top:0.2rem;">
                            📅 <?= date('d F Y, H:i', strtotime($row['created_at'])) ?>
                        </p>
                        <p style="margin-top:0.5rem; font-size:0.9rem;">
                            📍 Penerima: <strong><?= htmlspecialchars($row['nama_penerima']) ?></strong>
                        </p>
                        <p style="font-size:0.85rem; color:#6b7280;">
                            Alamat: <?= htmlspecialchars($row['alamat']) ?>
                        </p>
                    </div>
                    <div style="text-align:right;">
                        <span class="badge badge-<?= $row['status'] ?>" style="font-size:0.9rem; padding:0.4rem 1rem;">
                            <?= ucfirst($row['status']) ?>
                        </span>
                        <p style="margin-top:0.5rem; font-size:1.1rem; font-weight:700; color:#f4a261;">
                            Rp <?= number_format($row['total_harga'], 0, ',', '.') ?>
                        </p>
                    </div>
                </div>

                <!-- Detail item pesanan -->
                <?php
                $items_res = mysqli_query($conn, "SELECT oi.*, p.nama_produk FROM order_items oi 
                                                   JOIN products p ON oi.product_id = p.id 
                                                   WHERE oi.order_id = {$row['id']}");
                if (mysqli_num_rows($items_res) > 0): ?>
                <hr style="border:none; border-top:1px solid #e5e7eb; margin:1rem 0;">
                <div style="font-size:0.85rem; color:#374151;">
                    <strong>Detail Produk:</strong>
                    <ul style="margin-top:0.3rem; padding-left:1.2rem;">
                        <?php while ($item = mysqli_fetch_assoc($items_res)): ?>
                        <li><?= htmlspecialchars($item['nama_produk']) ?> × <?= $item['jumlah'] ?> 
                            = Rp <?= number_format($item['harga_satuan'] * $item['jumlah'], 0, ',', '.') ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card">
            <div class="card-body" style="text-align:center; padding:3rem;">
                <p style="font-size:3rem;">📦</p>
                <p style="color:#6b7280; margin-bottom:1rem;">Kamu belum pernah melakukan pesanan.</p>
                <a href="dashboard.php" class="btn btn-primary">Mulai Belanja</a>
            </div>
        </div>
    <?php endif; ?>
</div>

</div>
</main>

<footer>
    <p>© 2024 TokoBaju — Projek Sekolah PHP Native</p>
</footer>
<script src="../js/script.js"></script>
</body>
</html>
