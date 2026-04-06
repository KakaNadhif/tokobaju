<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    $cek = mysqli_query($conn, "SELECT gambar FROM products WHERE id = $product_id");
    $row = mysqli_fetch_assoc($cek);
    if ($row && $row['gambar'] && file_exists('../uploads/' . $row['gambar'])) {
        unlink('../uploads/' . $row['gambar']);
    }

    $sql  = "DELETE FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: produk.php?status=deleted');
        exit();
    } else {
        echo "Gagal menghapus produk.";
    }
    mysqli_stmt_close($stmt);
} else {
    header('Location: produk.php');
    exit();
}
mysqli_close($conn);
?>
