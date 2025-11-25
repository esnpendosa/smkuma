<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}
include '../koneksi.php';

// Ambil total pendaftar - PERBAIKAN: ganti 'pendaftar' dengan 'pendaftaran_siswa'
$result = mysqli_query($koneksi, "SELECT COUNT(*) as jumlah FROM pendaftaran_siswa");
$data = mysqli_fetch_array($result);
$total = $data['jumlah'];

// Ambil data statistik lainnya
$laki = mysqli_fetch_array(mysqli_query($koneksi, "SELECT COUNT(*) as jumlah FROM pendaftaran_siswa WHERE jenis_kelamin = 'Laki-laki'"))['jumlah'];
$perempuan = mysqli_fetch_array(mysqli_query($koneksi, "SELECT COUNT(*) as jumlah FROM pendaftaran_siswa WHERE jenis_kelamin = 'Perempuan'"))['jumlah'];

// Data pendaftar terbaru
$pendaftar_terbaru = mysqli_query($koneksi, "SELECT * FROM pendaftaran_siswa ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin | PPDB SMK UMAR MAS'UD</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      background: #f5f5f5;
      display: flex;
    }
    .sidebar {
      width: 250px;
      background: #004080;
      color: white;
      height: 100vh;
      padding: 20px;
      position: fixed;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 12px 15px;
      margin: 8px 0;
      border-radius: 5px;
      transition: background 0.3s;
    }
    .sidebar a:hover {
      background: rgba(255,255,255,0.1);
    }
    .sidebar .logout {
      background: #dc3545;
      margin-top: 20px;
    }
    .sidebar .logout:hover {
      background: #c82333;
    }
    .content {
      margin-left: 250px;
      padding: 30px;
      width: calc(100% - 250px);
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }
    .card {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .card h3 {
      color: #004080;
      margin-bottom: 10px;
      font-size: 16px;
    }
    .card p {
      font-size: 2em;
      font-weight: bold;
      color: #333;
    }
    .card.stat-laki { border-left: 4px solid #007bff; }
    .card.stat-perempuan { border-left: 4px solid #e83e8c; }
    .card.stat-total { border-left: 4px solid #28a745; }
    
    .recent-table {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-top: 30px;
    }
    .recent-table h3 {
      color: #004080;
      margin-bottom: 15px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table th, table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    table th {
      background: #f8f9fa;
      color: #004080;
      font-weight: 600;
    }
    table tr:hover {
      background: #f8f9fa;
    }
    .welcome {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="data_pendaftar.php">📋 Data Pendaftar</a>
    <a href="grafik.php">📊 Grafik</a>
    <a href="download.php">⬇️ Download Data</a>
    <a href="logout.php" class="logout">🚪 Logout</a>
  </div>

  <div class="content">
    <div class="welcome">
      <h1>Selamat Datang, <?= $_SESSION['admin']['nama_lengkap'] ?? $_SESSION['admin'] ?>!</h1>
      <p>Dashboard Sistem PPDB SMK UMAR MAS'UD</p>
    </div>

    <div class="cards">
      <div class="card stat-total">
        <h3>Total Pendaftar</h3>
        <p><?= $total; ?> siswa</p>
      </div>
      <div class="card stat-laki">
        <h3>Siswa Laki-laki</h3>
        <p><?= $laki; ?> siswa</p>
      </div>
      <div class="card stat-perempuan">
        <h3>Siswa Perempuan</h3>
        <p><?= $perempuan; ?> siswa</p>
      </div>
    </div>

    <div class="recent-table">
      <h3>Pendaftar Terbaru</h3>
      <table>
        <thead>
          <tr>
            <th>Nama Lengkap</th>
            <th>NIK</th>
            <th>Jenis Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>Tanggal Daftar</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_array($pendaftar_terbaru)): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
            <td><?= htmlspecialchars($row['nik']) ?></td>
            <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
            <td><?= date('d/m/Y', strtotime($row['tanggal_lahir'])) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['waktu_submit'])) ?></td>
          </tr>
          <?php endwhile; ?>
          <?php if($total == 0): ?>
          <tr>
            <td colspan="5" style="text-align: center;">Belum ada data pendaftar</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>