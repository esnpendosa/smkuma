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

$id = $_GET['id'] ?? 0;

// VALIDASI ID
if (!is_numeric($id) || $id <= 0) {
    die("ID tidak valid!");
}

// QUERY UNTUK MENGAMBIL DATA LENGKAP PENDAFTAR
$query = "
    SELECT 
        ps.*,
        a.asal_sekolah, a.tahun_lulus, a.rata_rata_raport,
        jb.pilihan_jurusan, jb.pilihan_beasiswa,
        ow.nama_ayah, ow.pekerjaan_ayah, ow.nohp_ayah,
        ow.nama_ibu, ow.pekerjaan_ibu, ow.nohp_ibu,
        ow.nama_wali, ow.pekerjaan_wali, ow.nohp_wali,
        al.provinsi, al.kota, al.kecamatan, al.alamat_lengkap,
        d.sk_lulus, d.kk, d.akta_lahir, d.pas_foto, d.ktp_ortu_wali, d.sertifikat_prestasi
    FROM pendaftaran_siswa ps
    LEFT JOIN akademik a ON ps.id = a.siswa_id
    LEFT JOIN jurusan_beasiswa jb ON ps.id = jb.siswa_id
    LEFT JOIN orangtua_wali ow ON ps.id = ow.siswa_id
    LEFT JOIN alamat_siswa al ON ps.id = al.siswa_id
    LEFT JOIN dokumen d ON ps.id = d.siswa_id
    WHERE ps.id = ?
";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Error dalam query: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data pendaftar dengan ID $id tidak ditemukan!");
}

// FUNGSI UNTUK MENGECEK FILE EXISTS - DIPERBAIKI
function checkFileExists($filepath) {
    if (empty($filepath) || $filepath === 'Tidak ada data') {
        return false;
    }
    
    // Extract filename dari path database
    $filename = basename($filepath);
    
    // Path utama: Form/uploads/filename
    $main_path = dirname(__DIR__) . '/Form/uploads/' . $filename;
    
    // Cek path utama
    if (file_exists($main_path) && is_file($main_path) && filesize($main_path) > 0) {
        return $main_path;
    }
    
    // Cek path alternatif
    $alternate_paths = [
        $_SERVER['DOCUMENT_ROOT'] . '/ppdbsmkum/Form/uploads/' . $filename,
        __DIR__ . '/../Form/uploads/' . $filename,
        $filepath // Path asli dari database
    ];
    
    foreach ($alternate_paths as $path) {
        if (file_exists($path) && is_file($path) && filesize($path) > 0) {
            return $path;
        }
    }
    
    return false;
}

// FUNGSI UNTUK MEMBUAT URL FILE - DIPERBAIKI TOTAL
function getFileUrl($filepath) {
    if (empty($filepath)) return '';
    
    // Extract hanya filename dari path database
    $filename = basename($filepath);
    
    // Buat URL langsung ke Form/uploads/filename
    // Dari Admin/ ke Form/uploads/
    $base_url = rtrim(dirname($_SERVER['PHP_SELF']), '/');
    
    // Jika base_url sudah mengandung 'Admin', kita perlu naik satu level
    if (strpos($base_url, 'Admin') !== false) {
        return $base_url . '/../Form/uploads/' . $filename;
    } else {
        return $base_url . '/Form/uploads/' . $filename;
    }
}

// FUNGSI UNTUK MEMBUAT ABSOLUTE URL - FUNGSI BARU
function getAbsoluteFileUrl($filepath) {
    if (empty($filepath)) return '';
    
    $filename = basename($filepath);
    
    // Buat absolute URL berdasarkan server
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Dari http://localhost/ppdbsmkum/Admin/ ke http://localhost/ppdbsmkum/Form/uploads/
    return $protocol . '://' . $host . '/ppdbsmkum/Form/uploads/' . $filename;
}

// FUNGSI UNTUK HANDLE DATA KOSONG
function getData($data, $key, $default = 'Tidak ada data') {
    if (!isset($data[$key]) || empty($data[$key]) || $data[$key] === '0000-00-00') {
        return $default;
    }
    return htmlspecialchars($data[$key]);
}

// FUNGSI UNTUK FORMAT TANGGAL
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date) || $date === '0000-00-00') {
        return 'Tidak ada data';
    }
    
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return 'Format tanggal tidak valid';
    }
    
    return date($format, $timestamp);
}

// FUNGSI UNTUK MENDAPATKAN UKURAN FILE
function getFileSize($filepath) {
    $real_path = checkFileExists($filepath);
    if ($real_path) {
        $size = filesize($real_path);
        if ($size === false) return 'Unknown';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;
        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }
        return round($size, 2) . ' ' . $units[$index];
    }
    return 'Unknown';
}

// FUNGSI UNTUK MENDAPATKAN TIPE FILE
function getFileType($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $image_types = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    $doc_types = ['pdf', 'doc', 'docx'];
    $archive_types = ['zip', 'rar'];
    
    if (in_array($extension, $image_types)) return 'image';
    if (in_array($extension, $doc_types)) return 'document';
    if (in_array($extension, $archive_types)) return 'archive';
    
    return 'other';
}

// FUNGSI UNTUK MEMERIKSA FOLDER UPLOADS
function checkUploadsFolder() {
    $uploads_path = dirname(__DIR__) . '/Form/uploads/';
    if (!is_dir($uploads_path)) {
        return "❌ Folder uploads tidak ditemukan: " . $uploads_path;
    }
    
    $files = scandir($uploads_path);
    $file_count = count($files) - 2; // Exclude . and ..
    
    return "✅ Folder uploads OK. Terdapat $file_count file.";
}

// FUNGSI UNTUK MENDAPATKAN LIST FILE DI UPLOADS - FUNGSI BARU
function getUploadsFileList() {
    $uploads_path = dirname(__DIR__) . '/Form/uploads/';
    $files = [];
    
    if (is_dir($uploads_path)) {
        $file_list = scandir($uploads_path);
        foreach ($file_list as $file) {
            if ($file !== '.' && $file !== '..') {
                $files[] = $file;
            }
        }
    }
    
    return $files;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pendaftar | PPDB SMK UMAR MAS'UD</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            color: #004080;
            margin: 0;
        }
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            transition: background 0.3s;
        }
        .btn-back:hover {
            background: #5a6268;
        }
        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .section h2 {
            color: #004080;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .info-item {
            margin-bottom: 15px;
        }
        .info-item label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .info-item span {
            color: #666;
            display: block;
            padding: 10px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #004080;
            word-break: break-word;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-jurusan {
            background: #e3f2fd;
            color: #004080;
            border: 1px solid #004080;
        }
        .badge-beasiswa {
            background: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #2e7d32;
        }
        .document-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
        }
        .document-item {
            text-align: center;
            padding: 20px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #fafafa;
        }
        .document-item:hover {
            border-color: #004080;
            background: #f0f8ff;
            transform: translateY(-2px);
        }
        .document-item strong {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }
        .document-item a {
            color: #004080;
            text-decoration: none;
            font-weight: 600;
            display: block;
            margin: 5px 0;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .document-item a:hover {
            background: #e3f2fd;
            text-decoration: none;
        }
        .empty-doc {
            color: #999;
            font-style: italic;
            display: block;
            margin-top: 5px;
            padding: 8px;
        }
        .file-info {
            font-size: 11px;
            color: #666;
            margin-top: 8px;
            word-break: break-all;
            background: #f0f0f0;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .btn-download {
            background: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 11px;
            display: inline-block;
            margin: 2px;
            transition: background 0.3s;
        }
        .btn-download:hover {
            background: #218838;
        }
        .btn-view {
            background: #17a2b8;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 11px;
            display: inline-block;
            margin: 2px;
            transition: background 0.3s;
        }
        .btn-view:hover {
            background: #138496;
        }
        .file-missing {
            color: #dc3545;
            font-size: 11px;
            margin-top: 5px;
            background: #ffe6e6;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .data-count {
            background: #004080;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
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
        .document-status {
            font-size: 12px;
            margin-top: 5px;
            padding: 3px 8px;
            border-radius: 4px;
        }
        .status-uploaded {
            background: #d4edda;
            color: #155724;
        }
        .status-missing {
            background: #f8d7da;
            color: #721c24;
        }
        .file-preview {
            max-width: 100%;
            max-height: 150px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .file-details {
            font-size: 10px;
            color: #888;
            margin-top: 5px;
        }
        .folder-info {
            background: #e7f3ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 12px;
            border-left: 4px solid #004080;
        }
        .url-debug {
            background: #fff3cd;
            padding: 8px;
            border-radius: 4px;
            font-size: 10px;
            margin-top: 5px;
            word-break: break-all;
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
            <h1>Detail Data Pendaftar 
                <span class="data-count">ID: <?= $id ?></span>
            </h1>
            <a href="data_pendaftar.php" class="btn-back">← Kembali ke Data Pendaftar</a>
        </div>

        <!-- Info Folder Uploads -->
        <div class="folder-info">
            <strong>📁 Info Folder Uploads:</strong> <?= checkUploadsFolder() ?>
        </div>

        <!-- Data Pribadi -->
        <div class="section">
            <h2>📝 Data Pribadi</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Nama Lengkap</label>
                    <span><?= getData($data, 'nama_lengkap') ?></span>
                </div>
                <div class="info-item">
                    <label>NIK</label>
                    <span><?= getData($data, 'nik') ?></span>
                </div>
                <div class="info-item">
                    <label>Jenis Kelamin</label>
                    <span><?= getData($data, 'jenis_kelamin') ?></span>
                </div>
                <div class="info-item">
                    <label>Agama</label>
                    <span><?= getData($data, 'agama') ?></span>
                </div>
                <div class="info-item">
                    <label>Tanggal Lahir</label>
                    <span><?= formatDate($data['tanggal_lahir'] ?? '') ?></span>
                </div>
                <div class="info-item">
                    <label>No. HP</label>
                    <span><?= getData($data, 'no_hp') ?></span>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <span><?= getData($data, 'email') ?></span>
                </div>
                <div class="info-item">
                    <label>Tanggal Daftar</label>
                    <span><?= formatDate($data['waktu_submit'] ?? '', 'd/m/Y H:i') ?></span>
                </div>
            </div>
        </div>

        <!-- Data Alamat -->
        <div class="section">
            <h2>🏠 Data Alamat</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Provinsi</label>
                    <span><?= getData($data, 'provinsi') ?></span>
                </div>
                <div class="info-item">
                    <label>Kota/Kabupaten</label>
                    <span><?= getData($data, 'kota') ?></span>
                </div>
                <div class="info-item">
                    <label>Kecamatan</label>
                    <span><?= getData($data, 'kecamatan') ?></span>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <label>Alamat Lengkap</label>
                    <span><?= getData($data, 'alamat_lengkap') ?></span>
                </div>
            </div>
        </div>

        <!-- Data Akademik -->
        <div class="section">
            <h2>📚 Data Akademik</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Asal Sekolah</label>
                    <span><?= getData($data, 'asal_sekolah') ?></span>
                </div>
                <div class="info-item">
                    <label>Tahun Lulus</label>
                    <span><?= ($data['tahun_lulus'] ?? '0000') != '0000' ? $data['tahun_lulus'] : 'Tidak ada data' ?></span>
                </div>
                <div class="info-item">
                    <label>Rata-rata Nilai Raport</label>
                    <span><?= !empty($data['rata_rata_raport']) ? number_format((float)$data['rata_rata_raport'], 2) : '0.00' ?></span>
                </div>
            </div>
        </div>

        <!-- Pilihan Jurusan & Beasiswa -->
        <div class="section">
            <h2>🎯 Pilihan Jurusan & Beasiswa</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Pilihan Jurusan</label>
                    <?php if(!empty($data['pilihan_jurusan'])): ?>
                        <span class="badge badge-jurusan"><?= htmlspecialchars($data['pilihan_jurusan']) ?></span>
                    <?php else: ?>
                        <span style="color: #999;">Belum memilih</span>
                    <?php endif; ?>
                </div>
                <div class="info-item">
                    <label>Pilihan Beasiswa</label>
                    <?php if(!empty($data['pilihan_beasiswa'])): ?>
                        <span class="badge badge-beasiswa"><?= htmlspecialchars($data['pilihan_beasiswa']) ?></span>
                    <?php else: ?>
                        <span style="color: #999;">Tidak mengajukan</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Data Orang Tua/Wali -->
        <div class="section">
            <h2>👨‍👩‍👧‍👦 Data Orang Tua/Wali</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Nama Ayah</label>
                    <span><?= getData($data, 'nama_ayah') ?></span>
                </div>
                <div class="info-item">
                    <label>Pekerjaan Ayah</label>
                    <span><?= getData($data, 'pekerjaan_ayah') ?></span>
                </div>
                <div class="info-item">
                    <label>No. HP Ayah</label>
                    <span><?= getData($data, 'nohp_ayah') ?></span>
                </div>
                <div class="info-item">
                    <label>Nama Ibu</label>
                    <span><?= getData($data, 'nama_ibu') ?></span>
                </div>
                <div class="info-item">
                    <label>Pekerjaan Ibu</label>
                    <span><?= getData($data, 'pekerjaan_ibu') ?></span>
                </div>
                <div class="info-item">
                    <label>No. HP Ibu</label>
                    <span><?= getData($data, 'nohp_ibu') ?></span>
                </div>
                <?php if(!empty($data['nama_wali'])): ?>
                <div class="info-item">
                    <label>Nama Wali</label>
                    <span><?= getData($data, 'nama_wali') ?></span>
                </div>
                <div class="info-item">
                    <label>Pekerjaan Wali</label>
                    <span><?= getData($data, 'pekerjaan_wali', 'Tidak ada data') ?></span>
                </div>
                <div class="info-item">
                    <label>No. HP Wali</label>
                    <span><?= getData($data, 'nohp_wali') ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Dokumen -->
        <div class="section">
            <h2>📎 Dokumen</h2>
            <div class="document-list">
                <?php
                $documents = [
                    'sk_lulus' => 'Surat Keterangan Lulus',
                    'kk' => 'Kartu Keluarga', 
                    'akta_lahir' => 'Akta Lahir',
                    'pas_foto' => 'Pas Foto',
                    'ktp_ortu_wali' => 'KTP Orang Tua/Wali',
                    'sertifikat_prestasi' => 'Sertifikat Prestasi'
                ];
                
                foreach ($documents as $key => $label): 
                    $file_path = $data[$key] ?? '';
                    $file_exists = checkFileExists($file_path);
                    $file_url = getFileUrl($file_path);
                    $absolute_url = getAbsoluteFileUrl($file_path);
                    $filename = basename($file_path);
                    $is_sertifikat = ($key === 'sertifikat_prestasi');
                    $file_type = getFileType($filename);
                    $file_size = getFileSize($file_path);
                ?>
                <div class="document-item">
                    <strong><?= $label ?></strong>
                    <?php if($file_exists && !empty($file_path)): ?>
                        <!-- Preview untuk gambar -->
                        <?php if($file_type === 'image'): ?>
                            <img src="<?= $absolute_url ?>" alt="Preview <?= $label ?>" class="file-preview" 
                                 onerror="this.style.display='none'">
                        <?php endif; ?>
                        
                        <div class="action-buttons">
                            <a href="<?= $absolute_url ?>" target="_blank" class="btn-view" 
                               data-filename="<?= $filename ?>">👁️ Lihat</a>
                            <a href="<?= $absolute_url ?>" download class="btn-download">📥 Download</a>
                        </div>
                        <div class="file-info">
                            📄 <?= htmlspecialchars($filename) ?><br>
                            <span class="file-details">
                                Ukuran: <?= $file_size ?><br>
                                Tipe: <?= strtoupper($file_type) ?>
                            </span>
                        </div>
                        <div class="url-debug" title="URL untuk debugging">
                            🔗 <?= $absolute_url ?>
                        </div>
                        <div class="document-status status-uploaded">✓ File Ditemukan di Server</div>
                    <?php else: ?>
                        <span class="empty-doc">❌ <?= $is_sertifikat ? 'Tidak ada' : 'Belum diupload' ?></span>
                        <?php if(!empty($file_path)): ?>
                            <div class="file-missing">
                                ⚠️ File di database: <?= htmlspecialchars($file_path) ?><br>
                                📁 Mencari di: Form/uploads/<?= htmlspecialchars($filename) ?>
                            </div>
                        <?php endif; ?>
                        <div class="document-status status-missing">✗ <?= $is_sertifikat ? 'Tidak ada' : 'Tidak Ditemukan' ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Debug Info -->
        <div class="section">
            <h2>🐛 Debug Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Base URL</label>
                    <span><?= dirname($_SERVER['PHP_SELF']) ?></span>
                </div>
                <div class="info-item">
                    <label>Document Root</label>
                    <span><?= $_SERVER['DOCUMENT_ROOT'] ?></span>
                </div>
                <div class="info-item">
                    <label>Current Directory</label>
                    <span><?= __DIR__ ?></span>
                </div>
                <div class="info-item">
                    <label>Uploads Path</label>
                    <span><?= dirname(__DIR__) . '/Form/uploads/' ?></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk test URL sebelum halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Testing file URLs...');
            
            const docLinks = document.querySelectorAll('.document-item a.btn-view');
            docLinks.forEach(link => {
                const url = link.href;
                const filename = link.getAttribute('data-filename');
                
                console.log('Testing URL:', url);
                
                // Test dengan Image object untuk gambar
                if (url.match(/\.(jpg|jpeg|png|gif)$/i)) {
                    const img = new Image();
                    img.onload = function() {
                        console.log('✅ Image loaded:', filename);
                        link.innerHTML = '👁️ Lihat';
                        link.style.opacity = '1';
                    };
                    img.onerror = function() {
                        console.log('❌ Image failed:', filename);
                        link.innerHTML = '👁️ File Hilang';
                        link.style.opacity = '0.6';
                        link.style.pointerEvents = 'none';
                    };
                    img.src = url;
                }
            });
        });

        // Auto refresh untuk data real-time (opsional)
        setTimeout(() => {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>