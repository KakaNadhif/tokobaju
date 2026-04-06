<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$pesan = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'deleted')  $pesan = '<div class="alert alert-danger alert-auto">Produk berhasil dihapus.</div>';
    if ($_GET['status'] == 'added')    $pesan = '<div class="alert alert-success alert-auto">Produk berhasil ditambahkan.</div>';
    if ($_GET['status'] == 'updated')  $pesan = '<div class="alert alert-success alert-auto">Produk berhasil diperbarui.</div>';
}

$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk — TokoBaju Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <span class="sidebar-brand">Toko<span>Baju</span></span>
        <a href="dashboard.php"><span class="icon">📊</span> Dashboard</a>
        <a href="produk.php" class="active"><span class="icon">👕</span> Produk</a>
        <a href="tambah_produk.php"><span class="icon">➕</span> Tambah Produk</a>
        <a href="pesanan.php"><span class="icon">📦</span> Pesanan</a>
        <a href="../logout.php"><span class="icon">🚪</span> Logout</a>
    </aside>

    <main class="admin-content">
        <div class="page-header">
            <h1>👕 Daftar Produk</h1>
            <a href="tambah_produk.php" class="btn btn-primary">+ Tambah Produk</a>
        </div>

        <?= $pesan ?>

        <div class="card">
            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <?php if ($row['gambar'] && file_exists('../uploads/' . $row['gambar'])): ?>
                                            <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" 
                                                 width="60" height="60" style="object-fit:cover; border-radius:6px;">
                                        <?php else: ?>
                                            <span style="font-size:2rem;">👕</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                    <td><?= $row['stok'] ?></td>
                                    <td>
                                        <a href="edit_produk.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Edit</a>
                                        <a href="javascript:void(0)" 
                                           onclick="konfirmasiHapus('delete_produk.php?id=<?= $row['id'] ?>', '<?= htmlspecialchars($row['nama_produk']) ?>')"
                                           class="btn btn-danger btn-sm">Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center; color:#aaa; padding:2rem;">Belum ada produk. <a href="tambah_produk.php">Tambah sekarang</a></td></tr>
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
