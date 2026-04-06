<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
if ($_SESSION['role'] == 'admin') {
    header('Location: ../admin/dashboard.php');
    exit();
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_GET['tambah'])) {
    $id_produk = intval($_GET['tambah']);
    if ($id_produk > 0) {
        if (isset($_SESSION['cart'][$id_produk])) {
            $_SESSION['cart'][$id_produk]++;
        } else {
            $_SESSION['cart'][$id_produk] = 1;
        }
        header('Location: dashboard.php?tambah_ok=1');
        exit();
    }
}

$per_page  = 6;
$page      = max(1, intval($_GET['page'] ?? 1));
$offset    = ($page - 1) * $per_page;
$total_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$total_page = ceil($total_row / $per_page);

$products  = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC LIMIT $per_page OFFSET $offset");
$cart_count = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko — TokoBaju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="dashboard.php" class="brand">Toko<span>Baju</span> 👕</a>
    <nav>
        <a href="dashboard.php" class="active">Produk</a>
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

<div class="hero">
    <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>! 👋</h1>
    <p>Temukan koleksi baju terbaik pilihan kamu</p>
</div>

<main>
<div class="container">
    <?php if (isset($_GET['tambah_ok'])): ?>
        <div class="alert alert-success alert-auto" style="margin-top:1rem;">✅ Produk berhasil ditambahkan ke keranjang!</div>
    <?php endif; ?>

    <div class="product-grid">
        <?php if (mysqli_num_rows($products) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($products)): ?>
            <div class="product-card">
                <div class="product-img">
                    <?php if ($row['gambar'] && file_exists('../uploads/' . $row['gambar'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" 
                             alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                    <?php else: ?>
                        👕
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-name"><?= htmlspecialchars($row['nama_produk']) ?></div>
                    <div class="product-price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                    <div class="product-stok">Stok: <?= $row['stok'] ?></div>
                    <p style="font-size:0.8rem; color:#6b7280; margin-bottom:0.8rem;">
                        <?= htmlspecialchars(substr($row['deskripsi'], 0, 70)) ?>...
                    </p>
                    <?php if ($row['stok'] > 0): ?>
                        <a href="dashboard.php?tambah=<?= $row['id'] ?>" class="btn btn-accent btn-sm btn-block">
                            🛒 Tambah ke Keranjang
                        </a>
                    <?php else: ?>
                        <button class="btn btn-sm btn-block" style="background:#e5e7eb; color:#9ca3af; cursor:not-allowed;">Stok Habis</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="grid-column:1/-1; text-align:center; color:#aaa; padding:3rem;">Belum ada produk tersedia.</p>
        <?php endif; ?>
    </div>

    <?php if ($total_page > 1): ?>
    <div style="text-align:center; padding:1.5rem 0; display:flex; gap:0.5rem; justify-content:center;">
        <?php for ($i = 1; $i <= $total_page; $i++): ?>
            <a href="?page=<?= $i ?>" class="btn <?= $i == $page ? 'btn-primary' : 'btn-outline' ?> btn-sm"><?= $i ?></a>
        <?php endfor; ?>
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
