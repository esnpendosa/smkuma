<?php

$host     = "localhost";    // biasanya "localhost"
$user     = "root";         // username MySQL (default XAMPP/WAMP biasanya root)
$password = "";             // password MySQL (kosong jika default XAMPP)
$dbname   = "ppdb_smk_um";  // nama database sesuai file SQL dump

$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
