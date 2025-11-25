-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 25 Nov 2025 pada 00.40
-- Versi server: 8.0.30
-- Versi PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `ppdb_smk_um`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`, `created_at`) VALUES
(1, 'admin', '$2y$12$yLJHLyrNOnv2ylubGny7X.9YkXvHLpxYbg5/0ZpIvr4L49TPLHCYa', 'Administrator PPDB', '2025-11-24 11:58:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `akademik`
--

CREATE TABLE `akademik` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `asal_sekolah` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `tahun_lulus` year NOT NULL,
  `rata_rata_raport` decimal(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `akademik`
--

INSERT INTO `akademik` (`id`, `siswa_id`, `asal_sekolah`, `tahun_lulus`, `rata_rata_raport`) VALUES
(1, 3, 'smk', '2050', 38.00),
(2, 4, 'wretyuiop', '0000', 0.00),
(3, 5, 'SMPN 1 SANGKAPURA', '0000', 0.00),
(11, 16, 'giyhjb', '2001', 90.00),
(12, 17, 'umg', '2020', 3.80),
(13, 18, 'umg', '2023', 3.80);

-- --------------------------------------------------------

--
-- Struktur dari tabel `alamat_siswa`
--

CREATE TABLE `alamat_siswa` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `provinsi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `kota` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `kecamatan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat_lengkap` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `alamat_siswa`
--

INSERT INTO `alamat_siswa` (`id`, `siswa_id`, `provinsi`, `kota`, `kecamatan`, `alamat_lengkap`) VALUES
(1, 3, 'gresik', 'gresik', 'gresik', 'gresik'),
(2, 4, 'fgfhgjklk', 'fdgfhjokp', 'retyuiopo', 'fghjkl'),
(3, 5, 'ty', 'ty', 'qer', 'jhjgfd'),
(11, 16, 'yu', 'sth', 'dgfh', 'fj'),
(12, 17, 'East Java', 'GRESIK', 'Bungah', 'Jl. Panglima sudirman sungonlegowo bungah gresik'),
(13, 18, 'Jawa Timur', 'Kabupaten Gresik', 'Bungah', 'Jl. H. Agus Salim');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen`
--

CREATE TABLE `dokumen` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `sk_lulus` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `kk` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `akta_lahir` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pas_foto` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ktp_ortu_wali` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `sertifikat_prestasi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dokumen`
--

INSERT INTO `dokumen` (`id`, `siswa_id`, `sk_lulus`, `kk`, `akta_lahir`, `pas_foto`, `ktp_ortu_wali`, `sertifikat_prestasi`) VALUES
(1, 3, 'uploads/1758552620_Permohonan Bantuan Dana Hibah Pilkada Bupati.pdf', 'uploads/1758552620_Permohonan Bantuan Dana Hibah Pilkada Bupati.pdf', 'uploads/1758552620_Permohonan Bantuan Dana Hibah Pilkada.pdf', 'uploads/1758552620_wallpaper devi.png', 'uploads/1758552620_wallpaper devi.png', 'uploads/1758552620_wallpaper devi.png'),
(2, 3, 'uploads/1758552926_wallpaper devi.png', 'uploads/1758552926_wallpaper devi.png', 'uploads/1758552926_Permohonan Bantuan Dana Hibah Pilkada.pdf', 'uploads/1758552926_wallpaper devi.png', 'uploads/1758552926_wallpaper devi.png', 'uploads/1758552926_wallpaper devi.png'),
(3, 4, 'uploads/1758553178_wallpaper devi.png', 'uploads/1758553178_wallpaper devi.png', 'uploads/1758553178_wallpaper devi.png', 'uploads/1758553178_wallpaper devi.png', 'uploads/1758553178_wallpaper devi.png', 'uploads/1758553178_wallpaper devi.png'),
(4, 5, 'uploads/1758683109_3-(94-98)-Azmi+Illahi-Revisi-2.pdf', 'uploads/1758683109_7902-17728-1-PB (1).pdf', 'uploads/1758683109_3-(94-98)-Azmi+Illahi-Revisi-2.pdf', 'uploads/1758683109_3-(94-98)-Azmi+Illahi-Revisi-2.pdf', 'uploads/1758683109_2778-7148-2-PB (1).pdf', 'uploads/1758683109_hidayat,+vol+9+no+1+urut+2+Komputika+TMD+7+-+14.pdf'),
(12, 16, 'uploads/1764024465_6924e0915c0f6.jpg', 'uploads/1764024465_6924e0915c3be.jpg', 'uploads/1764024465_6924e0915c6fd.pdf', 'uploads/1764024465_6924e0915cb4f.jpg', 'uploads/1764024465_6924e0915d588.jpg', 'uploads/1764024465_6924e0915d8f6.png'),
(13, 17, 'uploads/1764028862_6924f1bec1edc.jpg', 'uploads/1764028862_6924f1bec216a.png', 'uploads/1764028862_6924f1bec23a1.jpg', 'uploads/1764028862_6924f1bec256e.png', 'uploads/1764028862_6924f1bec276b.jpg', ''),
(14, 17, 'uploads/1764029757_6924f53d9d6ba.jpg', 'uploads/1764029757_6924f53d9d874.png', 'uploads/1764029757_6924f53d9da6a.jpg', 'uploads/1764029757_6924f53d9dc4a.png', 'uploads/1764029757_6924f53d9de2a.jpg', ''),
(15, 18, 'uploads/1764030780_6924f93c03d01.png', 'uploads/1764030780_6924f93c04076.jpg', 'uploads/1764030780_6924f93c043d1.docx', 'uploads/1764030780_6924f93c0474d.jpg', 'uploads/1764030780_6924f93c04c54.jpg', ''),
(16, 18, 'uploads/1764030794_6924f94a58365.png', 'uploads/1764030794_6924f94a5861f.jpg', 'uploads/1764030794_6924f94a58882.docx', 'uploads/1764030794_6924f94a58b6e.jpg', 'uploads/1764030794_6924f94a58e0d.jpg', ''),
(17, 18, 'uploads/1764030913_6924f9c1694be.png', 'uploads/1764030913_6924f9c1698a3.jpg', 'uploads/1764030913_6924f9c169ca0.docx', 'uploads/1764030913_6924f9c16a062.jpg', 'uploads/1764030913_6924f9c16a3ed.jpg', ''),
(18, 18, 'uploads/1764030923_6924f9cbda9ce.png', 'uploads/1764030923_6924f9cbdad9b.jpg', 'uploads/1764030923_6924f9cbdb0ee.docx', 'uploads/1764030923_6924f9cbdb456.jpg', 'uploads/1764030923_6924f9cbdbb4b.jpg', ''),
(19, 18, 'uploads/1764030979_6924fa03581c1.png', 'uploads/1764030979_6924fa03584f9.jpg', 'uploads/1764030979_6924fa0358ae7.docx', 'uploads/1764030979_6924fa03591c3.jpg', 'uploads/1764030979_6924fa03595ae.jpg', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurusan_beasiswa`
--

CREATE TABLE `jurusan_beasiswa` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `pilihan_jurusan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `pilihan_beasiswa` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurusan_beasiswa`
--

INSERT INTO `jurusan_beasiswa` (`id`, `siswa_id`, `pilihan_jurusan`, `pilihan_beasiswa`) VALUES
(1, 3, 'Teknik Komputer dan Jaringan', 'Siswa Yatim dan Piatu'),
(2, 4, 'Teknik Komputer dan Jaringan', 'Siswa Berprestasi'),
(3, 5, 'Teknik Komputer dan Jaringan', 'Siswa Berprestasi'),
(11, 16, 'Multimedia', 'Siswa Yatim dan Piatu'),
(12, 17, 'Teknik Komputer dan Jaringan', 'Siswa Berprestasi'),
(13, 18, 'Teknik Komputer dan Jaringan', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orangtua_wali`
--

CREATE TABLE `orangtua_wali` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `nama_ayah` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `pekerjaan_ayah` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nohp_ayah` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_ibu` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `pekerjaan_ibu` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nohp_ibu` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_wali` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pekerjaan_wali` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nohp_wali` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orangtua_wali`
--

INSERT INTO `orangtua_wali` (`id`, `siswa_id`, `nama_ayah`, `pekerjaan_ayah`, `nohp_ayah`, `nama_ibu`, `pekerjaan_ibu`, `nohp_ibu`, `nama_wali`, `pekerjaan_wali`, `nohp_wali`) VALUES
(1, 3, 'suwito', 'pns', '83049382383498398', 'umi', 'jualan', '347349833748733', 'puji', NULL, '348973788578347'),
(2, 4, 'sdfgghjkl;', 'ertyuiop', 'werdfghjkl;', 'zdxcvbn,m.', 'dfghjkl;', 'yfguhijokpl;', 'yuhjnm ', NULL, 'ertyui'),
(3, 5, 'Bahtiar Efendi', 'Wiraswasta', '081312108177', 'Rohani', 'Wiraswasta', '081312108177', 'sh', NULL, 'h'),
(11, 16, 'yfjh', 'fjhv', 'fjhv', 'ifhv', 'ugkjb', 'fhkv', '', NULL, ''),
(12, 17, 'Ayah', 'p', 'p', 'Ibu', 'p', 'p', '', NULL, ''),
(13, 18, 'Ayah', 'p', 'p', 'p', 'p', 'p', '', NULL, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran_siswa`
--

CREATE TABLE `pendaftaran_siswa` (
  `id` int NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nik` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') COLLATE utf8mb4_general_ci NOT NULL,
  `agama` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `waktu_submit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftaran_siswa`
--

INSERT INTO `pendaftaran_siswa` (`id`, `nama_lengkap`, `nik`, `jenis_kelamin`, `agama`, `tanggal_lahir`, `no_hp`, `email`, `waktu_submit`) VALUES
(1, 'erna', '234933483478734', 'Laki-laki', 'Islam', '2025-09-22', '803989389489893', 'erna@gmail.com', '2025-09-22 12:55:52'),
(3, 'erna', '139982398819', 'Laki-laki', 'islam', '2025-09-22', '93483498948934923438', 'erna@gmail.com', '2025-09-22 14:30:10'),
(4, 'dv', '456789', 'Perempuan', 'vhbsn', '2025-09-22', '3456789', 'devivhbn@gmail.com', '2025-09-22 14:58:14'),
(5, 'sxdfg', 'we', 'Laki-laki', 'Islam', '2025-09-24', '456', 'kantin123@gmail.com', '2025-09-24 03:03:36'),
(16, 'Muhammad As&#039;ad Muhibbin Akbar', '7687687986788587', 'Laki-laki', 'i', '2001-12-12', '08798', 'aadstar72@gmail.com', '2025-11-24 22:46:39'),
(17, 'MUHAMMAD AS&#039;AD MUHIBBIN AKBAR', '3524270103890001', 'Laki-laki', 'Islam', '2000-12-12', '081335795674', 'aadscreet@gmail.com', '2025-11-24 23:59:25'),
(18, 'MUHAMMAD AS&#039;AD MUHIBBIN AKBAR', '3524270103890009', 'Laki-laki', 'Islam', '2203-12-12', '085730302827', 'aadscreet@gmail.com', '2025-11-25 00:31:43');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `akademik`
--
ALTER TABLE `akademik`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `alamat_siswa`
--
ALTER TABLE `alamat_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `jurusan_beasiswa`
--
ALTER TABLE `jurusan_beasiswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `orangtua_wali`
--
ALTER TABLE `orangtua_wali`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `pendaftaran_siswa`
--
ALTER TABLE `pendaftaran_siswa`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `akademik`
--
ALTER TABLE `akademik`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `alamat_siswa`
--
ALTER TABLE `alamat_siswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `jurusan_beasiswa`
--
ALTER TABLE `jurusan_beasiswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `orangtua_wali`
--
ALTER TABLE `orangtua_wali`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran_siswa`
--
ALTER TABLE `pendaftaran_siswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `akademik`
--
ALTER TABLE `akademik`
  ADD CONSTRAINT `akademik_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `pendaftaran_siswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `alamat_siswa`
--
ALTER TABLE `alamat_siswa`
  ADD CONSTRAINT `alamat_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `pendaftaran_siswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  ADD CONSTRAINT `dokumen_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `pendaftaran_siswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jurusan_beasiswa`
--
ALTER TABLE `jurusan_beasiswa`
  ADD CONSTRAINT `jurusan_beasiswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `pendaftaran_siswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `orangtua_wali`
--
ALTER TABLE `orangtua_wali`
  ADD CONSTRAINT `orangtua_wali_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `pendaftaran_siswa` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
