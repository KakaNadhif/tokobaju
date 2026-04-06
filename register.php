<?php
// register.php — Halaman Registrasi
session_start();
include_once 'includes/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: user/dashboard.php');
    exit();
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username         = trim($_POST['username']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($username)) {
        $error = "Username tidak boleh kosong.";
    } elseif (strlen($username) < 3) {
        $error = "Username minimal 3 karakter.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $error = "Username hanya boleh huruf, angka, dan underscore.";
    } elseif (empty($password)) {
        $error = "Password tidak boleh kosong.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Cek username sudah ada
        $cek = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $error = "Username sudah terdaftar, coba yang lain.";
        } else {
            // Hash password & simpan
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt   = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            mysqli_stmt_bind_param($stmt, "ss", $username, $hashed);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Registrasi berhasil! Silakan login.";
                header("Refresh: 2; URL=index.php");
            } else {
                $error = "Gagal registrasi, silakan coba lagi.";
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($cek);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — TokoBaju</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-box">
        <span class="logo-big">Toko<span>Baju</span> 👕</span>
        <h2>Buat Akun Baru</h2>
        <p class="subtitle">Daftar sekarang dan mulai belanja!</p>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-auto"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success alert-auto"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" id="form-register">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Minimal 3 karakter" required
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Daftar Sekarang</button>
        </form>

        <p style="text-align:center; margin-top:1.2rem; font-size:0.9rem; color:#6b7280;">
            Sudah punya akun? <a href="index.php" style="color:#2d6a4f; font-weight:600;">Login di sini</a>
        </p>
    </div>
</div>
<script src="js/script.js"></script>
</body>
</html>
