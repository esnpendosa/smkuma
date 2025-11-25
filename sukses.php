<?php
session_start();

// Include koneksi dengan path yang benar
$koneksi_path = __DIR__ . '/Database/koneksi.php';
if (!file_exists($koneksi_path)) {
    // Coba path alternatif
    $koneksi_path = __DIR__ . '/../Database/koneksi.php';
}

if (!file_exists($koneksi_path)) {
    die("File koneksi database tidak ditemukan! Path yang dicoba: " . $koneksi_path);
}

include $koneksi_path;

// Cek apakah koneksi berhasil - PERBAIKAN: Pisahkan pengecekan
$koneksi_valid = isset($koneksi) && $koneksi;
if (!$koneksi_valid) {
    $error_msg = "Koneksi database gagal";
    if (function_exists('mysqli_connect_error')) {
        $error_msg .= ": " . mysqli_connect_error();
    } else {
        $error_msg .= ": Unknown error";
    }
    die($error_msg);
}

$siswa_id = $_GET['id'] ?? 0;

// Debug: Tampilkan ID yang diterima
error_log("ID Siswa yang diterima di sukses.php: " . $siswa_id);

// Jika ID tidak valid, coba ambil dari session atau POST data
if ($siswa_id <= 0) {
    // Coba dari session
    if (isset($_SESSION['last_siswa_id'])) {
        $siswa_id = $_SESSION['last_siswa_id'];
        error_log("Menggunakan ID dari session: " . $siswa_id);
    }
    // Coba dari POST (jika ada)
    elseif (isset($_POST['siswa_id'])) {
        $siswa_id = $_POST['siswa_id'];
        error_log("Menggunakan ID dari POST: " . $siswa_id);
    }
}

// Validasi ID siswa
if ($siswa_id <= 0) {
    // Jika masih tidak valid, tampilkan form untuk input manual
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ID Tidak Valid - PPDB SMK UMAR MAS'UD</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #004080, #0073e6);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                text-align: center;
                max-width: 500px;
                width: 100%;
            }
            .error-icon {
                font-size: 60px;
                color: #dc3545;
                margin-bottom: 20px;
            }
            h1 {
                color: #dc3545;
                margin-bottom: 15px;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #004080;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin: 10px 5px;
            }
            .btn:hover {
                background: #003366;
            }
            .debug-info {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                margin-top: 20px;
                font-size: 12px;
                text-align: left;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="error-icon">⚠️</div>
            <h1>ID Pendaftaran Tidak Valid</h1>
            <p>Maaf, ID pendaftaran tidak dapat ditemukan atau tidak valid.</p>
            <p>Silakan hubungi administrator atau coba lagi.</p>
            
            <div class="debug-info">
                <strong>Informasi Debug:</strong><br>
                ID yang diterima: <?= htmlspecialchars($_GET['id'] ?? 'Tidak ada') ?><br>
                Session ID: <?= isset($_SESSION['last_siswa_id']) ? $_SESSION['last_siswa_id'] : 'Tidak ada' ?><br>
                Waktu: <?= date('Y-m-d H:i:s') ?>
            </div>
            
            <div style="margin-top: 20px;">
                <a href="../index.php" class="btn">Kembali ke Beranda</a>
                <a href="../Form/form-siswa.php" class="btn">Daftar Ulang</a>
                <a href="mailto:admin@smkumarmasud.sch.id" class="btn">Hubungi Admin</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Query untuk mengambil data siswa
$query = "SELECT nama_lengkap, waktu_submit FROM pendaftaran_siswa WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);

if (!$stmt) {
    die("Error dalam persiapan query: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $siswa_id);

if (!mysqli_stmt_execute($stmt)) {
    die("Error dalam eksekusi query: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan
if (!$siswa) {
    mysqli_stmt_close($stmt);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Data Tidak Ditemukan - PPDB SMK UMAR MAS'UD</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #004080, #0073e6);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                text-align: center;
                max-width: 500px;
                width: 100%;
            }
            .warning-icon {
                font-size: 60px;
                color: #ffc107;
                margin-bottom: 20px;
            }
            h1 {
                color: #856404;
                margin-bottom: 15px;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #004080;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin: 10px 5px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="warning-icon">🔍</div>
            <h1>Data Tidak Ditemukan</h1>
            <p>Data untuk ID pendaftaran <strong><?= $siswa_id ?></strong> tidak ditemukan dalam database.</p>
            <p>Silakan verifikasi ID pendaftaran atau hubungi administrator.</p>
            
            <div style="margin-top: 20px;">
                <a href="../index.php" class="btn">Kembali ke Beranda</a>
                <a href="mailto:admin@smkumarmasud.sch.id" class="btn">Hubungi Admin</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

$nama_lengkap = htmlspecialchars($siswa['nama_lengkap'] ?? '');
$waktu_daftar = $siswa['waktu_submit'] ?? date('Y-m-d H:i:s');
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - PPDB SMK UMAR MAS'UD</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #004080, #0073e6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 40px;
            color: white;
        }
        h1 {
            color: #28a745;
            margin-bottom: 15px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
            text-align: left;
            border-left: 4px solid #004080;
        }
        .info-box h3 {
            color: #004080;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .info-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .info-item i {
            color: #28a745;
            margin-right: 10px;
            font-size: 14px;
        }
        .student-id {
            background: #004080;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 150px;
        }
        .btn-primary {
            background: #004080;
            color: white;
        }
        .btn-primary:hover {
            background: #003366;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        .btn-outline {
            background: transparent;
            color: #004080;
            border: 2px solid #004080;
        }
        .btn-outline:hover {
            background: #004080;
            color: white;
            transform: translateY(-2px);
        }
        .next-steps {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .next-steps h4 {
            color: #004080;
            margin-bottom: 15px;
        }
        .steps-list {
            text-align: left;
            color: #666;
            font-size: 14px;
        }
        .steps-list li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }
        .steps-list li:before {
            content: "•";
            color: #004080;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        .debug-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin-top: 20px;
            font-size: 12px;
            color: #856404;
            text-align: left;
        }
        @media (max-width: 480px) {
            .container {
                padding: 25px;
            }
            .btn-group {
                flex-direction: column;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        
        <h1>Pendaftaran Berhasil!</h1>
        <p class="subtitle">Selamat, data Anda telah terdaftar dalam sistem PPDB SMK UMAR MAS'UD</p>
        
        <div class="info-box">
            <h3>📋 Detail Pendaftaran</h3>
            <div class="info-item">
                <i>👤</i> <strong>Nama Lengkap:</strong> <?php echo $nama_lengkap; ?>
            </div>
            <div class="info-item">
                <i>🆔</i> <strong>ID Pendaftaran:</strong> 
                <span class="student-id">PPDB-<?php echo str_pad($siswa_id, 4, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="info-item">
                <i>📅</i> <strong>Tanggal Pendaftaran:</strong> <?php echo date('d F Y', strtotime($waktu_daftar)); ?>
            </div>
            <div class="info-item">
                <i>⏰</i> <strong>Waktu Pendaftaran:</strong> <?php echo date('H:i', strtotime($waktu_daftar)); ?> WIB
            </div>
        </div>

        <div class="next-steps">
            <h4>📝 Langkah Selanjutnya</h4>
            <ul class="steps-list">
                <li>Tunggu informasi seleksi melalui email/WhatsApp</li>
                <li>Pastikan data dan dokumen yang diupload sudah benar</li>
                <li>Simpan ID pendaftaran untuk keperluan verifikasi</li>
                <li>Proses seleksi akan diumumkan dalam 1-2 minggu</li>
            </ul>
        </div>

        <div class="btn-group">
            <a href="../index.php" class="btn btn-primary">
                🏠 Kembali ke Beranda
            </a>
            <a href="../Form/biodata_siswa.php" class="btn btn-outline">
                📝 Daftar Lagi
            </a>
            <a href="https://wa.me/6281234567890?text=Halo,%20saya%20<?php echo urlencode($nama_lengkap); ?>%20dengan%20ID%20PPDB-<?php echo $siswa_id; ?>%20ingin%20konfirmasi%20pendaftaran" 
               target="_blank" class="btn btn-secondary">
                💬 Konfirmasi via WhatsApp
            </a>
        </div>

        <!-- Debug info (bisa dihilangkan di production) -->
        <div class="debug-section">
            <strong>Debug Info:</strong><br>
            ID: <?php echo $siswa_id; ?> | 
            Query Success: ✅ | 
            Timestamp: <?php echo date('Y-m-d H:i:s'); ?>
        </div>

        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
            <p style="color: #666; font-size: 12px;">
                <strong>Kontak Panitia PPDB:</strong><br>
                📞 0812-3456-7890 | 📧 ppdb@smkumarmasud.sch.id<br>
                🏫 Jl. Pendidikan No. 123, Kota Gresik
            </p>
        </div>
    </div>

    <script>
        // Auto scroll ke atas
        window.scrollTo(0, 0);
        
        // Simpan ID di localStorage untuk backup
        localStorage.setItem('last_registration_id', '<?php echo $siswa_id; ?>');
        
        // Tambahkan efek confetti sederhana
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.innerHTML = '🎉';
            confetti.style.position = 'fixed';
            confetti.style.top = '20px';
            confetti.style.right = '20px';
            confetti.style.fontSize = '40px';
            confetti.style.zIndex = '1000';
            confetti.style.animation = 'bounce 2s infinite';
            document.body.appendChild(confetti);
            
            // Hapus confetti setelah 5 detik
            setTimeout(() => {
                confetti.remove();
            }, 5000);
        }, 1000);

        // Style untuk animasi
        const style = document.createElement('style');
        style.textContent = `
            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
                40% {transform: translateY(-10px);}
                60% {transform: translateY(-5px);}
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>