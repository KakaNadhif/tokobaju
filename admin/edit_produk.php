<?php
// admin/edit_produk.php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$product_id = intval($_GET['id'] ?? 0);
$error      = '';

$sql_select    = "SELECT * FROM products WHERE id = ?";
$stmt_select   = mysqli_prepare($conn, $sql_select);
mysqli_stmt_bind_param($stmt_select, "i", $product_id);
mysqli_stmt_execute($stmt_select);
$result_select = mysqli_stmt_get_result($stmt_select);
$product       = mysqli_fetch_assoc($result_select);

if (!$product) {
    echo "<p>Produk tidak ditemukan.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = trim($_POST['nama_produk']);
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    $deskripsi   = trim($_POST['deskripsi']);
    $gambar_lama = $product['gambar'];
    $gambar_final = $gambar_lama;

    if (empty($nama_produk) || empty($harga) || empty($stok)) {
        $error = "Nama produk, harga, dan stok wajib diisi!";
    } else {
        if (!empty($_FILES['gambar']['name'])) {
            $nama_file   = basename($_FILES['gambar']['name']);
            $ext         = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (!in_array($ext, $allowed_ext)) {
                $error = "Format gambar tidak didukung.";
            } else {
                $nama_unik   = time() . '_' . $nama_file;
                $target_dir  = '../uploads/';
                $target_file = $target_dir . $nama_unik;

                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                    $gambar_final = $nama_unik;
                    // Hapus gambar lama
                    if ($gambar_lama && file_exists($target_dir . $gambar_lama)) {
                        unlink($target_dir . $gambar_lama);
                    }
                } else {
                    $error = "Gagal mengupload gambar.";
                }
            }
        }

        if (empty($error)) {
            $sql_update = "UPDATE products SET nama_produk=?, harga=?, stok=?, deskripsi=?, gambar=? WHERE id=?";
            $stmt_upd   = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt_upd, "sdissi", $nama_produk, $harga, $stok, $deskripsi, $gambar_final, $product_id);
            if (mysqli_stmt_execute($stmt_upd)) {
                header('Location: produk.php?status=updated');
                exit();
            } else {
                $error = "Gagal memperbarui produk.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk — TokoBaju</title>
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
            <h1>✏️ Edit Produk</h1>
            <a href="produk.php" class="btn btn-outline">← Kembali</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card" style="max-width:600px;">
            <div class="card-body">
                <form action="edit_produk.php?id=<?= $product_id ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_produk">Nama Produk *</label>
                        <input type="text" id="nama_produk" name="nama_produk" required
                               value="<?= htmlspecialchars($product['nama_produk']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga (Rp) *</label>
                        <input type="number" id="harga" name="harga" required min="0"
                               value="<?= htmlspecialchars($product['harga']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok *</label>
                        <input type="number" id="stok" name="stok" required min="0"
                               value="<?= htmlspecialchars($product['stok']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi"><?= htmlspecialchars($product['deskripsi']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Gambar Saat Ini</label>
                        <?php if ($product['gambar'] && file_exists('../uploads/' . $product['gambar'])): ?>
                            <br><img src="../uploads/<?= htmlspecialchars($product['gambar']) ?>" 
                                     width="120" style="border-radius:8px; margin-top:0.5rem;">
                        <?php else: ?>
                            <span style="font-size:2.5rem; display:block;">👕</span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Ganti Gambar (opsional)</label>
                        <input type="file" id="gambar" name="gambar" accept="image/*">
                        <img id="preview-gambar" src="" alt="Preview" 
                             style="display:none; margin-top:0.8rem; max-width:150px; border-radius:8px;">
                    </div>
                    <button type="submit" class="btn btn-primary">Perbarui Produk</button>
                    <a href="produk.php" class="btn btn-outline" style="margin-left:0.5rem;">Batal</a>
                </form>
            </div>
        </div>
    </main>
</div>
<script src="../js/script.js"></script>
</body>
</html>
