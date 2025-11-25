<?php
include "Phpconnect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap   = $_POST['nama_lengkap'];
    $nik            = $_POST['nik'];
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $agama          = $_POST['agama'];
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $no_hp          = $_POST['no_hp'];
    $email          = $_POST['email'];

    $sql = "INSERT INTO pendaftaran_siswa 
            (nama_lengkap, nik, jenis_kelamin, agama, tanggal_lahir, no_hp, email) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nama_lengkap, $nik, $jenis_kelamin, $agama, $tanggal_lahir, $no_hp, $email);

    if ($stmt->execute()) {
        $siswa_id = $stmt->insert_id;
        header("Location: form-alamat.php?id=" . $siswa_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: form-daftar.php");
    exit();
}
