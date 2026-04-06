<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$total_produk  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$total_user    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'"))['total'];
$total_pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];
$pending       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status='pending'"))['total'];

$pesanan_baru = mysqli_query($conn, "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — TokoBaju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <span class="sidebar-brand">Toko<span>Baju</span></span>
        <a href="dashboard.php" class="active"><span class="icon">📊</span> Dashboard</a>
        <a href="produk.php"><span class="icon">👕</span> Produk</a>
        <a href="tambah_produk.php"><span class="icon">➕</span> Tambah Produk</a>
        <a href="pesanan.php"><span class="icon">📦</span> Pesanan</a>
        <a href="../logout.php"><span class="icon">🚪</span> Logout</a>
    </aside>

    <main class="admin-content">
        <div class="page-header">
            <h1>Dashboard Admin</h1>
            <span style="color:#6b7280; font-size:0.9rem;">Halo, <?= htmlspecialchars($_SESSION['username']) ?> 👋</span>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $total_produk ?></div>
                <div class="stat-label">Total Produk</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_user ?></div>
                <div class="stat-label">Total User</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_pesanan ?></div>
                <div class="stat-label">Total Pesanan</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:#f4a261;"><?= $pending ?></div>
                <div class="stat-label">Pesanan Pending</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">📦 Pesanan Terbaru</div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Penerima</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($pesanan_baru) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($pesanan_baru)): ?>
                                <tr>
                                    <td>#<?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                                    <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                    <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                    <td><a href="pesanan.php" class="btn btn-outline btn-sm">Lihat</a></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" style="text-align:center; color:#aaa;">Belum ada pesanan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="../js/script.js"></script>
</body>
</html>
