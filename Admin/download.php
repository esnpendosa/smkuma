<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include '../koneksi.php';

// Tentukan jenis download
$type = $_GET['type'] ?? 'csv';

// Fungsi untuk membersihkan dan memformat data
function cleanData($data) {
    if ($data === null || $data === '') {
        return '-';
    }
    // Hilangkan karakter khusus dan trim spasi
    $data = trim($data);
    $data = str_replace(["\r", "\n", "\t"], ' ', $data);
    return $data;
}

if ($type == 'csv') {
    // Download CSV
    header("Content-type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=data_pendaftar_" . date('Y-m-d_His') . ".csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Buat BOM untuk UTF-8
    echo "\xEF\xBB\xBF";
    
    $output = fopen("php://output", "w");
    
    // Header CSV dengan format yang lebih baik
    fputcsv($output, [
        'NO', 
        'NAMA LENGKAP', 
        'NIK', 
        'JENIS KELAMIN', 
        'AGAMA',
        'TANGGAL LAHIR',
        'NO HP', 
        'EMAIL',
        'ASAL SEKOLAH', 
        'TAHUN LULUS',
        'RATA-RATA RAPORT',
        'JURUSAN PILIHAN', 
        'BEASISWA',
        'NAMA AYAH',
        'PEKERJAAN AYAH',
        'NAMA IBU',
        'PEKERJAAN IBU',
        'PROVINSI',
        'KOTA/KABUPATEN',
        'KECAMATAN',
        'ALAMAT LENGKAP',
        'TANGGAL DAFTAR'
    ], ';'); // Gunakan semicolon sebagai delimiter

    // Query data dengan JOIN
    $query = "
        SELECT 
            ps.id,
            ps.nama_lengkap,
            ps.nik,
            ps.jenis_kelamin,
            ps.agama,
            ps.tanggal_lahir,
            ps.no_hp,
            ps.email,
            ps.waktu_submit,
            a.asal_sekolah,
            a.tahun_lulus,
            a.rata_rata_raport,
            jb.pilihan_jurusan,
            jb.pilihan_beasiswa,
            ow.nama_ayah,
            ow.pekerjaan_ayah,
            ow.nama_ibu,
            ow.pekerjaan_ibu,
            al.provinsi,
            al.kota,
            al.kecamatan,
            al.alamat_lengkap
        FROM pendaftaran_siswa ps
        LEFT JOIN akademik a ON ps.id = a.siswa_id
        LEFT JOIN jurusan_beasiswa jb ON ps.id = jb.siswa_id
        LEFT JOIN orangtua_wali ow ON ps.id = ow.siswa_id
        LEFT JOIN alamat_siswa al ON ps.id = al.siswa_id
        ORDER BY ps.waktu_submit DESC
    ";
    
    $result = mysqli_query($koneksi, $query);
    $no = 1;
    
    while($data = mysqli_fetch_assoc($result)) {
        // Format data dengan benar
        $row = [
            $no++,
            cleanData($data['nama_lengkap']),
            cleanData($data['nik']),
            cleanData($data['jenis_kelamin']),
            cleanData($data['agama']),
            date('d/m/Y', strtotime($data['tanggal_lahir'])),
            cleanData($data['no_hp']),
            cleanData($data['email']),
            cleanData($data['asal_sekolah']),
            $data['tahun_lulus'] != '0000' && $data['tahun_lulus'] != '' ? $data['tahun_lulus'] : '-',
            $data['rata_rata_raport'] > 0 ? number_format($data['rata_rata_raport'], 2) : '-',
            cleanData($data['pilihan_jurusan']),
            cleanData($data['pilihan_beasiswa']),
            cleanData($data['nama_ayah']),
            cleanData($data['pekerjaan_ayah']),
            cleanData($data['nama_ibu']),
            cleanData($data['pekerjaan_ibu']),
            cleanData($data['provinsi']),
            cleanData($data['kota']),
            cleanData($data['kecamatan']),
            cleanData($data['alamat_lengkap']),
            date('d/m/Y H:i', strtotime($data['waktu_submit']))
        ];
        
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit();

} elseif ($type == 'excel') {
    // Download Excel (HTML table) - VERSI DIPERBAIKI
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=data_pendaftar_" . date('Y-m-d_His') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    $query = "
        SELECT 
            ps.id,
            ps.nama_lengkap,
            ps.nik,
            ps.jenis_kelamin,
            ps.agama,
            ps.tanggal_lahir,
            ps.no_hp,
            ps.email,
            ps.waktu_submit,
            a.asal_sekolah,
            a.tahun_lulus,
            a.rata_rata_raport,
            jb.pilihan_jurusan,
            jb.pilihan_beasiswa,
            ow.nama_ayah,
            ow.pekerjaan_ayah,
            ow.nama_ibu,
            ow.pekerjaan_ibu,
            al.provinsi,
            al.kota,
            al.kecamatan,
            al.alamat_lengkap
        FROM pendaftaran_siswa ps
        LEFT JOIN akademik a ON ps.id = a.siswa_id
        LEFT JOIN jurusan_beasiswa jb ON ps.id = jb.siswa_id
        LEFT JOIN orangtua_wali ow ON ps.id = ow.siswa_id
        LEFT JOIN alamat_siswa al ON ps.id = al.siswa_id
        ORDER BY ps.waktu_submit DESC
    ";
    
    $result = mysqli_query($koneksi, $query);
    
    // Hitung total data
    $total_data = mysqli_num_rows($result);
    mysqli_data_seek($result, 0); // Reset pointer
    
    // Hitung statistik
    $jurusan_stats = [];
    $beasiswa_stats = [];
    $jenis_kelamin_stats = ['Laki-laki' => 0, 'Perempuan' => 0];
    
    mysqli_data_seek($result, 0);
    while($data = mysqli_fetch_assoc($result)) {
        // Statistik jurusan
        $jurusan = $data['pilihan_jurusan'] ?: 'Belum Memilih';
        $jurusan_stats[$jurusan] = ($jurusan_stats[$jurusan] ?? 0) + 1;
        
        // Statistik beasiswa
        $beasiswa = $data['pilihan_beasiswa'] ?: 'Tidak Mengajukan';
        $beasiswa_stats[$beasiswa] = ($beasiswa_stats[$beasiswa] ?? 0) + 1;
        
        // Statistik jenis kelamin
        if (isset($jenis_kelamin_stats[$data['jenis_kelamin']])) {
            $jenis_kelamin_stats[$data['jenis_kelamin']]++;
        }
    }
    mysqli_data_seek($result, 0);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Data Pendaftar PPDB SMK UMAR MAS'UD</title>
        <style>
            body { 
                font-family: "Segoe UI", Arial, sans-serif; 
                margin: 20px; 
                color: #333;
            }
            .header { 
                text-align: center; 
                margin-bottom: 30px; 
                border-bottom: 3px solid #004080;
                padding-bottom: 20px;
            }
            .header h1 { 
                color: #004080; 
                margin-bottom: 5px; 
                font-size: 24px;
            }
            .header h2 { 
                color: #666; 
                font-size: 16px; 
                margin-bottom: 10px; 
                font-weight: normal;
            }
            .info { 
                margin-bottom: 25px; 
                text-align: center; 
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                border-left: 4px solid #004080;
            }
            .stats-section {
                margin-bottom: 25px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 15px;
            }
            .stats-section h3 {
                color: #004080;
                margin-bottom: 15px;
                font-size: 16px;
                border-bottom: 1px solid #eee;
                padding-bottom: 8px;
            }
            .stats-grid {
                display: table;
                width: 100%;
                border-collapse: collapse;
            }
            .stat-row {
                display: table-row;
            }
            .stat-cell {
                display: table-cell;
                padding: 6px 10px;
                border-bottom: 1px solid #f0f0f0;
            }
            .stat-cell:first-child {
                font-weight: bold;
                width: 60%;
            }
            .stat-cell:last-child {
                text-align: center;
                width: 40%;
                background: #f8f9fa;
            }
            table { 
                border-collapse: collapse; 
                width: 100%; 
                font-size: 10px;
                margin-top: 20px;
            }
            th { 
                background-color: #004080; 
                color: white; 
                padding: 12px 8px; 
                border: 1px solid #ddd; 
                text-align: center; 
                font-weight: bold;
                font-size: 11px;
            }
            td { 
                padding: 8px; 
                border: 1px solid #ddd; 
                text-align: left; 
                vertical-align: top;
            }
            tr:nth-child(even) { 
                background-color: #f9f9f9; 
            }
            tr:hover { 
                background-color: #f0f8ff; 
            }
            .number { 
                text-align: center; 
                font-weight: bold;
            }
            .center { 
                text-align: center; 
            }
            .footer { 
                margin-top: 30px; 
                text-align: center; 
                font-size: 11px; 
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 15px;
            }
            .section-title {
                background: #e7f3ff;
                padding: 10px;
                margin: 20px 0 10px 0;
                border-left: 4px solid #004080;
                font-weight: bold;
                color: #004080;
            }
            .badge {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 3px;
                font-size: 9px;
                font-weight: bold;
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
        </style>
    </head>
    <body>
        <!-- HEADER -->
        <div class="header">
            <h1>DATA PENDAFTAR PPDB SMK UMAR MAS'UD</h1>
            <h2>Tahun Ajaran <?= date('Y'); ?>/<?= date('Y') + 1; ?></h2>
        </div>
        
        <!-- INFO UTAMA -->
        <div class="info">
            <strong>Tanggal Export:</strong> <?= date('d F Y H:i:s'); ?> | 
            <strong>Total Data:</strong> <?= number_format($total_data, 0, ',', '.'); ?> Pendaftar |
            <strong>Dihasilkan oleh:</strong> Sistem PPDB SMK UMAR MAS'UD
        </div>

        <!-- STATISTIK -->
        <div class="stats-section">
            <h3>📊 STATISTIK PENDAFTAR</h3>
            <div class="stats-grid">
                <!-- Statistik Jurusan -->
                <div class="stat-row">
                    <div class="stat-cell"><strong>Jurusan Pilihan:</strong></div>
                    <div class="stat-cell"></div>
                </div>
                <?php foreach($jurusan_stats as $jurusan => $count): ?>
                <div class="stat-row">
                    <div class="stat-cell">• <?= htmlspecialchars($jurusan) ?></div>
                    <div class="stat-cell"><?= $count ?> orang</div>
                </div>
                <?php endforeach; ?>
                
                <!-- Statistik Beasiswa -->
                <div class="stat-row">
                    <div class="stat-cell" style="padding-top: 15px;"><strong>Jenis Beasiswa:</strong></div>
                    <div class="stat-cell" style="padding-top: 15px;"></div>
                </div>
                <?php foreach($beasiswa_stats as $beasiswa => $count): ?>
                <div class="stat-row">
                    <div class="stat-cell">• <?= htmlspecialchars($beasiswa) ?></div>
                    <div class="stat-cell"><?= $count ?> orang</div>
                </div>
                <?php endforeach; ?>
                
                <!-- Statistik Jenis Kelamin -->
                <div class="stat-row">
                    <div class="stat-cell" style="padding-top: 15px;"><strong>Jenis Kelamin:</strong></div>
                    <div class="stat-cell" style="padding-top: 15px;"></div>
                </div>
                <?php foreach($jenis_kelamin_stats as $jk => $count): ?>
                <div class="stat-row">
                    <div class="stat-cell">• <?= htmlspecialchars($jk) ?></div>
                    <div class="stat-cell"><?= $count ?> orang</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- DATA DETAIL -->
        <div class="section-title">📋 DATA DETAIL PENDAFTAR</div>
        
        <table>
            <thead>
                <tr>
                    <th width="30">NO</th>
                    <th width="120">NAMA LENGKAP</th>
                    <th width="100">NIK</th>
                    <th width="50">JK</th>
                    <th width="60">AGAMA</th>
                    <th width="70">TGL LAHIR</th>
                    <th width="90">NO HP</th>
                    <th width="120">EMAIL</th>
                    <th width="100">ASAL SEKOLAH</th>
                    <th width="50">LULUS</th>
                    <th width="50">NILAI</th>
                    <th width="80">JURUSAN</th>
                    <th width="80">BEASISWA</th>
                    <th width="90">AYAH</th>
                    <th width="80">PEKERJAAN</th>
                    <th width="90">IBU</th>
                    <th width="80">PEKERJAAN</th>
                    <th width="80">PROVINSI</th>
                    <th width="80">KOTA</th>
                    <th width="80">KECAMATAN</th>
                    <th width="150">ALAMAT</th>
                    <th width="90">TGL DAFTAR</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while($data = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    
                    // Nomor
                    echo "<td class='number'>" . $no++ . "</td>";
                    
                    // Data Pribadi
                    echo "<td><strong>" . htmlspecialchars(cleanData($data['nama_lengkap'])) . "</strong></td>";
                    echo "<td>" . htmlspecialchars(cleanData($data['nik'])) . "</td>";
                    echo "<td class='center'>" . htmlspecialchars(cleanData($data['jenis_kelamin'])) . "</td>";
                    echo "<td class='center'>" . htmlspecialchars(cleanData($data['agama'])) . "</td>";
                    echo "<td class='center'>" . date('d/m/Y', strtotime($data['tanggal_lahir'])) . "</td>";
                    echo "<td>" . htmlspecialchars(cleanData($data['no_hp'])) . "</td>";
                    echo "<td>" . htmlspecialchars(cleanData($data['email'])) . "</td>";
                    
                    // Data Akademik
                    echo "<td>" . htmlspecialchars(cleanData($data['asal_sekolah'])) . "</td>";
                    echo "<td class='center'>" . ($data['tahun_lulus'] != '0000' && $data['tahun_lulus'] != '' ? $data['tahun_lulus'] : '-') . "</td>";
                    echo "<td class='center'><strong>" . ($data['rata_rata_raport'] > 0 ? number_format($data['rata_rata_raport'], 2) : '-') . "</strong></td>";
                    
                    // Jurusan & Beasiswa dengan badge
                    echo "<td class='center'>";
                    if (!empty($data['pilihan_jurusan'])) {
                        echo "<span class='badge badge-jurusan'>" . htmlspecialchars(cleanData($data['pilihan_jurusan'])) . "</span>";
                    } else {
                        echo "-";
                    }
                    echo "</td>";
                    
                    echo "<td class='center'>";
                    if (!empty($data['pilihan_beasiswa'])) {
                        echo "<span class='badge badge-beasiswa'>" . htmlspecialchars(cleanData($data['pilihan_beasiswa'])) . "</span>";
                    } else {
                        echo "-";
                    }
                    echo "</td>";
                    
                    // Data Orang Tua
                    echo "<td>" . htmlspecialchars(cleanData($data['nama_ayah'])) . "</td>";
                    echo "<td>" . htmlspecialchars(cleanData($data['pekerjaan_ayah'])) . "</td>";
                    echo "<td>" . htmlspecialchars(cleanData($data['nama_ibu'])) . "</td>";
                    echo "<td>" . htmlspecialchars(cleanData($data['pekerjaan_ibu'])) . "</td>";
                    
                    // Data Alamat
                    echo "<td>" . htmlspecialchars(cleanData($data['provinsi'])) . "</td>";
                    echo "<td>" . htmlspecialchars(cleanData($data['kota'])) . "</td>";
                    echo "<td>" . htmlspecialchars(cleanData($data['kecamatan'])) . "</td>";
                    echo "<td>" . htmlspecialchars(cleanData($data['alamat_lengkap'])) . "</td>";
                    
                    // Tanggal Daftar
                    echo "<td class='center'>" . date('d/m/Y H:i', strtotime($data['waktu_submit'])) . "</td>";
                    
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        
        <!-- FOOTER -->
        <div class="footer">
            <p><strong>SMK UMAR MAS'UD</strong> - Jl. Pendidikan No. 123, Kota Gresik, Jawa Timur</p>
            <p>Telp: (031) 123456 | Email: info@smkumarmasud.sch.id | Website: www.smkumarmasud.sch.id</p>
            <p>© <?= date('Y'); ?> Sistem PPDB SMK UMAR MAS'UD - All rights reserved</p>
        </div>
    </body>
    </html>
    <?php
    exit();

} elseif ($type == 'pdf') {
    // Redirect ke halaman untuk generate PDF
    $_SESSION['info'] = "Fitur download PDF sedang dalam pengembangan.";
    header("Location: download_page.php");
    exit();
}

// Jika tidak ada type yang valid, redirect ke halaman download
header("Location: download_page.php");
exit();
?>