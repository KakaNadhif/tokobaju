<?php
session_start();
include_once 'includes/koneksi.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong!";
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();
        $stmt->close();

        if ($id && password_verify($password, $hashed_password)) {
            $_SESSION['user_id']  = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role']     = $role;

            if ($role == 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: user/dashboard.php');
            }
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — TokoBaju</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-box">
        <span class="logo-big">Toko<span>Baju</span> 👕</span>
        <h2>Selamat Datang</h2>
        <p class="subtitle">Masuk ke akun kamu untuk mulai belanja</p>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-auto"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php" method="POST" id="form-login">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p style="text-align:center; margin-top:1.2rem; font-size:0.9rem; color:#6b7280;">
            Belum punya akun? <a href="register.php" style="color:#2d6a4f; font-weight:600;">Daftar di sini</a>
        </p>
    </div>
</div>
<script src="js/script.js"></script>
</body>
</html>
