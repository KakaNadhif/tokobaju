<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id     = intval($_POST['order_id']);
    $status_baru  = $_POST['status'];
    $allowed      = ['pending', 'dikirim', 'selesai'];
    if (in_array($status_baru, $allowed)) {
        mysqli_query($conn, "UPDATE orders SET status='$status_baru' WHERE id=$order_id");
    }
    header('Location: pesanan.php?status=updated');
    exit();
}

$pesan = '';
if (isset($_GET['status']) && $_GET['status'] == 'updated') {
    $pesan = '<div class="alert alert-success alert-auto">Status pesanan berhasil diperbarui.</div>';
}

$pesanan = mysqli_query($conn, "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan — TokoBaju Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <span class="sidebar-brand">Toko<span>Baju</span></span>
        <a href="dashboard.php"><span class="icon">📊</span> Dashboard</a>
        <a href="produk.php"><span class="icon">👕</span> Produk</a>
        <a href="tambah_produk.php"><span class="icon">➕</span> Tambah Produk</a>
        <a href="pesanan.php" class="active"><span class="icon">📦</span> Pesanan</a>
        <a href="../logout.php"><span class="icon">🚪</span> Logout</a>
    </aside>

    <main class="admin-content">
        <div class="page-header">
            <h1>📦 Manajemen Pesanan</h1>
        </div>

        <?= $pesan ?>

        <div class="card">
            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Penerima</th>
                                <th>Alamat</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($pesanan) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($pesanan)): ?>
                                <tr>
                                    <td>#<?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                                    <td style="max-width:150px; font-size:0.8rem;"><?= htmlspecialchars(substr($row['alamat'], 0, 60)) ?>...</td>
                                    <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                    <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <form method="POST" style="display:flex; gap:0.3rem; flex-wrap:wrap;">
                                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                            <select name="status" style="padding:0.3rem; border:1px solid #ddd; border-radius:6px; font-size:0.8rem;">
                                                <option value="pending"  <?= $row['status']=='pending'  ? 'selected':'' ?>>Pending</option>
                                                <option value="dikirim" <?= $row['status']=='dikirim' ? 'selected':'' ?>>Dikirim</option>
                                                <option value="selesai" <?= $row['status']=='selesai' ? 'selected':'' ?>>Selesai</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" style="text-align:center; color:#aaa; padding:2rem;">Belum ada pesanan.</td></tr>
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
