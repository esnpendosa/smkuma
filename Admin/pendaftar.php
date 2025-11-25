<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}
include '../koneksi.php';
$data = mysqli_query($koneksi, "SELECT * FROM pendaftar ORDER BY tanggal_daftar DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Pendaftar</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="data_pendaftar.php" class="active">📋 Data Pendaftar</a>
    <a href="grafik.php">📊 Grafik</a>
    <a href="download.php">⬇️ Download Data</a>
    <a href="logout.php" class="logout">🚪 Logout</a>
  </div>

  <div class="content">
    <h1>Data Pendaftar</h1>
    <table>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>NISN</th>
        <th>Asal Sekolah</th>
        <th>Jurusan</th>
        <th>Nilai</th>
        <th>Tanggal Daftar</th>
      </tr>
      <?php $no=1; while($d=mysqli_fetch_array($data)){ ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= $d['nama']; ?></td>
        <td><?= $d['nisn']; ?></td>
        <td><?= $d['asal_sekolah']; ?></td>
        <td><?= $d['jurusan']; ?></td>
        <td><?= $d['nilai_rata']; ?></td>
        <td><?= $d['tanggal_daftar']; ?></td>
      </tr>
      <?php } ?>
    </table>
  </div>
</body>
</html>
