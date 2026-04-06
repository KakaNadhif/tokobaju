<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = trim($_POST['nama_produk']);
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    $deskripsi   = trim($_POST['deskripsi']);
    $gambar      = '';

    if (empty($nama_produk) || empty($harga) || empty($stok)) {
        $error = "Nama produk, harga, dan stok wajib diisi!";
    } else {
        if (!empty($_FILES['gambar']['name'])) {
            $nama_file   = basename($_FILES['gambar']['name']);
            $ext         = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (!in_array($ext, $allowed_ext)) {
                $error = "Format gambar tidak didukung. Gunakan JPG, PNG, atau GIF.";
            } else {
                $nama_unik   = time() . '_' . $nama_file;
                $target_dir  = '../uploads/';
                $target_file = $target_dir . $nama_unik;

                if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                    $gambar = $nama_unik;
                } else {
                    $error = "Gagal mengupload gambar.";
                }
            }
        }

        if (empty($error)) {
            $sql  = "INSERT INTO products (nama_produk, harga, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sdiss", $nama_produk, $harga, $stok, $deskripsi, $gambar);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: produk.php?status=added');
                exit();
            } else {
                $error = "Gagal menyimpan produk: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk — TokoBaju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <span class="sidebar-brand">Toko<span>Baju</span></span>
        <a href="dashboard.php"><span class="icon">📊</span> Dashboard</a>
        <a href="produk.php"><span class="icon">👕</span> Produk</a>
        <a href="tambah_produk.php" class="active"><span class="icon">➕</span> Tambah Produk</a>
        <a href="pesanan.php"><span class="icon">📦</span> Pesanan</a>
        <a href="../logout.php"><span class="icon">🚪</span> Logout</a>
    </aside>

    <main class="admin-content">
        <div class="page-header">
            <h1>➕ Tambah Produk</h1>
            <a href="produk.php" class="btn btn-outline">← Kembali</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card" style="max-width:600px;">
            <div class="card-body">
                <form action="tambah_produk.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_produk">Nama Produk *</label>
                        <input type="text" id="nama_produk" name="nama_produk" placeholder="Contoh: Kaos Polos Hitam"
                               value="<?= htmlspecialchars($_POST['nama_produk'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga (Rp) *</label>
                        <input type="number" id="harga" name="harga" placeholder="Contoh: 75000" min="0"
                               value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok *</label>
                        <input type="number" id="stok" name="stok" placeholder="Jumlah stok" min="0"
                               value="<?= htmlspecialchars($_POST['stok'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Produk</label>
                        <textarea id="deskripsi" name="deskripsi" placeholder="Deskripsi singkat produk..."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar Produk (opsional)</label>
                        <input type="file" id="gambar" name="gambar" accept="image/*">
                        <img id="preview-gambar" src="" alt="Preview" 
                             style="display:none; margin-top:0.8rem; max-width:150px; border-radius:8px;">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Produk</button>
                    <a href="produk.php" class="btn btn-outline" style="margin-left:0.5rem;">Batal</a>
                </form>
            </div>
        </div>
    </main>
</div>
<script src="../js/script.js"></script>
</body>
</html>
