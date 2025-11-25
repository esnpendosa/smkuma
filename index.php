<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>PPDB - SMK UMMA</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --primary:#004080;
      --accent:#0074d9;
      --radius:14px;
      --container:1100px;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    }
    *{box-sizing:border-box}
    body{margin:0;background:#ffffff;color:#0b1730;line-height:1.45}
    .container{max-width:var(--container);margin:0 auto;padding:0 20px}

    /* Header */
    header{position:fixed;left:0;right:0;top:0;z-index:50;background:#ffffff;border-bottom:1px solid rgba(0,0,0,0.06)}
    .nav{display:flex;align-items:center;justify-content:space-between;padding:14px 0}
    .brand{display:flex;gap:12px;align-items:center}
    .logo {
    width: 44px;
    height: 44px;
    object-fit: contain;
    border-radius: 6px; /* kalau mau kotak bisa hapus ini */
    }
    .brand h1{font-size:16px;margin:0;color:var(--primary)}
    nav ul{display:flex;gap:18px;list-style:none;margin:0;padding:0}
    nav a{color:var(--primary);text-decoration:none;font-weight:600}
    .btn-primary{background:var(--primary);color:#fff;padding:10px 16px;border-radius:10px;text-decoration:none;font-weight:700}

    /* Mobile */
    .hamburger{display:none;border:0;background:none}
    @media(max-width:900px){nav ul{display:none}.hamburger{display:block}}

    /* Hero */
    .hero{padding:120px 0 60px;background:#ffffff;color:var(--primary)}
    .hero-inner{display:grid;grid-template-columns:1fr 420px;gap:30px;align-items:center}
    .hero h2{font-size:36px;margin:0 0 10px}
    .hero p{margin:0 0 20px;opacity:0.95}
    .hero .cta{display:flex;gap:12px}
    .card{background:#f9f9f9;padding:18px;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.06)}

    .hero-image {
    width: 100%;
    max-width: 400px;
    height: auto;
    object-fit: contain;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background: none !important;
    display: block;
    margin: 0 auto;
    }

.card-sambutan {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  background: #ffffff;
  padding: 16px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  flex-wrap: wrap;
  max-width: 800px;
  margin: 0 auto;
}

.card-sambutan .isi-sambutan {
  flex: 1;
  font-size: 14px;
  line-height: 1.6;
  color: #333;
}

    /* Semua card background putih kebiruan */
.card,
.card-sambutan,
.alumni-card,
.timeline .item div,
#alurpendaftaran > div > div {
  background: #f6f6f6ff !important; /* putih kebiruan */
  color: #000; /* font hitam */
}

/* Timeline nomor tetap biru */
.timeline .item .nomor {
  background: #004080;
  color: #fff;
  font-weight:700;
  padding:8px;
  border-radius:50%;
  min-width:32px;
  text-align:center;
}

/* Muted text tetap lebih soft */
.muted {
  color: rgba(11,23,48,0.6);
}

/* Background section tetap putih */
section {
  background: #fff; 
  color: #000;
}

/* Hapus background section luar */
#profilsekolah, 
#alurpendaftaran, 
#alumni, 
#infoppdb {
  background: transparent !important; /* atau #fff untuk tetap putih */
  padding: 48px 0;
}

/* Timeline nomor tetap biru */
.timeline .item .nomor {
  background: #004080;
  color: #fff;
  font-weight:700;
  padding:8px;
  border-radius:50%;
  min-width:32px;
  text-align:center;
}

/* Muted text tetap soft */
.muted {
  color: rgba(11,23,48,0.6);
}

/* Semua elemen animasi */
.animate {
  opacity: 0;
  transform: translateY(50px) scale(0.95);
  transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

.animate.visible {
  opacity: 1;
  transform: translateY(0) scale(1);
}

/* Delay berbeda-beda untuk child */
.delay-1 { transition-delay: 0.1s; }
.delay-2 { transition-delay: 0.2s; }
.delay-3 { transition-delay: 0.3s; }
.delay-4 { transition-delay: 0.4s; }
.delay-5 { transition-delay: 0.5s; }
.delay-6 { transition-delay: 0.6s; }

/* Splash screen */
#opening {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: #ffffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  opacity: 1;
  transition: opacity 1s ease;
}

#opening img {
  width: 150px;
  height: 150px;
  transform: scale(0);
  animation: logo-zoom 1s forwards;
}

@keyframes logo-zoom {
  0% { transform: scale(0); opacity: 0; }
  50% { transform: scale(1.2); opacity: 1; }
  100% { transform: scale(1); opacity: 1; }
}

.slider {
    overflow: hidden;
    position: relative;
    width: 100%;
  }

  .slide-track {
    display: flex;
    width: calc(280px * 8); /* jumlah card total (4 asli + 4 duplikat) */
    animation: scroll 15s linear infinite;
  }

  .alumni-card {
    min-width: 280px;
    max-width: 280px;
    margin: 0 10px;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    text-align: center;
  }

  .alumni-card img {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    margin-bottom: 10px;
  }

  @keyframes scroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
  }

    @media(max-width:900px){
      .hero-inner{grid-template-columns:1fr}
      .hero h2{font-size:28px}
    }

    /* Sections */
    section{padding:48px 0}
    .grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
    .card h3{margin:0 0 8px}
    .majors .card{min-height:220px}
    @media(max-width:900px){.grid-3{grid-template-columns:1fr}}

    /* Timeline */
    .timeline{display:flex;flex-direction:column;gap:12px}
    .timeline .item{display:flex;gap:14px;align-items:flex-start}
    .date{min-width:88px;background:#fff;color:var(--primary);padding:8px;border-radius:8px;font-weight:700;text-align:center;border:1px solid #ddd}

    /* Footer */
    footer{background:#061726;color:#cfe8ff;padding:28px 0;border-top:1px solid rgba(255,255,255,0.02)}

    /* Smooth Scroll */
    :target{scroll-margin-top:92px}
    .muted{color:rgba(11,23,48,0.6)}
    .badge{display:inline-block;padding:6px 10px;border-radius:999px;background:#e6f1ff;color:var(--primary);font-weight:700}
  </style>
</head>
<body>
<header>
  <div class="container nav">
    <div class="brand">
      <img src="img/logosmkum.png" alt="Logo SMK Umar Mas'ud" class="logo">

      <div>
        <h1>SMK UMAR MAS'UD</h1>
      </div>
    </div>
    <nav>
      <ul>
        <li><a href="#home">Home</a></li>
        <li><a href="#profilsekolah">Profil Sekolah</a></li>
        <li><a href="#jurusan">Jurusan</a></li>
        <li><a href="#infoppdb">Info PPDB</a></li>
        <li><a href="#alurpendaftaran">Alur Pendaftaran</a></li>
      </ul>
    </nav>
  </div>
</header>
<div id="opening">
  <img src="img/logosmkum.png" alt="Logo SMK UMMA">
</div>

  <main>
    <section id="home" class="hero">
  <div class="container hero-inner">
    <div>
      <div class="muted" style="font-size:16px">Pendaftaran Tahun Ajaran 2025/2026</div>
      <h2>Elevate Your Future — Mari Bergabung di SMK Umar Mas'ud</h2>
      <p>Bangun kariermu di dunia teknologi, mulai dari sini. Satu klik untuk masa depan tanpa batas.</p>
      <div class="cta">
<a class="btn-primary" href="Form/ppdb.php"
     style="padding:12px 24px; background:#004080; color:#fff; border-radius:8px; text-decoration:none; font-weight:600; box-shadow:0 4px 8px rgba(0,0,0,0.15);">
    Daftar Sekarang
  </a>
  <a class="btn-primary" href="https://wa.me/6282332821626" target="_blank">
    Informasi
  </a>
</div>
    </div>
    <aside>
      <img src="img/dausmay1.png" alt="Gambar PPDB" class="hero-image">
    </aside>
  </div>
</section>
    <!-- Section lain tetap -->
  </main>

  <section id="sambutan" style="padding:48px 0;">
  <div class="container" style="max-width:1000px; margin:0 auto;">
    <h2 style="text-align:center;margin-bottom:24px;font-size:28px;color:#000;">Sambutan Kepala Sekolah</h2>
    <div class="card-sambutan" style="display:flex; align-items:flex-start; gap:24px; background:#f8fbff; padding:24px; border-radius:12px;">
      <img src="img/kepsek.png" alt="Kepala Sekolah" class="foto-kepsek" style="width:200px; border-radius:12px;">
      <div class="isi-sambutan" style="color:#000; text-align:justify; flex:1;">
        <p>
          Assalamu’alaikum Warahmatullahi Wabarakatuh,<br><br>
          Puji syukur kita panjatkan ke hadirat Allah SWT. Sejak diresmikan pada 18 Mei 2013, SMK Umar Mas’ud hadir sebagai sekolah menengah kejuruan pertama di Pulau Bawean dengan komitmen mencetak generasi yang unggul, mandiri, dan siap menghadapi tantangan zaman.
          <br><br>
          Melalui jurusan unggulan seperti Teknik Komputer dan Jaringan (TKJ), kami berupaya membekali peserta didik dengan keterampilan teknologi, sikap profesional, serta akhlakul karimah yang menjadi bekal penting dalam dunia kerja maupun kehidupan bermasyarakat. Kami berharap SMK Umar Mas’ud dapat terus memberi motivasi, inspirasi, dan kontribusi nyata bagi kemajuan pendidikan di Pulau Bawean. Kepada calon peserta didik baru, selamat datang dan mari bergabung bersama kami untuk menatap masa depan yang lebih cerah.
          <br><br>
          Wassalamu’alaikum Warahmatullahi Wabarakatuh
        </p>
        <p><strong>- Nur Hadi, S.Sy</strong></p>
      </div>
    </div>
  </div>
</section>


   <section id="profilsekolah" style="background:#f8fbff;padding:48px 0; color:#000; text-align:justify;">
  <div class="container">
    <h2 style="color:#000;">Profil Sekolah</h2>
    <p style="margin-top:12px;max-width:800px; color:#000;">
      SMK Umar Mas’ud adalah sekolah menengah kejuruan pertama di Pulau Bawean yang berdiri sejak 18 Mei 2013 di bawah naungan Yayasan Syech Maulana Umar Mas’ud. Sekolah ini hadir untuk menjawab tantangan pendidikan dengan pendekatan inovatif, terutama dalam bidang teknologi, agar lulusan siap bersaing di dunia kerja maupun melanjutkan pendidikan tinggi.
    </p>
    <div class="grid-3" style="margin-top:20px">
      <div class="card">
        <h3 style="color:#000;">Visi</h3>
        <p style="color:#000;">
          Terwujudnya SMK yang mandiri dan unggul yang menghasilkan lulusan dengan SDM profesional, kompetitif, terampil, mandiri, disiplin, berakhlakul karimah, dan bertakwa kepada Tuhan Yang Maha Esa.
        </p>
      </div>
      <div class="card">
        <h3 style="color:#000;">Misi</h3>
        <ul style="color:#000;">
          <li>Lulusan berakhlakul karimah da bertakwa.</li>
          <li>Menguasai IPTEK sesuai perkembangan zaman.</li>
          <li>Pusat pendidikan berbasis kompetensi industri dan kewirausahaan.</li>
          <li>Kompetensi sesuai kebutuhan dunia usaha & industri.</li>
          <li>Alumni siap kerja, wirausaha, atau melanjutkan kuliah.</li>
        </ul>
      </div>
      <div class="card">
        <h3 style="color:#000;">Keunggulan</h3>
        <ul style="color:#000;">
          <li>Jurusan unggulan TKJ sesuai kebutuhan industri.</li>
          <li>Pembelajaran berbasis praktik dan teknologi.</li>
          <li>Guru profesional dan berpengalaman.</li>
          <li>Pembinaan karakter Islami dan kegiatan religi.</li>
          <li>Peluang kerja dan wirausaha luas bagi lulusan.</li>
          <li>Lingkungan belajar kondusif, disiplin dan kekeluargaan.</li>
        </ul>
      </div>
    </div>
  </div>
</section>

    <section id="jurusan" class="container" style="margin-top:32px">
  <h2 style="margin-bottom:12px; color:#000;">Jurusan</h2>
  <div class="jurusan-box" style="display:flex; align-items:flex-start; gap:16px; margin-top:12px; flex-wrap:wrap;">
    <div class="jurusan-text" style="flex:1; min-width:300px; color:#000;">
      <h3 style="margin-bottom:8px; color:#000;">Teknik Komputer dan Jaringan (TKJ)</h3>
      <p class="muted" style="margin-bottom:8px; color:#000;">
        Jurusan TKJ membekali siswa dengan keterampilan merakit, mengelola, dan memperbaiki komputer serta membangun dan mengelola jaringan komputer. 
        Siswa juga diajarkan tentang administrasi perkantoran seperti Microsoft Office dan Desain Grafis.
      </p>
      <ul class="muted" style="margin-top:0; padding-left:20px; color:#000;">
        <li>Masa Studi 3 Tahun</li>
        <li>Bekerja dan Berwirausaha</li>
        <li>Melanjutkan Studi : Teknik Informatika, Sistem Informasi</li>
      </ul>
    </div>

    <div class="jurusan-img" style="flex:1; min-width:280px; text-align:center;">
      <img src="img/tekaje2.png" alt="Jurusan TKJ" style="max-width:80%;">
    </div>
  </div>
</section>


<section id="infoppdb" style="background:#f8fbff;padding:48px 0">
  <div class="container">
    <h2 style="color:#000;">Informasi PPDB</h2>
    <div class="timeline" style="margin-top:16px; display:flex; flex-direction:column; gap:16px;">

      <!-- Item 1 -->
      <div class="item" style="padding:0; background:transparent; display:flex; gap:14px; align-items:flex-start;">
        <div class="nomor" style="background:#004080; color:#000; font-weight:700; padding:8px; border-radius:50%; min-width:32px; text-align:center;">1</div>
        <div>
          <strong>Pendaftaran</strong>
          <div class="muted">Pendaftaran dibuka pada bulan Mei - Juli 2026</div>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="item" style="padding:0; background:transparent; display:flex; gap:14px; align-items:flex-start;">
        <div class="nomor" style="background:#004080; color:#000; font-weight:700; padding:8px; border-radius:50%; min-width:32px; text-align:center;">2</div>
        <div>
          <strong>Persyaratan</strong>
          <div class="muted">SKL/Ijazah SMP/MTs, KK, Akta, Pas Foto 3x4, KTP Ortu, Sertifikat Prestasi</div>
        </div>
      </div>

      <!-- Item 3 -->
      <div class="item" style="padding:0; background:transparent; display:flex; gap:14px; align-items:flex-start;">
        <div class="nomor" style="background:#004080; color:#000; font-weight:700; padding:8px; border-radius:50%; min-width:32px; text-align:center;">3</div>
        <div>
          <strong>Lokasi</strong>
          <div class="muted">Pendaftaran bisa dilakukan secara Online dan Offline (Kantor SMK UMMA)</div>
        </div>
      </div>

    </div>
  </div>
</section>

   <section id="alurpendaftaran" class="container" style="margin-top:40px;">
  <h2 style="text-align:center; color:#000; margin-bottom:32px;">Alur Pendaftaran</h2>

  <div style="display:flex; gap:20px; flex-wrap:wrap; justify-content:center;">

    <!-- Step 1 -->
    <div style="flex:1 1 180px; max-width:180px; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); text-align:center;">
      <div style="width:40px; height:40px; margin:auto; border-radius:50%; background:#004080; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; margin-bottom:12px;">
        1
      </div>
      <h3 style="font-size:16px; color:#000; margin:0 0 8px;">Buka Website</h3>
      <p style="font-size:14px; color:#000; margin:0;">Akses website resmi PPDB SMK UM.</p>
    </div>

    <!-- Step 2 -->
    <div style="flex:1 1 180px; max-width:180px; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); text-align:center;">
      <div style="width:40px; height:40px; margin:auto; border-radius:50%; background:#004080; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; margin-bottom:12px;">
        2
      </div>
      <h3 style="font-size:16px; color:#000; margin:0 0 8px;">Klik Daftar</h3>
      <p style="font-size:14px; color:#000; margin:0;">Tekan tombol daftar untuk ke Google Form.</p>
    </div>

    <!-- Step 3 -->
    <div style="flex:1 1 180px; max-width:180px; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); text-align:center;">
      <div style="width:40px; height:40px; margin:auto; border-radius:50%; background:#004080; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; margin-bottom:12px;">
        3
      </div>
      <h3 style="font-size:16px; color:#000; margin:0 0 8px;">Isi Formulir</h3>
      <p style="font-size:14px; color:#000; margin:0;">Lengkapi data diri dan orang tua.</p>
    </div>

    <!-- Step 4 -->
    <div style="flex:1 1 180px; max-width:180px; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); text-align:center;">
      <div style="width:40px; height:40px; margin:auto; border-radius:50%; background:#004080; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; margin-bottom:12px;">
        4
      </div>
      <h3 style="font-size:16px; color:#000; margin:0 0 8px;">Upload Berkas</h3>
      <p style="font-size:14px; color:#000; margin:0;">Unggah : SKL/Ijazah SMP/MTs, KK, Akta, Pas Foto 3x4, KTP Ortu, Sertifikat Prestasi</p>
    </div>

    <!-- Step 5 -->
    <div style="flex:1 1 180px; max-width:180px; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); text-align:center;">
      <div style="width:40px; height:40px; margin:auto; border-radius:50%; background:#004080; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; margin-bottom:12px;">
        5
      </div>
      <h3 style="font-size:16px; color:#000; margin:0 0 8px;">Kirim Form</h3>
      <p style="font-size:14px; color:#000; margin:0;">Periksa data lalu submit formulir.</p>
    </div>

    <!-- Step 6 -->
    <div style="flex:1 1 180px; max-width:180px; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); text-align:center;">
      <div style="width:40px; height:40px; margin:auto; border-radius:50%; background:#004080; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; margin-bottom:12px;">
        6
      </div>
      <h3 style="font-size:16px; color:#000; margin:0 0 8px;">Selesai</h3>
      <p style="font-size:14px; color:#000; margin:0;">Pendaftaran berhasil, tunggu informasi selanjutnya.</p>
    </div>

  </div>

<div style="text-align:center; margin-top:32px;">
  <a class="btn-primary" href="Form/ppdb.php"
     style="padding:12px 24px; background:#004080; color:#fff; border-radius:8px; text-decoration:none; font-weight:600; box-shadow:0 4px 8px rgba(0,0,0,0.15);">
    Daftar Sekarang
  </a>
</div>
</section>


<section id="mitra" class="py-20 bg-white">
  <div class="container mx-auto px-6 md:px-12">
    <div class="flex flex-col md:flex-row items-center justify-between gap-10">
      <div class="md:w-1/2">
        <h2 class="text-3xl font-bold text-[#004080] mb-4">Kerjasama Mitra</h2>
        <p class="text-gray-700 leading-relaxed">
          SMK Umar Mas’ud telah menjalin kerjasama dengan berbagai dunia usaha dan dunia industri (DUDI) 
          sebagai bentuk dukungan dalam meningkatkan kualitas pendidikan dan penyerapan lulusan.
        </p>
      </div>
      <div class="md:w-1/2 grid grid-cols-2 sm:grid-cols-3 gap-6 justify-items-center">
        <img src="img/telkom.png" alt="Mitra 1" class="grayscale hover:grayscale-0 transition" style="height:90px; width:auto;">
        <img src="img/yamaha.png" alt="Mitra 2" class="grayscale hover:grayscale-0 transition" style="height:90px; width:auto;">
        <img src="img/indobismar.jpeg" alt="Mitra 3" class="grayscale hover:grayscale-0 transition" style="height:90px; width:auto;">
        <img src="img/maspion.png" alt="Mitra 4" class="grayscale hover:grayscale-0 transition" style="height:90px; width:auto;">
        <img src="img/artaboga.png" alt="Mitra 5" class="grayscale hover:grayscale-0 transition" style="height:90px; width:auto;">
      </div>
    </div>
  </div>
</section>


<section id="alumni" style="padding:60px 0;background:#f8fbff;overflow:hidden">
  <div class="container" style="text-align:center;max-width:1100px;margin:auto">
    <h2 style="font-size:28px;font-weight:700;color:#000;margin-bottom:30px">Ini Kata Mereka</h2>

    <!-- Wrapper -->
    <div class="slider-wrapper" style="position:relative;width:100%;overflow:hidden">
      <div class="slider-track" style="display:flex;gap:20px;transition:transform 0.6s linear">
        
        <!-- Card 1 -->
        <div class="alumni-card" style="flex:0 0 300px;background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.08);text-align:left">
          <div style="display:flex;align-items:center;gap:15px;margin-bottom:12px">
            <img src="img/ikhsan.jpg" alt="Ikhsan" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
            <div>
              <h3 style="margin:0;font-size:18px;color:#000">Ikhsan</h3>
              <p style="font-size:13px;color:#555;margin:2px 0 0">Lulusan 2019</p>
            </div>
          </div>
          <p style="font-size:14px;color:#000;font-style:italic;text-align:justify;line-height:1.5">
            Belajar di SMK Umar Mas’ud jurusan TKJ memberi saya bekal ilmu dan keterampilan praktis yang langsung bisa diterapkan di dunia kerja.
          </p>
        </div>

        <!-- Card 2 -->
        <div class="alumni-card" style="flex:0 0 300px;background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.08);text-align:left">
          <div style="display:flex;align-items:center;gap:15px;margin-bottom:12px">
            <img src="img/inaya.jpg" alt="Inayatul Ilmiyah" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
            <div>
              <h3 style="margin:0;font-size:18px;color:#000">Inayatul Ilmiyah</h3>
              <p style="font-size:13px;color:#555;margin:2px 0 0">Lulusan 2020</p>
            </div>
          </div>
          <p style="font-size:14px;color:#000;font-style:italic;text-align:justify;line-height:1.5">
            Selama belajar di SMK Umar Mas’ud jurusan TKJ, saya mendapatkan ilmu dan keterampilan praktis yang sangat bermanfaat di dunia kerja.
          </p>
        </div>

        <!-- Card 3 -->
        <div class="alumni-card" style="flex:0 0 300px;background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.08);text-align:left">
          <div style="display:flex;align-items:center;gap:15px;margin-bottom:12px">
            <img src="img/azhari.jpg" alt="Azhari" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
            <div>
              <h3 style="margin:0;font-size:18px;color:#000">Azhari</h3>
              <p style="font-size:13px;color:#555;margin:2px 0 0">Lulusan 2024</p>
            </div>
          </div>
          <p style="font-size:14px;color:#000;font-style:italic;text-align:justify;line-height:1.5">
            Saya sangat senang bisa menempuh pendidikan di SMK Umar Mas’ud jurusan TKJ. Sekarang saya bisa bekerja sesuai ilmu komputer yang saya pelajari.
          </p>
        </div>

        <!-- Card 4 -->
        <div class="alumni-card" style="flex:0 0 300px;background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.08);text-align:left">
          <div style="display:flex;align-items:center;gap:15px;margin-bottom:12px">
            <img src="img/khusnul.jpg" alt="Alumni 4" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
            <div>
              <h3 style="margin:0;font-size:18px;color:#000">Khusnul Khotimah</h3>
              <p style="font-size:13px;color:#555;margin:2px 0 0">Lulusan 2020</p>
            </div>
          </div>
          <p style="font-size:14px;color:#000;font-style:italic;text-align:justify;line-height:1.5">
            "Belajar di SMK Umar Mas’ud bukan hanya tentang teori, tapi juga praktik langsung. Dari jurusan TKJ saya belajar mengelola komputer dan jaringan, yang sekarang sangat membantu pekerjaan saya sebagai Operator Sekolah di MINU 21 Bululanjang. Saya bangga jadi alumni SMK UMMA."
          </p>
        </div>

        <div class="alumni-card" style="flex:0 0 300px;background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.08);text-align:left">
          <div style="display:flex;align-items:center;gap:15px;margin-bottom:12px">
            <img src="img/rohani.jpg" alt="Alumni 4" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
            <div>
              <h3 style="margin:0;font-size:18px;color:#000">Siti Rohani</h3>
              <p style="font-size:13px;color:#555;margin:2px 0 0">Lulusan 2024</p>
            </div>
          </div>
          <p style="font-size:14px;color:#000;font-style:italic;text-align:justify;line-height:1.5">
            "SMK Umar Mas’ud memberikan saya bekal keterampilan komputer dan administrasi yang sangat berguna. Setelah lulus, saya bisa bekerja sebagai Admin Klinik, dan semua itu berkat ilmu serta bimbingan dari guru-guru SMK UMMA."
          </p>
        </div>

        <div class="alumni-card" style="flex:0 0 300px;background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.08);text-align:left">
          <div style="display:flex;align-items:center;gap:15px;margin-bottom:12px">
            <img src="img/puput.jpg" alt="Alumni 4" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
            <div>
              <h3 style="margin:0;font-size:18px;color:#000">Puput Maulidiny</h3>
              <p style="font-size:13px;color:#555;margin:2px 0 0">Lulusan 2024</p>
            </div>
          </div>
          <p style="font-size:14px;color:#000;font-style:italic;text-align:justify;line-height:1.5">
            "Belajar di SMK Umar Mas’ud membuat saya memiliki dasar komputer yang kuat. Setelah lulus, saya melanjutkan kuliah di Jurusan Sistem Informasi, dan alhamdulillah materi yang pernah saya pelajari di SMK sangat membantu memahami perkuliahan."
          </p>
        </div>

        <div class="alumni-card" style="flex:0 0 300px;background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.08);text-align:left">
          <div style="display:flex;align-items:center;gap:15px;margin-bottom:12px">
            <img src="img/devi.jpg" alt="Alumni 4" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
            <div>
              <h3 style="margin:0;font-size:18px;color:#000">Devi Wulansari</h3>
              <p style="font-size:13px;color:#555;margin:2px 0 0">Lulusan 2020</p>
            </div>
          </div>
          <p style="font-size:14px;color:#000;font-style:italic;text-align:justify;line-height:1.5">
            "SMK Umar Mas’ud memberi saya banyak pengalaman praktik jaringan dan komputer. Setelah lulus, saya mantap melanjutkan kuliah di Jurusan Teknik Informatika. Ilmu dari SMK sangat bermanfaat sebagai bekal di perkuliahan."
          </p>
        </div>

      </div>
    </div>
  </div>
</section>


    <footer style="background:#004080;color:#fff;padding:40px 20px;border-top:3px solid #003366">
  <div class="container" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:25px;align-items:start">

    <div>
      <h3 style="margin-bottom:12px;font-size:18px;font-weight:700">Alamat</h3>
      <p style="margin:0;line-height:1.6;font-size:14px">
        Jalan Nirwana No. 01<br>
        Dusun Kebundaya, Desa Sawah Mulya,<br>
        Kecamatan Sangkapura, Kabupaten Gresik<br>
        Kode Pos 6181<br>
      </p>
    </div>

    <div>
      <h3 style="margin-bottom:12px;font-size:18px;font-weight:700">Follow Us</h3>
      
      <div style="display:flex;gap:12px;align-items:center;margin-bottom:10px;flex-wrap:wrap">
        <a href="https://www.instagram.com/smkumma_/" target="_blank" style="color:#fff;text-decoration:none;display:flex;align-items:center;gap:6px;font-size:14px;transition:0.3s">
          <img src="img/logoig.png" alt="Instagram" style="width:18px;height:18px"> Instagram
        </a>

        <a href="https://www.youtube.com/@smkumarmasud9104" target="_blank" style="color:#fff;text-decoration:none;display:flex;align-items:center;gap:6px;font-size:14px;transition:0.3s">
          <img src="img/logoyoutube.png" alt="YouTube" style="width:18px;height:18px"> YouTube
        </a>

        <a href="https://wa.me/6282332821626" target="_blank" style="color:#fff;text-decoration:none;display:flex;align-items:center;gap:6px;font-size:14px;transition:0.3s">
          <img src="img/logowa.png" alt="WhatsApp" style="width:18px;height:18px"> WhatsApp
        </a>
      </div>

      <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-start">
        <a href="https://www.tiktok.com/@smkumarmasud" target="_blank" style="color:#fff;text-decoration:none;display:flex;align-items:center;gap:6px;font-size:14px;transition:0.3s">
          <img src="img/logotiktok.png" alt="Tiktok" style="width:18px;height:18px"> Tiktok
        </a>
        <a href="http://www.smkumarmasud.sch.com" target="_blank" style="color:#fff;text-decoration:none;display:flex;align-items:center;gap:6px;font-size:14px;transition:0.3s">
          <img src="img/logoweb.png" alt="Website Sekolah" style="width:18px;height:18px"> Website Sekolah
        </a>
        <a href="https://www.ysmumma.com/" target="_blank" style="color:#fff;text-decoration:none;display:flex;align-items:center;gap:6px;font-size:14px;transition:0.3s">
          <img src="img/logoweb.png" alt="Website Yayasan" style="width:18px;height:18px"> Website Yayasan
        </a>
      </div>

    </div>

    <div>
  <h3 style="margin-bottom:12px;font-size:18px;font-weight:700">Google Maps</h3>
  <iframe
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.123456789!2d112.659354!3d-5.845183!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2ddf55d1a71d1dd3%3A0xcda1279b5d52ca0a!2sSMK%20Umar%20Mas'ud!5e0!3m2!1sid!2sid!4v1694812345678!5m2!1sid!2sid"
    width="100%"
    height="140"
    style="border:0;border-radius:10px;"
    allowfullscreen=""
    loading="lazy"
    referrerpolicy="no-referrer-when-downgrade">
  </iframe>
</div>

  </div>

  <div style="text-align:center;margin-top:30px;font-size:13px;color:#ccc">
    &copy; 2025 Devi Wulansari | SMK Umar Mas'ud. All Rights Reserved.
  </div>
</footer>



  <script>
    function toggleMenu(){
      const nav = document.querySelector('nav ul');
      if(!nav) return;
      if(nav.style.display === 'flex') nav.style.display = 'none';
      else nav.style.display = 'flex';
    }

    // Close mobile menu on link click
    document.addEventListener('click', (e)=>{
      if(e.target.matches('nav a')){
        const navul = document.querySelector('nav ul');
        if(window.innerWidth<=900 && navul) navul.style.display='none';
      }
    })
  </script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // Pilih semua section + hero, kecuali header/footer
  const sections = document.querySelectorAll('main section, section.hero, section:not(header):not(footer)');

  // Tambahkan class animate untuk semua section
  sections.forEach(section => section.classList.add('animate'));

  // Intersection Observer untuk animasi saat scroll
  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if(entry.isIntersecting){
        entry.target.classList.add('visible');
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.2 });

  sections.forEach(section => observer.observe(section));

  // Animasi langsung untuk hero saat load
  const hero = document.querySelector('.hero');
  if(hero) {
    hero.classList.add('animate');
    setTimeout(() => hero.classList.add('visible'), 100); // delay kecil biar smooth
  }
});
</script>

<script>
window.addEventListener('load', () => {
  const opening = document.getElementById('opening');
  // Tahan 2 detik lalu fade out
  setTimeout(() => {
    opening.style.opacity = '0';
    setTimeout(() => {
      opening.style.display = 'none';
      // Optional: jalankan animasi rotate-in untuk hero setelah opening
      const hero = document.querySelector('.hero');
      if(hero) hero.classList.add('visible');
    }, 1000); // durasi fade out sama dengan CSS transition
  }, 2000); // durasi tampilan logo
});
</script>

<script>
  const track = document.querySelector('.slider-track');
  const wrapper = document.querySelector('.slider-wrapper');
  const cards = document.querySelectorAll('.slider-track .alumni-card');
  const cardWidth = 320; // 300px + 20px gap
  let index = 0;
  let slideInterval;

  function slide() {
    index++;
    track.style.transition = "transform 0.6s linear";
    track.style.transform = `translateX(${-index * cardWidth}px)`;

    if (index >= cards.length / 2) {
      setTimeout(() => {
        track.style.transition = "none";
        track.style.transform = "translateX(0)";
        index = 0;
      }, 700);
    }
  }

  function startSlide() {
    slideInterval = setInterval(slide, 2000);
  }

  function stopSlide() {
    clearInterval(slideInterval);
  }

  // mulai otomatis
  startSlide();

  // berhenti kalau kursor masuk
  wrapper.addEventListener('mouseenter', stopSlide);
  // lanjut lagi kalau kursor keluar
  wrapper.addEventListener('mouseleave', startSlide);
</script>

</body>
</html>
