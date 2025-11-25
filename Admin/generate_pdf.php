<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: loginadmin.php");
    exit();
}
include '../koneksi.php';

require_once('../tcpdf/tcpdf.php');

// Buat instance PDF
$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('PPDB SMK UMAR MAS\'UD');
$pdf->SetAuthor('Admin PPDB');
$pdf->SetTitle('Data Pendaftar PPDB');
$pdf->SetSubject('Data Pendaftar');

// Add a page
$pdf->AddPage();

// Set content
$html = '
<h1 style="text-align:center; color:#004080;">DATA PENDAFTAR PPDB</h1>
<h3 style="text-align:center;">SMK UMAR MAS\'UD</h3>
<p style="text-align:center;">Tanggal Generate: ' . date('d/m/Y H:i') . '</p>
<br>';

// Query data
$query = "
    SELECT 
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
        jb.pilihan_beasiswa
    FROM pendaftaran_siswa ps
    LEFT JOIN akademik a ON ps.id = a.siswa_id
    LEFT JOIN jurusan_beasiswa jb ON ps.id = jb.siswa_id
    ORDER BY ps.waktu_submit DESC
";

$result = mysqli_query($koneksi, $query);

$html .= '
<table border="1" cellpadding="5" style="border-collapse: collapse;">
    <thead>
        <tr style="background-color:#004080; color:white;">
            <th>No</th>
            <th>Nama</th>
            <th>NIK</th>
            <th>Jenis Kelamin</th>
            <th>Asal Sekolah</th>
            <th>Jurusan</th>
            <th>Nilai</th>
            <th>Tanggal Daftar</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while($data = mysqli_fetch_assoc($result)) {
    $html .= '
    <tr>
        <td>' . $no++ . '</td>
        <td>' . htmlspecialchars($data['nama_lengkap']) . '</td>
        <td>' . htmlspecialchars($data['nik']) . '</td>
        <td>' . htmlspecialchars($data['jenis_kelamin']) . '</td>
        <td>' . htmlspecialchars($data['asal_sekolah'] ?? '') . '</td>
        <td>' . htmlspecialchars($data['pilihan_jurusan'] ?? '') . '</td>
        <td>' . ($data['rata_rata_raport'] ?? '0') . '</td>
        <td>' . date('d/m/Y H:i', strtotime($data['waktu_submit'])) . '</td>
    </tr>';
}

$html .= '
    </tbody>
</table>';

// Output PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('data_pendaftar_' . date('Y-m-d') . '.pdf', 'D');
?>