<?php
$host = "localhost";
$user = "root"; // sesuaikan dengan user database Anda
$pass = ""; // sesuaikan dengan password database Anda
$db   = "ppdb_smk_um";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>