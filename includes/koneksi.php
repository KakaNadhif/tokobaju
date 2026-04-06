<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_jualbaju";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
