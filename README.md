
# Sistem PPDB SMK Umar Mas'ud

Sistem Pendaftaran Peserta Didik Baru (PPDB) Online untuk SMK Umar Mas'ud.

## 🚀 Fitur

### Untuk Calon Siswa
- ✅ Form pendaftaran online multi-step
- ✅ Upload dokumen digital
- ✅ Pilihan jurusan dan beasiswa
- ✅ Validasi data real-time
- ✅ Cetak bukti pendaftaran

### Untuk Admin
- ✅ Dashboard admin
- ✅ Management data pendaftar
- ✅ Download data excel
- ✅ Grafik statistik
- ✅ Sistem login admin

## 📋 Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web server (Apache/Nginx)
- Browser modern

## 🛠️ Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/esnpendosa/smkuma.git
   ```

2. **Import database**
   - Buat database `ppdb_smk_um`
   - Import file `ppdb_smk_um.sql`

3. **Konfigurasi database**
   - Edit file `koneksi.php`
   - Sesuaikan host, username, password database

4. **Setup folder uploads**
   - Buat folder `uploads/` di root
   - Berikan permission 755 (jika di Linux)

5. **Akses aplikasi**
   - Frontend: `http://localhost/ppdbsmkum/`
   - Admin: `http://localhost/ppdbsmkum/Admin/login.php`

## 📁 Struktur Project

```
ppdbsmkum/
├── Admin/                 # Panel admin
│   ├── dashboard.php
│   ├── login.php
│   ├── pendaftar.php
│   └── ...
├── Database/              # Koneksi dan proses data
│   ├── db.php
│   ├── Phpconnect.php
│   └── uploads/          # Folder upload dokumen
├── Form/                  # Form pendaftaran
│   └── ppdb.php
├── img/                   # Assets gambar
├── index.php             # Halaman beranda
├── koneksi.php           # Konfigurasi database
├── ppdb_smk_um.sql       # Database structure
└── style.css             # Stylesheet
```

## 👥 Default Login Admin

- **Username:** admin
- **Password:** password

*Ganti password default setelah login pertama!*

## 🎯 Cara Penggunaan

### Untuk Calon Siswa:
1. Buka halaman beranda
2. Klik "Daftar Sekarang"
3. Isi form step-by-step
4. Upload dokumen required
5. Submit dan cetak bukti

### Untuk Admin:
1. Login di `/Admin/login.php`
2. Akses dashboard untuk melihat statistik
3. Kelola data pendaftar di "Data Pendaftar"
4. Download data di "Download Excel"

## 🔧 Konfigurasi

### Database Configuration
Edit `koneksi.php`:
```php
<?php
$host = "localhost";
$user = "root"; 
$pass = "";
$db   = "ppdb_smk_um";
?>
```

### File Upload Configuration
- Max file size: 2MB
- Allowed formats: JPG, PNG, PDF, DOC, DOCX
- Upload folder: `Database/uploads/`

## 📊 Teknologi Used

- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap
- **Backend:** PHP Native, MySQL
- **Security:** SQL injection prevention, XSS protection
- **Features:** File upload, Data validation, Session management

## 🤝 Kontribusi

1. Fork project
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

Project ini under license - see [LICENSE](LICENSE) file untuk detail.

## 👨‍💻 Developer

**Esn Pendosa**
- GitHub: [@esnpendosa](https://github.com/esnpendosa)

## 📞 Support

Jika ada pertanyaan atau masalah, silakan buat issue di GitHub repository.

---

**SMK Umar Mas'ud** - Sistem PPDB Online Modern dan Efisien 🎓
```

## Cara membuat file README.md:

1. **Buat file baru** di root folder project dengan nama `README.md`
2. **Copy paste** kode di atas ke dalam file
3. **Save** file

## Atau via command line:

```bash
# Buat file README.md
echo "# Sistem PPDB SMK Umar Mas'ud" > README.md

# Tambahkan konten (gunakan text editor favorit Anda)
# atau copy paste manual melalui text editor
