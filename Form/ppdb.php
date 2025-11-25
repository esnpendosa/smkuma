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

function redirect($url) {
    header("Location: $url");
    exit();
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

// PROSES FORM MULTI-STEP
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $step = $_POST['step'] ?? 'siswa';
    
    switch($step) {
        case 'siswa':
            prosesFormSiswa();
            break;
        case 'alamat':
            prosesFormAlamat();
            break;
        case 'orangtua':
            prosesFormOrangTua();
            break;
        case 'akademik':
            prosesFormAkademik();
            break;
        case 'jurusan':
            prosesFormJurusan();
            break;
        case 'dokumen':
            prosesFormDokumen();
            break;
    }
}

function prosesFormSiswa() {
    global $koneksi;
    
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $nik = sanitize($_POST['nik']);
    $jenis_kelamin = sanitize($_POST['jenis_kelamin']);
    $agama = sanitize($_POST['agama']);
    $tanggal_lahir = sanitize($_POST['tanggal_lahir']);
    $no_hp = sanitize($_POST['no_hp']);
    $email = sanitize($_POST['email']);

    // Validasi NIK (16 digit)
    if (strlen($nik) != 16 || !is_numeric($nik)) {
        set_message('error', 'NIK harus 16 digit angka');
        return;
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_message('error', 'Format email tidak valid');
        return;
    }

    // Cek duplikasi NIK
    $check_query = "SELECT id FROM pendaftaran_siswa WHERE nik = '$nik'";
    $check_result = mysqli_query($koneksi, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        set_message('error', 'NIK sudah terdaftar');
        return;
    }

    $query = "INSERT INTO pendaftaran_siswa (nama_lengkap, nik, jenis_kelamin, agama, tanggal_lahir, no_hp, email) 
              VALUES ('$nama_lengkap', '$nik', '$jenis_kelamin', '$agama', '$tanggal_lahir', '$no_hp', '$email')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['siswa_id'] = mysqli_insert_id($koneksi);
        $_SESSION['current_step'] = 'alamat';
        set_message('success', 'Data siswa berhasil disimpan!');
    } else {
        set_message('error', 'Gagal menyimpan data siswa: ' . mysqli_error($koneksi));
    }
}

function prosesFormAlamat() {
    global $koneksi;
    
    // PERBAIKAN: Validasi session sebelum digunakan
    if (!isset($_SESSION['siswa_id']) || empty($_SESSION['siswa_id'])) {
        set_message('error', 'Session tidak valid. Silakan mulai pendaftaran dari awal.');
        $_SESSION['current_step'] = 'siswa';
        return;
    }
    
    $siswa_id = (int)$_SESSION['siswa_id'];
    $provinsi = sanitize($_POST['provinsi']);
    $kota = sanitize($_POST['kota']);
    $kecamatan = sanitize($_POST['kecamatan']);
    $alamat_lengkap = sanitize($_POST['alamat_lengkap']);

    $query = "INSERT INTO alamat_siswa (siswa_id, provinsi, kota, kecamatan, alamat_lengkap) 
              VALUES ('$siswa_id', '$provinsi', '$kota', '$kecamatan', '$alamat_lengkap')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['current_step'] = 'orangtua';
        set_message('success', 'Data alamat berhasil disimpan!');
    } else {
        set_message('error', 'Gagal menyimpan data alamat: ' . mysqli_error($koneksi));
    }
}

function prosesFormOrangTua() {
    global $koneksi;
    
    // PERBAIKAN: Validasi session sebelum digunakan
    if (!isset($_SESSION['siswa_id']) || empty($_SESSION['siswa_id'])) {
        set_message('error', 'Session tidak valid. Silakan mulai pendaftaran dari awal.');
        $_SESSION['current_step'] = 'siswa';
        return;
    }
    
    $siswa_id = (int)$_SESSION['siswa_id'];
    $nama_ayah = sanitize($_POST['nama_ayah']);
    $pekerjaan_ayah = sanitize($_POST['pekerjaan_ayah']);
    $nohp_ayah = sanitize($_POST['nohp_ayah']);
    $nama_ibu = sanitize($_POST['nama_ibu']);
    $pekerjaan_ibu = sanitize($_POST['pekerjaan_ibu']);
    $nohp_ibu = sanitize($_POST['nohp_ibu']);
    $nama_wali = sanitize($_POST['nama_wali'] ?? '');
    $nohp_wali = sanitize($_POST['nohp_wali'] ?? '');

    $query = "INSERT INTO orangtua_wali (siswa_id, nama_ayah, pekerjaan_ayah, nohp_ayah, nama_ibu, pekerjaan_ibu, nohp_ibu, nama_wali, nohp_wali) 
              VALUES ('$siswa_id', '$nama_ayah', '$pekerjaan_ayah', '$nohp_ayah', '$nama_ibu', '$pekerjaan_ibu', '$nohp_ibu', '$nama_wali', '$nohp_wali')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['current_step'] = 'akademik';
        set_message('success', 'Data orang tua berhasil disimpan!');
    } else {
        set_message('error', 'Gagal menyimpan data orang tua: ' . mysqli_error($koneksi));
    }
}

function prosesFormAkademik() {
    global $koneksi;
    
    // PERBAIKAN: Validasi session sebelum digunakan
    if (!isset($_SESSION['siswa_id']) || empty($_SESSION['siswa_id'])) {
        set_message('error', 'Session tidak valid. Silakan mulai pendaftaran dari awal.');
        $_SESSION['current_step'] = 'siswa';
        return;
    }
    
    $siswa_id = (int)$_SESSION['siswa_id'];
    $asal_sekolah = sanitize($_POST['asal_sekolah']);
    $tahun_lulus = sanitize($_POST['tahun_lulus']);
    $rata_rata_raport = sanitize($_POST['rata_rata_raport']);

    // Validasi tahun lulus
    $current_year = date('Y');
    if ($tahun_lulus < 1980 || $tahun_lulus > $current_year) {
        set_message('error', 'Tahun lulus tidak valid');
        return;
    }

    // Validasi nilai
    if ($rata_rata_raport < 0 || $rata_rata_raport > 100) {
        set_message('error', 'Nilai rata-rata harus antara 0-100');
        return;
    }

    $query = "INSERT INTO akademik (siswa_id, asal_sekolah, tahun_lulus, rata_rata_raport) 
              VALUES ('$siswa_id', '$asal_sekolah', '$tahun_lulus', '$rata_rata_raport')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['current_step'] = 'jurusan';
        set_message('success', 'Data akademik berhasil disimpan!');
    } else {
        set_message('error', 'Gagal menyimpan data akademik: ' . mysqli_error($koneksi));
    }
}

function prosesFormJurusan() {
    global $koneksi;
    
    // PERBAIKAN: Validasi session sebelum digunakan
    if (!isset($_SESSION['siswa_id']) || empty($_SESSION['siswa_id'])) {
        set_message('error', 'Session tidak valid. Silakan mulai pendaftaran dari awal.');
        $_SESSION['current_step'] = 'siswa';
        return;
    }
    
    $siswa_id = (int)$_SESSION['siswa_id'];
    $pilihan_jurusan = sanitize($_POST['pilihan_jurusan']);
    $pilihan_beasiswa = sanitize($_POST['pilihan_beasiswa'] ?? '');

    $query = "INSERT INTO jurusan_beasiswa (siswa_id, pilihan_jurusan, pilihan_beasiswa) 
              VALUES ('$siswa_id', '$pilihan_jurusan', '$pilihan_beasiswa')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['current_step'] = 'dokumen';
        set_message('success', 'Data jurusan berhasil disimpan!');
    } else {
        set_message('error', 'Gagal menyimpan data jurusan: ' . mysqli_error($koneksi));
    }
}

function prosesFormDokumen() {
    global $koneksi;
    
    // PERBAIKAN: Validasi session sebelum digunakan
    if (!isset($_SESSION['siswa_id']) || empty($_SESSION['siswa_id'])) {
        set_message('error', 'Session tidak valid. Silakan mulai pendaftaran dari awal.');
        $_SESSION['current_step'] = 'siswa';
        return;
    }
    
    $siswa_id = (int)$_SESSION['siswa_id'];
    $upload_dir = __DIR__ . '/uploads/';
    
    // Buat folder uploads jika belum ada
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Fungsi upload file
    function uploadFile($file, $upload_dir, $required = true) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_types)) {
                set_message('error', 'Format file tidak diizinkan. Gunakan JPG, PNG, PDF, DOC');
                return '';
            }
            
            // Batas ukuran file 2MB
            if ($file['size'] > 2097152) {
                set_message('error', 'Ukuran file terlalu besar. Maksimal 2MB');
                return '';
            }
            
            $filename = time() . '_' . uniqid() . '.' . $file_ext;
            $target_path = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                return 'uploads/' . $filename;
            }
        } elseif ($required && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            set_message('error', 'Error upload file: ' . $file['name']);
        }
        
        return $required ? '' : null;
    }
    
    // Upload semua file
    $sk_lulus = uploadFile($_FILES['sk_lulus'], $upload_dir, true);
    $kk = uploadFile($_FILES['kk'], $upload_dir, true);
    $akta_lahir = uploadFile($_FILES['akta_lahir'], $upload_dir, true);
    $pas_foto = uploadFile($_FILES['pas_foto'], $upload_dir, true);
    $ktp_ortu_wali = uploadFile($_FILES['ktp_ortu_wali'], $upload_dir, true);
    $sertifikat_prestasi = uploadFile($_FILES['sertifikat_prestasi'], $upload_dir, false);
    
    // Cek jika ada error upload
    if (empty($sk_lulus) || empty($kk) || empty($akta_lahir) || empty($pas_foto) || empty($ktp_ortu_wali)) {
        return;
    }
    
    $query = "INSERT INTO dokumen (siswa_id, sk_lulus, kk, akta_lahir, pas_foto, ktp_ortu_wali, sertifikat_prestasi) 
              VALUES ('$siswa_id', '$sk_lulus', '$kk', '$akta_lahir', '$pas_foto', '$ktp_ortu_wali', '$sertifikat_prestasi')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['current_step'] = 'selesai';
        $_SESSION['pendaftaran_selesai'] = true;
        set_message('success', 'Pendaftaran berhasil! Data Anda telah disimpan.');
    } else {
        set_message('error', 'Gagal menyimpan data dokumen: ' . mysqli_error($koneksi));
    }
}

// TAMPILKAN FORM BERDASARKAN STEP
$current_step = $_SESSION['current_step'] ?? 'siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB SMK UMAR MAS'UD</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #004080;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin-bottom: 10px;
            font-size: 2em;
        }
        .progress-bar {
            display: flex;
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .progress-step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: bold;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        .step-active .step-number {
            background: #004080;
            color: white;
            transform: scale(1.1);
        }
        .step-completed .step-number {
            background: #28a745;
            color: white;
        }
        .step-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }
        .step-active .step-label {
            color: #004080;
            font-weight: bold;
        }
        .form-container {
            padding: 30px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
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
        .form-section {
            display: none;
        }
        .form-active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #004080;
            box-shadow: 0 0 0 3px rgba(0, 64, 128, 0.1);
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
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
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .success-page {
            text-align: center;
            padding: 50px 30px;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .file-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        /* Tambahan untuk form yang tersembunyi */
        .form-section:not(.form-active) {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PPDB SMK UMAR MAS'UD</h1>
            <p>Form Pendaftaran Peserta Didik Baru</p>
        </div>

        <?php if (!isset($_SESSION['pendaftaran_selesai'])): ?>
        <div class="progress-bar">
            <?php
            $steps = [
                'siswa' => ['Biodata', 1],
                'alamat' => ['Alamat', 2],
                'orangtua' => ['Orang Tua', 3],
                'akademik' => ['Akademik', 4],
                'jurusan' => ['Jurusan', 5],
                'dokumen' => ['Dokumen', 6]
            ];
            
            foreach ($steps as $step_key => $step_data):
                $step_class = '';
                if ($step_key == $current_step) {
                    $step_class = 'step-active';
                } elseif (array_search($current_step, array_keys($steps)) > array_search($step_key, array_keys($steps))) {
                    $step_class = 'step-completed';
                }
            ?>
            <div class="progress-step <?= $step_class ?>">
                <div class="step-number"><?= $step_data[1] ?></div>
                <div class="step-label"><?= $step_data[0] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="form-container">
            <?php show_message(); ?>

            <?php if (isset($_SESSION['pendaftaran_selesai'])): ?>
                <!-- HALAMAN SUKSES -->
                               <!-- HALAMAN SUKSES -->
                <div class="success-page">
                    <div class="success-icon">✅</div>
                    <h2 style="color: #28a745; margin-bottom: 20px;">Pendaftaran Berhasil!</h2>
                    <p style="margin-bottom: 30px; font-size: 16px; color: #666;">
                        Terima kasih telah mendaftar di SMK UMAR MAS'UD. Data Anda telah berhasil disimpan.<br>
                        Silakan tunggu informasi lebih lanjut melalui email atau WhatsApp.
                    </p>
                    <div class="btn-group" style="justify-content: center;">
                        <a href="/ppdbsmkum/index.php" class="btn btn-primary">Kembali ke Beranda</a>
                        <a href="cetak_bukti.php?id=<?= $_SESSION['siswa_id'] ?>" class="btn btn-success" target="_blank">Cetak Bukti</a>
                    </div>
                </div>

            <?php else: ?>
                <!-- FORM MULTI-STEP - SETIAP STEP FORM TERPISAH -->
                
                <!-- STEP 1: DATA SISWA -->
                <?php if ($current_step == 'siswa'): ?>
                <form method="POST" id="ppdbForm">
                    <input type="hidden" name="step" value="siswa">
                    
                    <div class="form-section form-active">
                        <h2 style="color: #004080; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">Data Pribadi Siswa</h2>
                        
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap *</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" required 
                                   value="<?= $_POST['nama_lengkap'] ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="nik">NIK (16 digit) *</label>
                            <input type="text" id="nik" name="nik" required maxlength="16"
                                   value="<?= $_POST['nik'] ?? '' ?>" pattern="[0-9]{16}">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin *</label>
                                <select id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki" <?= ($_POST['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= ($_POST['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="agama">Agama *</label>
                                <input type="text" id="agama" name="agama" required
                                       value="<?= $_POST['agama'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir *</label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" required
                                   value="<?= $_POST['tanggal_lahir'] ?? '' ?>">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label for="no_hp">No. WhatsApp *</label>
                                <input type="text" id="no_hp" name="no_hp" required
                                       value="<?= $_POST['no_hp'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required
                                       value="<?= $_POST['email'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Lanjut ke Alamat →</button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>

                <!-- STEP 2: ALAMAT -->
                <?php if ($current_step == 'alamat'): ?>
                <form method="POST" id="ppdbForm">
                    <input type="hidden" name="step" value="alamat">
                    
                    <div class="form-section form-active">
                        <h2 style="color: #004080; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">Data Alamat</h2>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label for="provinsi">Provinsi *</label>
                                <input type="text" id="provinsi" name="provinsi" required
                                       value="<?= $_POST['provinsi'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="kota">Kota/Kabupaten *</label>
                                <input type="text" id="kota" name="kota" required
                                       value="<?= $_POST['kota'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="kecamatan">Kecamatan *</label>
                                <input type="text" id="kecamatan" name="kecamatan" required
                                       value="<?= $_POST['kecamatan'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat_lengkap">Alamat Lengkap *</label>
                            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" required><?= $_POST['alamat_lengkap'] ?? '' ?></textarea>
                        </div>

                        <div class="btn-group">
                            <a href="?step=siswa" class="btn btn-secondary">← Kembali</a>
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Lanjut ke Orang Tua →</button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>

                <!-- STEP 3: ORANG TUA -->
                <?php if ($current_step == 'orangtua'): ?>
                <form method="POST" id="ppdbForm">
                    <input type="hidden" name="step" value="orangtua">
                    
                    <div class="form-section form-active">
                        <h2 style="color: #004080; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">Data Orang Tua/Wali</h2>
                        
                        <h3 style="margin: 20px 0 15px; color: #333;">Data Ayah</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label for="nama_ayah">Nama Ayah *</label>
                                <input type="text" id="nama_ayah" name="nama_ayah" required
                                       value="<?= $_POST['nama_ayah'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="pekerjaan_ayah">Pekerjaan Ayah *</label>
                                <input type="text" id="pekerjaan_ayah" name="pekerjaan_ayah" required
                                       value="<?= $_POST['pekerjaan_ayah'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="nohp_ayah">No. HP Ayah *</label>
                                <input type="text" id="nohp_ayah" name="nohp_ayah" required
                                       value="<?= $_POST['nohp_ayah'] ?? '' ?>">
                            </div>
                        </div>

                        <h3 style="margin: 20px 0 15px; color: #333;">Data Ibu</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label for="nama_ibu">Nama Ibu *</label>
                                <input type="text" id="nama_ibu" name="nama_ibu" required
                                       value="<?= $_POST['nama_ibu'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="pekerjaan_ibu">Pekerjaan Ibu *</label>
                                <input type="text" id="pekerjaan_ibu" name="pekerjaan_ibu" required
                                       value="<?= $_POST['pekerjaan_ibu'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="nohp_ibu">No. HP Ibu *</label>
                                <input type="text" id="nohp_ibu" name="nohp_ibu" required
                                       value="<?= $_POST['nohp_ibu'] ?? '' ?>">
                            </div>
                        </div>

                        <h3 style="margin: 20px 0 15px; color: #333;">Data Wali (Opsional)</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label for="nama_wali">Nama Wali</label>
                                <input type="text" id="nama_wali" name="nama_wali"
                                       value="<?= $_POST['nama_wali'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="nohp_wali">No. HP Wali</label>
                                <input type="text" id="nohp_wali" name="nohp_wali"
                                       value="<?= $_POST['nohp_wali'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="btn-group">
                            <a href="?step=alamat" class="btn btn-secondary">← Kembali</a>
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Lanjut ke Akademik →</button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>

                <!-- STEP 4: AKADEMIK -->
                <?php if ($current_step == 'akademik'): ?>
                <form method="POST" id="ppdbForm">
                    <input type="hidden" name="step" value="akademik">
                    
                    <div class="form-section form-active">
                        <h2 style="color: #004080; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">Data Akademik</h2>
                        
                        <div class="form-group">
                            <label for="asal_sekolah">Asal Sekolah *</label>
                            <input type="text" id="asal_sekolah" name="asal_sekolah" required
                                   value="<?= $_POST['asal_sekolah'] ?? '' ?>">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label for="tahun_lulus">Tahun Lulus *</label>
                                <input type="number" id="tahun_lulus" name="tahun_lulus" required min="1980" max="<?= date('Y') ?>"
                                       value="<?= $_POST['tahun_lulus'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="rata_rata_raport">Rata-rata Nilai Raport *</label>
                                <input type="number" id="rata_rata_raport" name="rata_rata_raport" required min="0" max="100" step="0.01"
                                       value="<?= $_POST['rata_rata_raport'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="btn-group">
                            <a href="?step=orangtua" class="btn btn-secondary">← Kembali</a>
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Lanjut ke Jurusan →</button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>

                <!-- STEP 5: JURUSAN -->
                <?php if ($current_step == 'jurusan'): ?>
                <form method="POST" id="ppdbForm">
                    <input type="hidden" name="step" value="jurusan">
                    
                    <div class="form-section form-active">
                        <h2 style="color: #004080; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">Pilihan Jurusan & Beasiswa</h2>
                        
                        <div class="form-group">
                            <label for="pilihan_jurusan">Pilihan Jurusan *</label>
                            <select id="pilihan_jurusan" name="pilihan_jurusan" required>
                                <option value="">-- Pilih Jurusan --</option>
                                <option value="Teknik Komputer dan Jaringan" <?= ($_POST['pilihan_jurusan'] ?? '') == 'Teknik Komputer dan Jaringan' ? 'selected' : '' ?>>Teknik Komputer dan Jaringan</option>
                                <option value="Rekayasa Perangkat Lunak" <?= ($_POST['pilihan_jurusan'] ?? '') == 'Rekayasa Perangkat Lunak' ? 'selected' : '' ?>>Rekayasa Perangkat Lunak</option>
                                <option value="Multimedia" <?= ($_POST['pilihan_jurusan'] ?? '') == 'Multimedia' ? 'selected' : '' ?>>Multimedia</option>
                                <option value="Akuntansi" <?= ($_POST['pilihan_jurusan'] ?? '') == 'Akuntansi' ? 'selected' : '' ?>>Akuntansi</option>
                                <option value="Pemasaran" <?= ($_POST['pilihan_jurusan'] ?? '') == 'Pemasaran' ? 'selected' : '' ?>>Pemasaran</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pilihan_beasiswa">Pilihan Beasiswa (Opsional)</label>
                            <select id="pilihan_beasiswa" name="pilihan_beasiswa">
                                <option value="">-- Tidak Mengajukan --</option>
                                <option value="Siswa Berprestasi" <?= ($_POST['pilihan_beasiswa'] ?? '') == 'Siswa Berprestasi' ? 'selected' : '' ?>>Siswa Berprestasi</option>
                                <option value="Siswa Yatim dan Piatu" <?= ($_POST['pilihan_beasiswa'] ?? '') == 'Siswa Yatim dan Piatu' ? 'selected' : '' ?>>Siswa Yatim dan Piatu</option>
                                <option value="Siswa Tidak Mampu" <?= ($_POST['pilihan_beasiswa'] ?? '') == 'Siswa Tidak Mampu' ? 'selected' : '' ?>>Siswa Tidak Mampu</option>
                            </select>
                        </div>

                        <div class="btn-group">
                            <a href="?step=akademik" class="btn btn-secondary">← Kembali</a>
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Lanjut ke Dokumen →</button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>

                <!-- STEP 6: DOKUMEN -->
                <?php if ($current_step == 'dokumen'): ?>
                <form method="POST" enctype="multipart/form-data" id="ppdbForm">
                    <input type="hidden" name="step" value="dokumen">
                    
                    <div class="form-section form-active">
                        <h2 style="color: #004080; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">Upload Dokumen</h2>
                        <p style="margin-bottom: 20px; color: #666;">Upload dokumen dalam format JPG, PNG, atau PDF (maks. 2MB per file)</p>
                        
                        <div class="form-group">
                            <label for="sk_lulus">Surat Keterangan Lulus/Ijazah *</label>
                            <input type="file" id="sk_lulus" name="sk_lulus" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                            <div class="file-info">Format: JPG, PNG, PDF, DOC | Maksimal: 2MB</div>
                        </div>

                        <div class="form-group">
                            <label for="kk">Kartu Keluarga *</label>
                            <input type="file" id="kk" name="kk" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                            <div class="file-info">Format: JPG, PNG, PDF, DOC | Maksimal: 2MB</div>
                        </div>

                        <div class="form-group">
                            <label for="akta_lahir">Akta Lahir *</label>
                            <input type="file" id="akta_lahir" name="akta_lahir" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                            <div class="file-info">Format: JPG, PNG, PDF, DOC | Maksimal: 2MB</div>
                        </div>

                        <div class="form-group">
                            <label for="pas_foto">Pas Foto 3x4 *</label>
                            <input type="file" id="pas_foto" name="pas_foto" accept=".jpg,.jpeg,.png" required>
                            <div class="file-info">Format: JPG, PNG | Maksimal: 2MB</div>
                        </div>

                        <div class="form-group">
                            <label for="ktp_ortu_wali">KTP Orang Tua/Wali *</label>
                            <input type="file" id="ktp_ortu_wali" name="ktp_ortu_wali" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                            <div class="file-info">Format: JPG, PNG, PDF, DOC | Maksimal: 2MB</div>
                        </div>

                        <div class="form-group">
                            <label for="sertifikat_prestasi">Sertifikat Prestasi (Opsional)</label>
                            <input type="file" id="sertifikat_prestasi" name="sertifikat_prestasi" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <div class="file-info">Format: JPG, PNG, PDF, DOC | Maksimal: 2MB</div>
                        </div>

                        <div class="btn-group">
                            <a href="?step=jurusan" class="btn btn-secondary">← Kembali</a>
                            <button type="submit" class="btn btn-success" style="flex: 1;">Selesaikan Pendaftaran</button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>

    <script>
        // Validasi form client-side
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('ppdbForm');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Validasi NIK untuk step siswa
                    const nikInput = document.getElementById('nik');
                    if (nikInput) {
                        const nik = nikInput.value;
                        if (nik.length !== 16 || !/^\d+$/.test(nik)) {
                            e.preventDefault();
                            alert('NIK harus 16 digit angka');
                            nikInput.focus();
                            return;
                        }
                    }
                    
                    // Validasi file size untuk step dokumen
                    const fileInputs = form.querySelectorAll('input[type="file"]');
                    for (let input of fileInputs) {
                        if (input.files.length > 0) {
                            const file = input.files[0];
                            if (file.size > 2 * 1024 * 1024) { // 2MB
                                e.preventDefault();
                                alert(`File ${input.name} terlalu besar. Maksimal 2MB.`);
                                input.focus();
                                return;
                            }
                        }
                    }
                });
            }
            
            // Reset session jika parameter reset ada
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('reset')) {
                // Redirect untuk clear session
                window.location.href = window.location.pathname;
            }
        });

        // Tambahkan novalidate attribute untuk mencegah validasi browser pada form tersembunyi
        document.querySelectorAll('form').forEach(form => {
            form.setAttribute('novalidate', 'true');
        });
    </script>
</body>
</html>

<?php
// Reset session jika diminta
if (isset($_GET['reset'])) {
    session_destroy();
    session_start();
    $_SESSION['current_step'] = 'siswa';
    redirect($_SERVER['PHP_SELF']);
}
?>