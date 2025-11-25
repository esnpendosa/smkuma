<?php
include "Phpconnect.php";

/**
 * ========================
 * SISWA (Biodata Utama)
 * ========================
 */
function insertSiswa($data) {
    global $conn;
    $sql = "INSERT INTO pendaftaran_siswa 
            (nama_lengkap, nik, jenis_kelamin, agama, tanggal_lahir, no_hp, email) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss",
        $data['nama_lengkap'],
        $data['nik'],
        $data['jenis_kelamin'],
        $data['agama'],
        $data['tanggal_lahir'],
        $data['no_hp'],
        $data['email']
    );
    if ($stmt->execute()) {
        return $stmt->insert_id;
    }
    return false;
}

/**
 * ========================
 * ALAMAT SISWA
 * ========================
 */
function insertAlamat($siswa_id, $data) {
    global $conn;
    $sql = "INSERT INTO alamat_siswa (siswa_id, provinsi, kota, kecamatan, alamat_lengkap)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss",
        $siswa_id,
        $data['provinsi'],
        $data['kota'],
        $data['kecamatan'],
        $data['alamat_lengkap']
    );
    return $stmt->execute();
}

/**
 * ========================
 * ORANG TUA / WALI
 * ========================
 */
function insertOrangtuaWali($siswa_id, $data) {
    global $conn;
    $sql = "INSERT INTO orangtua_wali 
            (siswa_id, nama_ayah, pekerjaan_ayah, nohp_ayah, nama_ibu, pekerjaan_ibu, nohp_ibu, nama_wali, nohp_wali)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssss",
        $siswa_id,
        $data['nama_ayah'],
        $data['pekerjaan_ayah'],
        $data['nohp_ayah'],
        $data['nama_ibu'],
        $data['pekerjaan_ibu'],
        $data['nohp_ibu'],
        $data['nama_wali'],
        $data['nohp_wali']
    );
    return $stmt->execute();
}

/**
 * ========================
 * AKADEMIK
 * ========================
 */
function insertAkademik($siswa_id, $data) {
    global $conn;
    $sql = "INSERT INTO akademik (siswa_id, asal_sekolah, tahun_lulus, rata_rata_raport)
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isis",
        $siswa_id,
        $data['asal_sekolah'],
        $data['tahun_lulus'],
        $data['rata_rata_raport']
    );
    return $stmt->execute();
}

/**
 * ========================
 * JURUSAN & BEASISWA
 * ========================
 */
function insertJurusanBeasiswa($siswa_id, $data) {
    global $conn;
    $sql = "INSERT INTO jurusan_beasiswa (siswa_id, pilihan_jurusan, pilihan_beasiswa)
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss",
        $siswa_id,
        $data['pilihan_jurusan'],
        $data['pilihan_beasiswa']
    );
    return $stmt->execute();
}

/**
 * ========================
 * DOKUMEN (Upload)
 * ========================
 */
function insertDokumen($siswa_id, $data) {
    global $conn;
    $sql = "INSERT INTO dokumen (siswa_id, sk_lulus, kk, akta_lahir, pas_foto, ktp_ortu_wali, sertifikat_prestasi)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss",
        $siswa_id,
        $data['sk_lulus'],
        $data['kk'],
        $data['akta_lahir'],
        $data['pas_foto'],
        $data['ktp_ortu_wali'],
        $data['sertifikat_prestasi']
    );
    return $stmt->execute();
}
?>
