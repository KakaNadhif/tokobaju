<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['hapus'])) {
    $hapus_id = intval($_GET['hapus']);
    unset($_SESSION['cart'][$hapus_id]);
    header('Location: cart.php');
    exit();
}

if (isset($_GET['kosongkan'])) {
    $_SESSION['cart'] = [];
    header('Location: cart.php');
    exit();
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$items      = [];
$total_harga = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $prod_id => $qty) {
        $res = mysqli_query($conn, "SELECT * FROM products WHERE id = " . intval($prod_id));
        $p   = mysqli_fetch_assoc($res);
        if ($p) {
            $subtotal     = $p['harga'] * $qty;
            $total_harga += $subtotal;
            $items[]      = array_merge($p, ['qty' => $qty, 'subtotal' => $subtotal]);
        }
    }
}

$cart_count = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang — TokoBaju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="dashboard.php" class="brand">Toko<span>Baju</span> 👕</a>
    <nav>
        <a href="dashboard.php">Produk</a>
        <a href="pesanan_saya.php">Pesanan Saya</a>
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
        <h1>🛒 Keranjang Belanja</h1>
        <a href="dashboard.php" class="btn btn-outline">← Lanjut Belanja</a>
    </div>

    <?php if (empty($items)): ?>
        <div class="card">
            <div class="card-body" style="text-align:center; padding:3rem;">
                <p style="font-size:3rem;">🛒</p>
                <p style="color:#6b7280; margin-bottom:1rem;">Keranjang kamu masih kosong.</p>
                <a href="dashboard.php" class="btn btn-primary">Mulai Belanja</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga Satuan</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr class="cart-row" data-harga="<?= $item['harga'] ?>">
                                <td>
                                    <div style="display:flex; align-items:center; gap:0.8rem;">
                                        <?php if ($item['gambar'] && file_exists('../uploads/' . $item['gambar'])): ?>
                                            <img src="../uploads/<?= htmlspecialchars($item['gambar']) ?>" 
                                                 width="50" height="50" style="object-fit:cover; border-radius:6px;">
                                        <?php else: ?>
                                            <span style="font-size:2rem;">👕</span>
                                        <?php endif; ?>
                                        <strong><?= htmlspecialchars($item['nama_produk']) ?></strong>
                                    </div>
                                </td>
                                <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                <td>
                                    <div class="cart-qty">
                                        <input type="number" class="qty-input" 
                                               value="<?= $item['qty'] ?>" 
                                               min="1" max="<?= $item['stok'] ?>"
                                               data-id="<?= $item['id'] ?>">
                                    </div>
                                </td>
                                <td class="subtotal">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="cart.php?hapus=<?= $item['id'] ?>" 
                                       onclick="return confirm('Hapus item ini dari keranjang?')"
                                       class="btn btn-danger btn-sm">🗑️</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; 
                    background:white; padding:1.5rem; border-radius:12px; box-shadow:var(--shadow); margin-top:1.5rem; flex-wrap:wrap; gap:1rem;">
            <div>
                <p style="color:#6b7280; font-size:0.9rem;">Total Belanja</p>
                <div class="cart-total" id="grand-total">Rp <?= number_format($total_harga, 0, ',', '.') ?></div>
            </div>
            <div style="display:flex; gap:0.8rem;">
                <a href="cart.php?kosongkan=1" 
                   onclick="return confirm('Kosongkan semua keranjang?')"
                   class="btn btn-outline">🗑️ Kosongkan</a>
                <a href="checkout.php" class="btn btn-accent">Checkout →</a>
            </div>
        </div>
    <?php endif; ?>
</div>
</main>

<footer>
    <p>© 2024 TokoBaju — Projek Sekolah PHP Native</p>
</footer>
<script src="../js/script.js"></script>
</body>
</html>
