<?php
session_start();

// KONFIGURASI DATABASE
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ppdb_smk_um');

// KONEKSI DATABASE
$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// FUNGSI UTILITAS
function sanitize($data) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, htmlspecialchars(trim($data)));
}

function set_message($type, $message) {
    $_SESSION['message'] = ['type' => $type, 'text' => $message];
}

function show_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $class = $message['type'] == 'error' ? 'alert-error' : 'alert-success';
        echo "<div class='alert $class'>" . $message['text'] . "</div>";
        unset($_SESSION['message']);
    }
}

// CEK LOGIN ADMIN
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// PROSES HAPUS DATA
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if ($id > 0) {
        // Mulai transaction
        mysqli_begin_transaction($koneksi);
        
        try {
            // Ambil data dokumen untuk menghapus file fisik - DIPERBAIKI PATH
            $doc_query = "SELECT sk_lulus, kk, akta_lahir, pas_foto, ktp_ortu_wali, sertifikat_prestasi FROM dokumen WHERE siswa_id = ?";
            $doc_stmt = $koneksi->prepare($doc_query);
            
            if ($doc_stmt) {
                $doc_stmt->bind_param("i", $id);
                
                if ($doc_stmt->execute()) {
                    $doc_result = $doc_stmt->get_result();
                    $doc_data = $doc_result->fetch_assoc();
                    
                    // Hapus file fisik jika ada - DIPERBAIKI PATH
                    if ($doc_data) {
                        $upload_dir = __DIR__ . '/../Form/uploads/'; // DIPERBAIKI: Form/uploads/
                        
                        $documents = [
                            'sk_lulus' => $doc_data['sk_lulus'],
                            'kk' => $doc_data['kk'],
                            'akta_lahir' => $doc_data['akta_lahir'],
                            'pas_foto' => $doc_data['pas_foto'],
                            'ktp_ortu_wali' => $doc_data['ktp_ortu_wali'],
                            'sertifikat_prestasi' => $doc_data['sertifikat_prestasi']
                        ];
                        
                        foreach ($documents as $doc_type => $doc_path) {
                            if (!empty($doc_path)) {
                                $filename = basename($doc_path);
                                $possible_paths = [
                                    $upload_dir . $filename, // Path utama: Form/uploads/filename
                                    __DIR__ . '/../' . $doc_path, // Path relatif
                                    $upload_dir . $doc_path // Full path
                                ];
                                
                                foreach ($possible_paths as $file_path) {
                                    if (file_exists($file_path) && is_file($file_path)) {
                                        if (unlink($file_path)) {
                                            error_log("✅ File deleted: " . basename($file_path));
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                $doc_stmt->close();
            }
            
            // Hapus data dari semua tabel terkait - DIPERBAIKI URUTAN DAN KOLOM
            $tables = [
                'dokumen' => 'siswa_id',
                'orangtua_wali' => 'siswa_id', 
                'alamat_siswa' => 'siswa_id',
                'jurusan_beasiswa' => 'siswa_id',
                'akademik' => 'siswa_id',
                'pendaftaran_siswa' => 'id' // DIPERBAIKI: menggunakan 'id' bukan 'siswa_id'
            ];
            
            foreach ($tables as $table => $column) {
                $query = "DELETE FROM $table WHERE $column = ?";
                $stmt = $koneksi->prepare($query);
                
                if ($stmt) {
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute()) {
                        error_log("✅ Deleted from $table where $column = $id");
                    } else {
                        throw new Exception("Error deleting from $table: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    throw new Exception("Error preparing delete for $table: " . $koneksi->error);
                }
            }
            
            // Commit transaction
            mysqli_commit($koneksi);
            set_message('success', "✅ Data pendaftar berhasil dihapus! (ID: $id)");
            
        } catch (Exception $e) {
            // Rollback transaction jika ada error
            mysqli_rollback($koneksi);
            set_message('error', "❌ Gagal menghapus data: " . $e->getMessage());
            error_log("❌ Delete error: " . $e->getMessage());
        }
    } else {
        set_message('error', "❌ ID tidak valid!");
    }
    
    header("Location: data_pendaftar.php");
    exit();
}

// QUERY UNTUK MENGAMBIL DATA PENDAFTAR
$query = "
    SELECT 
        ps.id,
        ps.nama_lengkap,
        ps.nik,
        ps.jenis_kelamin,
        ps.tanggal_lahir,
        ps.email,
        ps.no_hp,
        ps.waktu_submit,
        a.asal_sekolah,
        a.tahun_lulus,
        a.rata_rata_raport,
        jb.pilihan_jurusan,
        jb.pilihan_beasiswa,
        ow.nama_ayah,
        ow.nama_ibu,
        al.provinsi,
        al.kota,
        al.kecamatan
    FROM pendaftaran_siswa ps
    LEFT JOIN akademik a ON ps.id = a.siswa_id
    LEFT JOIN jurusan_beasiswa jb ON ps.id = jb.siswa_id
    LEFT JOIN orangtua_wali ow ON ps.id = ow.siswa_id
    LEFT JOIN alamat_siswa al ON ps.id = al.siswa_id
    ORDER BY ps.waktu_submit DESC
";

$data = mysqli_query($koneksi, $query);

// CEK ERROR QUERY
if (!$data) {
    die("Error dalam query: " . mysqli_error($koneksi));
}

// HITUNG STATISTIK
$total = mysqli_num_rows($data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaftar | PPDB SMK UMAR MAS'UD</title>
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
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #004080;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
            overflow-y: auto;
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
        .sidebar a:hover, .sidebar a.active {
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
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .header h1 {
            color: #004080;
            margin-bottom: 10px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 600;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .stats-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 200px;
            text-align: center;
        }
        .stat-item h3 {
            color: #004080;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .stat-item p {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }
        .table-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }
        table th {
            background: #004080;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover {
            background: #f8f9fa;
        }
        table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-jurusan {
            background: #e3f2fd;
            color: #004080;
        }
        .badge-beasiswa {
            background: #e8f5e8;
            color: #2e7d32;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
        }
        .btn-view {
            background: #17a2b8;
            color: white;
        }
        .btn-view:hover {
            background: #138496;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background: #c82333;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .empty-state img {
            width: 100px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 18px;
        }
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #004080;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            margin-bottom: 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
            .content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
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
        <?php show_message(); ?>

        <div class="header">
            <h1>Data Semua Pendaftar</h1>
            <p>Berikut adalah data lengkap semua siswa yang telah mendaftar PPDB SMK UMAR MAS'UD</p>
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <h3>Total Pendaftar</h3>
                <p><?= $total; ?></p>
            </div>
            <div class="stat-item">
                <h3>Terakhir Update</h3>
                <p><?= date('d/m/Y H:i'); ?></p>
            </div>
            <div class="stat-item">
                <h3>Status Sistem</h3>
                <p style="color: #28a745;">✓ Aktif</p>
            </div>
        </div>

        <div class="table-container">
            <?php if($total > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>NIK</th>
                        <th>Jenis Kelamin</th>
                        <th>Asal Sekolah</th>
                        <th>Jurusan</th>
                        <th>Nilai Raport</th>
                        <th>Beasiswa</th>
                        <th>Orang Tua</th>
                        <th>Kota</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    mysqli_data_seek($data, 0);
                    while($d = mysqli_fetch_array($data)): 
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($d['nama_lengkap'] ?? 'Tidak ada data'); ?></strong></td>
                        <td><?= htmlspecialchars($d['nik'] ?? 'Tidak ada data'); ?></td>
                        <td><?= htmlspecialchars($d['jenis_kelamin'] ?? 'Tidak ada data'); ?></td>
                        <td><?= htmlspecialchars($d['asal_sekolah'] ?? '-'); ?></td>
                        <td>
                            <span class="badge badge-jurusan">
                                <?= htmlspecialchars($d['pilihan_jurusan'] ?? 'Belum memilih'); ?>
                            </span>
                        </td>
                        <td><strong><?= isset($d['rata_rata_raport']) ? number_format($d['rata_rata_raport'], 2) : '0.00'; ?></strong></td>
                        <td>
                            <?php if(!empty($d['pilihan_beasiswa'])): ?>
                                <span class="badge badge-beasiswa">
                                    <?= htmlspecialchars($d['pilihan_beasiswa']); ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small>
                                Ayah: <?= htmlspecialchars($d['nama_ayah'] ?? '-'); ?><br>
                                Ibu: <?= htmlspecialchars($d['nama_ibu'] ?? '-'); ?>
                            </small>
                        </td>
                        <td><?= htmlspecialchars($d['kota'] ?? '-'); ?></td>
                        <td><?= !empty($d['waktu_submit']) ? date('d/m/Y H:i', strtotime($d['waktu_submit'])) : '-'; ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="detail_pendaftar.php?id=<?= $d['id']; ?>" class="btn btn-view" title="Lihat Detail">👁️ Lihat</a>
                                <button class="btn btn-delete" onclick="hapusData(<?= $d['id']; ?>, '<?= htmlspecialchars(addslashes($d['nama_lengkap'] ?? '')); ?>')" title="Hapus Data">🗑️ Hapus</button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23666'%3E%3Cpath d='M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z'/%3E%3C/svg%3E" alt="No data">
                <h3>Belum ada data pendaftar</h3>
                <p>Data pendaftar akan muncul di sini setelah siswa melakukan pendaftaran.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Loading overlay -->
    <div class="loading" id="loading">
        <div style="text-align: center;">
            <div class="loading-spinner"></div>
            <div>Menghapus data...</div>
        </div>
    </div>

    <script>
        function hapusData(id, nama) {
            if(confirm('Apakah Anda yakin ingin menghapus data pendaftar:\n\n' + nama + '\n\nID: ' + id + '\n\nTindakan ini tidak dapat dibatalkan!')) {
                // Tampilkan loading
                document.getElementById('loading').style.display = 'flex';
                
                // Redirect ke halaman hapus
                window.location.href = 'data_pendaftar.php?action=hapus&id=' + id;
            }
        }

        // Sembunyikan loading setelah halaman dimuat
        window.addEventListener('load', function() {
            document.getElementById('loading').style.display = 'none';
        });

        // Auto hide loading setelah 10 detik (safety net)
        setTimeout(function() {
            document.getElementById('loading').style.display = 'none';
        }, 10000);
    </script>
</body>
</html>