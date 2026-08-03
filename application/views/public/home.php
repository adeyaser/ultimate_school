<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <title><?= isset($sekolah['nama_sekolah']) ? $sekolah['nama_sekolah'] : 'Ultimate School' ?> - Official Website & PPDB Online</title>

    <!-- Google Fonts & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <!-- AdminLTE 4 / Bootstrap 5 CSS -->
    <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>" />
    <!-- Custom Modern Compro Aesthetics -->
    <link rel="stylesheet" href="<?= base_url('assets/css/compro-custom.css') ?>" />
  </head>
  <body class="bg-body-tertiary">

    <!-- Fixed Top Header -->
    <header class="fixed-top shadow-sm z-3 bg-white">
      <!-- Top Announcement Bar -->
      <div class="bg-dark text-white py-2 px-3 small border-bottom border-secondary">
        <div class="container d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center overflow-hidden">
            <span class="badge text-bg-danger me-2 px-2 py-1"><i class="bi bi-megaphone-fill me-1"></i> PENGUMUMAN</span>
            <marquee class="opacity-90 me-3" scrollamount="5">
              <?= isset($sekolah['running_text']) ? $sekolah['running_text'] : '🔥 Penerimaan Peserta Didik Baru (PPDB) T.A. 2026/2027 Resmi Dibuka! Segera Daftarkan Putra/Putri Anda.' ?>
            </marquee>
          </div>
          <div class="d-none d-md-flex align-items-center gap-3">
            <a href="tel:<?= isset($sekolah['telepon']) ? $sekolah['telepon'] : '02112345678' ?>" class="text-white text-decoration-none"><i class="bi bi-telephone-fill me-1 text-warning"></i> <?= isset($sekolah['telepon']) ? $sekolah['telepon'] : '021-12345678' ?></a>
            <a href="mailto:<?= isset($sekolah['email']) ? $sekolah['email'] : 'info@ultimateschool.com' ?>" class="text-white text-decoration-none"><i class="bi bi-envelope-fill me-1 text-warning"></i> <?= isset($sekolah['email']) ? $sekolah['email'] : 'info@ultimateschool.com' ?></a>
          </div>
        </div>
      </div>

      <!-- Glassmorphism Single-Row Navbar -->
      <nav class="navbar navbar-expand-xl navbar-compro">
        <div class="container-fluid container-xl">
          <a class="navbar-brand fw-bold text-primary d-flex align-items-center me-3" href="<?= base_url() ?>">
            <img src="<?= (isset($sekolah['logo']) && strpos($sekolah['logo'], 'http') === 0) ? $sekolah['logo'] : base_url(isset($sekolah['logo']) && $sekolah['logo'] ? $sekolah['logo'] : 'dist/assets/img/AdminLTELogo.png') ?>" alt="Logo" width="38" height="38" class="me-2 rounded-circle shadow-sm" />
            <span class="d-flex flex-column lh-1">
              <span class="fs-5 fw-bold"><?= isset($sekolah['nama_sekolah']) ? strtoupper($sekolah['nama_sekolah']) : 'ULTIMATE SCHOOL' ?></span>
              <small class="text-primary fw-bold" style="font-size: 0.65rem !important; letter-spacing: 0.5px;">
                JENJANG TERAKREDITASI <?= isset($sekolah['jenjang']) ? $sekolah['jenjang'] : 'SMA' ?>
              </small>
            </span>
          </a>
          <button class="navbar-toggler border-0 shadow-none p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navPublic">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navPublic">
            <ul class="navbar-nav ms-auto mb-2 mb-xl-0 fw-semibold align-items-xl-center gap-1">
              <li class="nav-item">
                <a class="nav-link active" href="#hero">
                  <i class="bi bi-house-door-fill text-primary"></i> Beranda
                </a>
              </li>

              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropProfil" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-info-circle-fill text-info"></i> Profil Sekolah
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropProfil">
                  <li><a class="dropdown-item" href="#sambutan"><i class="bi bi-chat-quote-fill text-primary"></i> Sambutan Kepsek</a></li>
                  <li><a class="dropdown-item" href="#profil"><i class="bi bi-compass-fill text-success"></i> Visi & Misi</a></li>
                  <li><a class="dropdown-item" href="#fasilitas"><i class="bi bi-building-fill text-warning"></i> Fasilitas Modern</a></li>
                  <li><a class="dropdown-item" href="#eskul"><i class="bi bi-trophy-fill text-danger"></i> Ekstrakurikuler</a></li>
                </ul>
              </li>

              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropAkademik" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-journal-bookmark-fill text-warning"></i> Akademik
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropAkademik">
                  <li><a class="dropdown-item" href="#jurusan"><i class="bi bi-journal-code text-primary"></i> Jurusan Peminatan</a></li>
                  <li><a class="dropdown-item" href="<?= base_url('auth') ?>"><i class="bi bi-laptop-fill text-success"></i> Portal CBT & Ujian</a></li>
                </ul>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#ppdb">
                  <i class="bi bi-pencil-square text-success"></i> PPDB Online
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#faq">
                  <i class="bi bi-question-circle-fill text-purple"></i> FAQ
                </a>
              </li>

              <li class="nav-item ms-xl-3 d-flex gap-2 align-items-center mt-3 mt-xl-0">
                <a class="btn btn-outline-primary btn-nav-action rounded-pill shadow-sm" href="#cek-status">
                  <i class="bi bi-search me-1"></i> Cek Status
                </a>
                <a class="btn btn-primary btn-nav-action rounded-pill shadow-sm text-white" href="<?= base_url('auth') ?>">
                  <i class="bi bi-lock-fill me-1"></i> Portal Login
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <!-- Fixed Header Spacer -->
    <div class="fixed-header-spacer"></div>

    <!-- Hero Banner Section -->
    <section id="hero" class="hero-section text-white">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-7 text-center text-lg-start">
            <span class="badge text-bg-warning px-3 py-2 fs-6 mb-3 rounded-pill fw-bold shadow-sm">
              <i class="bi bi-star-fill me-1"></i> PPDB T.A. 2026/2027 Resmi Dibuka
            </span>
            <h1 class="display-3 hero-title mb-3">
              <?= isset($sekolah['hero_title']) ? $sekolah['hero_title'] : 'Mewujudkan Generasi Unggul, Berkarakter & Berdaya Saing Global' ?>
            </h1>
            <p class="fs-5 mb-4 opacity-90 fw-light">
              <?= isset($sekolah['hero_subtitle']) ? $sekolah['hero_subtitle'] : 'Selamat datang di Ultimate School. Pendidikan berkualitas berbasis teknologi modern, kurikulum unggulan, dan pembentukan akhlak mulia.' ?>
            </p>
            <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start mb-4">
              <a href="#ppdb" class="btn btn-warning btn-lg fw-bold px-4 py-3 rounded-pill shadow-lg text-dark">
                <i class="bi bi-pencil-square me-2"></i> Daftar PPDB Online
              </a>
              <a href="#cek-status" class="btn btn-outline-light btn-lg fw-bold px-4 py-3 rounded-pill">
                <i class="bi bi-search me-2"></i> Cek Status Pendaftaran
              </a>
            </div>
          </div>
          
          <div class="col-lg-5 mt-5 mt-lg-0">
            <div class="position-relative">
              <div class="img-zoom-container shadow-2xl rounded-4">
                <?php 
                  $hero_media_src = isset($sekolah['hero_media']) && $sekolah['hero_media'] ? $sekolah['hero_media'] : 'dist/assets/img/photo1.png';
                  $hero_url = (strpos($hero_media_src, 'http') === 0) ? $hero_media_src : base_url($hero_media_src);
                  $hero_type = isset($sekolah['hero_type']) ? $sekolah['hero_type'] : 'image';
                ?>
                <?php if ($hero_type === 'video'): ?>
                  <video autoplay loop muted playsinline class="w-100 rounded-4 shadow-lg border border-4 border-white" style="max-height: 400px; object-fit: cover;">
                    <source src="<?= $hero_url ?>" type="video/mp4">
                    Browser Anda tidak mendukung pemutar video.
                  </video>
                <?php else: ?>
                  <img src="<?= $hero_url ?>" alt="Hero Media" class="img-fluid rounded-4 shadow-lg border border-4 border-white opacity-95" />
                <?php endif; ?>
              </div>
              
              <!-- Floating Badge -->
              <div class="position-absolute bottom-0 start-0 translate-middle-y ms-n3 p-3 bg-white text-dark rounded-4 shadow-lg d-none d-sm-flex align-items-center border">
                <div class="icon-wrapper bg-success text-white p-3 rounded-circle me-3">
                  <i class="bi bi-patch-check-fill fs-3"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-0">Akreditasi A (Unggul)</h6>
                  <small class="text-muted">Kementerian Pendidikan RI</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Floating Stats Banner -->
        <div class="row g-3 mt-5">
          <div class="col-6 col-md-3">
            <div class="floating-stat-card text-center text-dark">
              <h2 class="fw-bold text-primary mb-0"><?= isset($stats['total_siswa']) ? number_format($stats['total_siswa']) : '1,248' ?>+</h2>
              <small class="fw-semibold text-secondary">Siswa Aktif</small>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="floating-stat-card text-center text-dark">
              <h2 class="fw-bold text-success mb-0"><?= isset($stats['total_guru']) ? $stats['total_guru'] : '86' ?>+</h2>
              <small class="fw-semibold text-secondary">Guru & Tenaga Pendidik</small>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="floating-stat-card text-center text-dark">
              <h2 class="fw-bold text-warning mb-0"><?= isset($stats['total_kelas']) ? $stats['total_kelas'] : '36' ?></h2>
              <small class="fw-semibold text-secondary">Rombongan Belajar</small>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="floating-stat-card text-center text-dark">
              <h2 class="fw-bold text-indigo mb-0">99.8%</h2>
              <small class="fw-semibold text-secondary">Tingkat Kelulusan</small>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Sambutan Kepala Sekolah Section -->
    <section id="sambutan" class="py-5 bg-white">
      <div class="container py-4">
        <div class="row align-items-center">
          <div class="col-lg-5 mb-4 mb-lg-0 text-center">
            <div class="p-3 bg-light rounded-4 shadow-sm border d-inline-block">
              <img src="<?= base_url('dist/assets/img/avatar5.png') ?>" alt="Kepala Sekolah" class="img-fluid rounded-4 shadow mb-3" style="max-width: 280px;" />
              <h4 class="fw-bold mb-1"><?= isset($sekolah['kepala_sekolah']) ? $sekolah['kepala_sekolah'] : 'Dr. H. Ahmad Dahlan, M.Pd.' ?></h4>
              <p class="text-primary fw-bold mb-0">Kepala Sekolah <?= isset($sekolah['nama_sekolah']) ? $sekolah['nama_sekolah'] : 'Ultimate School' ?></p>
            </div>
          </div>
          <div class="col-lg-7">
            <span class="section-tag tag-blue"><i class="bi bi-chat-quote-fill me-1"></i> Sambutan Kepala Sekolah</span>
            <h2 class="fw-bold mb-3 display-6">Menyambut Masa Depan Cerah Bersama <?= isset($sekolah['nama_sekolah']) ? $sekolah['nama_sekolah'] : 'Ultimate School' ?></h2>
            <p class="fs-5 text-secondary lead mb-4">
              "<?= isset($sekolah['sambutan_kepsek']) ? nl2br($sekolah['sambutan_kepsek']) : 'Assalamu\'alaikum Warahmatullahi Wabarakatuh. Selamat datang di portal resmi sekolah kami. Kami berkomitmen menyelenggarakan pendidikan holistik yang mengintegrasikan akademik tinggi, penguasaan teknologi digital, dan penguatan karakter religius peserta didik.' ?>"
            </p>
            <div class="d-flex align-items-center gap-3">
              <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                <i class="bi bi-award fs-2"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-0">Pendidikan Berkarakter & Berbasis Teknologi</h6>
                <small class="text-muted">Mencetak alumni unggul yang siap bersaing di perguruan tinggi favorit & dunia kerja.</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Profil & Visi Misi Section -->
    <section id="profil" class="py-5 bg-light">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="section-tag tag-green"><i class="bi bi-compass-fill me-1"></i> Visi & Misi</span>
          <h2 class="fw-bold display-6">Fondasi Utama Pendidikan Kami</h2>
          <p class="text-muted fs-5">Arah dan komitmen sekolah dalam membimbing setiap peserta didik</p>
        </div>

        <div class="row g-4">
          <div class="col-md-6">
            <div class="feature-card h-100 p-4 p-md-5">
              <div class="feature-icon-wrapper icon-blue">
                <i class="bi bi-eye-fill"></i>
              </div>
              <h3 class="fw-bold mb-3">Visi Sekolah</h3>
              <p class="fs-5 text-secondary mb-0">
                <?= isset($sekolah['visi']) ? nl2br($sekolah['visi']) : 'Menciptakan generasi unggul dalam prestasi, berkarakter mulia, berwawasan lingkungan, serta menguasai sains dan teknologi.' ?>
              </p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="feature-card h-100 p-4 p-md-5">
              <div class="feature-icon-wrapper icon-green">
                <i class="bi bi-bullseye"></i>
              </div>
              <h3 class="fw-bold mb-3">Misi Sekolah</h3>
              <div class="fs-5 text-secondary">
                <?= isset($sekolah['misi']) ? nl2br($sekolah['misi']) : "1. Menyelenggarakan proses pembelajaran yang inovatif dan berorientasi pada peserta didik.\n2. Mengembangkan nilai-nilai karakter religius dan etika moral.\n3. Memfasilitasi bakat dan minat siswa melalui berbagai kegiatan ekstrakurikuler." ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Academic Programs / Jurusan Section -->
    <section id="jurusan" class="py-5 bg-white">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="section-tag tag-blue"><i class="bi bi-journal-code me-1"></i> Program Keahlian</span>
          <h2 class="fw-bold display-6">Pilihan Jurusan Peminatan</h2>
          <p class="text-muted fs-5">Kurikulum disesuaikan dengan minat, bakat, dan proyeksi karier siswa</p>
        </div>

        <div class="row g-4">
          <div class="col-md-4">
            <div class="program-card h-100">
              <span class="program-badge text-bg-primary">MIPA</span>
              <div class="feature-icon-wrapper icon-blue mb-4"><i class="bi bi-calculator"></i></div>
              <h3 class="fw-bold mb-3">Matematika & IPA</h3>
              <p class="text-secondary mb-4">Fokus pada penalaran logis, eksperimen laboratorium fisika, kimia, biologi, dan pemrograman dasar.</p>
              <ul class="list-unstyled text-muted small">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Lab Sains Lengkap</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Bimbingan Olim Sains (OSN)</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Kuis CBT & Robotik</li>
              </ul>
            </div>
          </div>

          <div class="col-md-4">
            <div class="program-card h-100">
              <span class="program-badge text-bg-warning text-dark">IPS</span>
              <div class="feature-icon-wrapper icon-amber mb-4"><i class="bi bi-graph-up-arrow"></i></div>
              <h3 class="fw-bold mb-3">Ilmu Sosial (IPS)</h3>
              <p class="text-secondary mb-4">Mempelajari ekonomi, sosiologi, geografi, dan kewirausahaan digital modern.</p>
              <ul class="list-unstyled text-muted small">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Lab Akuntansi & Mini Bank</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Program Young Entrepreneur</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Studi Lapangan Industri</li>
              </ul>
            </div>
          </div>

          <div class="col-md-4">
            <div class="program-card h-100">
              <span class="program-badge text-bg-success">Bahasa</span>
              <div class="feature-icon-wrapper icon-green mb-4"><i class="bi bi-translate"></i></div>
              <h3 class="fw-bold mb-3">Bahasa & Budaya</h3>
              <p class="text-secondary mb-4">Penguasaan Bahasa Inggris, Jepang, Mandarin, karya sastra, serta kebudayaan nusantara.</p>
              <ul class="list-unstyled text-muted small">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Native Speaker Class</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Studio Bahasa Digital</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Exchange Program Ready</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Fasilitas Sekolah Section -->
    <section id="fasilitas" class="py-5 bg-light">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="section-tag tag-green"><i class="bi bi-building-fill me-1"></i> Sarana & Prasarana</span>
          <h2 class="fw-bold display-6">Fasilitas Modern Sekolah</h2>
          <p class="text-muted fs-5">Mendukung kenyamanan dan efektivitas proses belajar mengajar</p>
        </div>

        <div class="row g-4">
          <?php if (!empty($fasilitas)): ?>
            <?php foreach ($fasilitas as $fas): ?>
              <div class="col-md-4">
                <div class="feature-card h-100">
                  <div class="img-zoom-container">
                    <img src="<?= base_url('dist/assets/img/' . ($fas['foto'] ? $fas['foto'] : 'photo1.png')) ?>" alt="<?= $fas['nama_fasilitas'] ?>" class="img-fluid w-100" style="height: 200px; object-fit: cover;" />
                  </div>
                  <div class="p-4">
                    <h4 class="fw-bold mb-2"><?= $fas['nama_fasilitas'] ?></h4>
                    <p class="text-muted mb-0"><?= $fas['deskripsi'] ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-md-4">
              <div class="feature-card h-100">
                <div class="img-zoom-container">
                  <img src="<?= base_url('dist/assets/img/photo1.png') ?>" alt="Gedung Kelas" class="img-fluid w-100" style="height: 200px; object-fit: cover;" />
                </div>
                <div class="p-4">
                  <h4 class="fw-bold mb-2">Ruang Kelas Ber-AC & Smart TV</h4>
                  <p class="text-muted mb-0">Ruang belajar nyaman berpendingin udara, dilengkapi proyektor LCD dan Smart Board multimedia.</p>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Acara & Agenda Sekolah Section -->
    <section id="acara" class="py-5 bg-white border-bottom">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="section-tag tag-blue"><i class="bi bi-calendar-event-fill me-1"></i> Agenda & Pengumuman</span>
          <h2 class="fw-bold display-6">Acara & Agenda Kegiatan Sekolah</h2>
          <p class="text-muted fs-5">Informasi jadwal kegiatan, kompetisi, dan pengumuman terbaru</p>
        </div>

        <div class="row g-4">
          <?php if (!empty($acara)): ?>
            <?php foreach ($acara as $ac): ?>
              <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                  <div class="card-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                    <span class="badge text-bg-warning fw-bold"><?= $ac['jenis'] ?></span>
                    <small><i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($ac['tanggal_mulai'])) ?></small>
                  </div>
                  <div class="card-body p-4">
                    <h5 class="fw-bold mb-2 text-primary"><?= $ac['judul'] ?></h5>
                    <p class="text-secondary small mb-3"><?= $ac['deskripsi'] ?></p>
                    <div class="border-top pt-2 d-flex justify-content-between small text-muted">
                      <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= $ac['tempat'] ?></span>
                      <span><i class="bi bi-person-fill text-success me-1"></i> <?= $ac['penanggung_jawab'] ?></span>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-md-4">
              <div class="p-4 bg-light rounded-4 text-center"><h5>MPLS T.A. 2026/2027</h5></div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Ekstrakurikuler Showcase Section -->
    <section id="eskul" class="py-5 bg-light">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="section-tag tag-green"><i class="bi bi-trophy-fill me-1"></i> Pengembangan Diri</span>
          <h2 class="fw-bold display-6">Kegiatan Ekstrakurikuler (Eskul)</h2>
          <p class="text-muted fs-5">Wadah pembentukan minat, bakat, kepemimpinan, dan prestasi siswa</p>
        </div>

        <div class="row g-4 justify-content-center">
          <?php if (!empty($eskul)): ?>
            <?php foreach ($eskul as $e): ?>
              <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="feature-card p-4 text-center h-100 border rounded-4 bg-white shadow-sm d-flex flex-column align-items-center justify-content-between">
                  <div class="w-100">
                    <div class="feature-icon-wrapper icon-green mx-auto mb-3" style="width: 58px; height: 58px; border-radius: 14px;">
                      <i class="bi bi-trophy-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark fs-6 text-wrap"><?= $e['nama_eskul'] ?></h5>
                    <p class="small text-muted mb-3"><i class="bi bi-clock-fill me-1 text-warning"></i> <?= $e['hari'] ?> (<?= date('H:i', strtotime($e['jam_mulai'])) ?> WIB)</p>
                  </div>
                  <div class="w-100 pt-2 border-top">
                    <span class="badge text-bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small text-wrap text-break fw-semibold mw-100">
                      <i class="bi bi-person-badge-fill me-1"></i> Pembina: <?= $e['nama_pembina'] ?>
                    </span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-lg-4 col-md-6 col-12">
              <div class="feature-card p-4 text-center rounded-4 border bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="feature-icon-wrapper icon-blue mx-auto mb-3"><i class="bi bi-flag-fill fs-2"></i></div>
                  <h5 class="fw-bold mb-2">Pramuka Wajib</h5>
                  <p class="small text-muted mb-3"><i class="bi bi-clock-fill me-1 text-warning"></i> Jumat (15:00 WIB)</p>
                </div>
                <div class="pt-2 border-top">
                  <span class="badge text-bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill small text-wrap text-break fw-semibold mw-100">Pembina: Budi Santoso, S.Pd</span>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
              <div class="feature-card p-4 text-center rounded-4 border bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="feature-icon-wrapper icon-amber mx-auto mb-3"><i class="bi bi-award-fill fs-2"></i></div>
                  <h5 class="fw-bold mb-2">Pasukan Pengibar Bendera</h5>
                  <p class="small text-muted mb-3"><i class="bi bi-clock-fill me-1 text-warning"></i> Sabtu (08:00 WIB)</p>
                </div>
                <div class="pt-2 border-top">
                  <span class="badge text-bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill small text-wrap text-break fw-semibold mw-100">Pembina: Siti Aminah, M.Pd</span>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
              <div class="feature-card p-4 text-center rounded-4 border bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="feature-icon-wrapper icon-green mx-auto mb-3"><i class="bi bi-heart-pulse-fill fs-2"></i></div>
                  <h5 class="fw-bold mb-2">PMR & KSR Red Cross</h5>
                  <p class="small text-muted mb-3"><i class="bi bi-clock-fill me-1 text-warning"></i> Sabtu (10:00 WIB)</p>
                </div>
                <div class="pt-2 border-top">
                  <span class="badge text-bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small text-wrap text-break fw-semibold mw-100">Pembina: Dr. Hendra</span>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- PPDB Registration Form Section -->
    <section id="ppdb" class="py-5 bg-light">
      <div class="container py-4">
        <div class="row justify-content-center">
          <div class="col-lg-9">
            <div class="card border-0 shadow-2xl rounded-4">
              <div class="card-header bg-primary text-white p-4 rounded-top-4 text-center">
                <span class="badge text-bg-warning px-3 py-2 fw-bold text-dark mb-2">PPDB ONLINE T.A. 2026/2027</span>
                <h2 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2"></i> Formulir Pendaftaran Siswa Baru</h2>
                <p class="mb-0 opacity-90">Lengkapi data diri calon murid secara benar untuk mengikuti seleksi masuk</p>
              </div>
              <div class="card-body p-4 p-md-5">

                <?php if ($this->session->flashdata('success_ppdb')): ?>
                  <div class="alert alert-success p-4 rounded-4 mb-4 shadow-sm border-0">
                    <h4 class="fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Pendaftaran Berhasil!</h4>
                    <?= $this->session->flashdata('success_ppdb') ?>
                  </div>
                <?php endif; ?>

                <form action="<?= base_url('home/daftar_ppdb') ?>" method="post">
                  <div class="p-3 bg-primary-subtle text-primary rounded-3 fw-bold mb-4">
                    <i class="bi bi-person-lines-fill me-2"></i> 1. Informasi Data Diri Calon Siswa
                  </div>

                  <div class="row g-3 mb-4">
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Nama Lengkap Siswa *</label>
                      <input type="text" name="nama_lengkap" class="form-control form-control-lg" placeholder="Ahmad Fauzi" required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">NISN (Nomor Induk Siswa Nasional)</label>
                      <input type="text" name="nisn" class="form-control form-control-lg" placeholder="0081239011" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Tempat Lahir *</label>
                      <input type="text" name="tempat_lahir" class="form-control form-control-lg" placeholder="Jakarta" required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Tanggal Lahir *</label>
                      <input type="date" name="tanggal_lahir" class="form-control form-control-lg" required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Jenis Kelamin *</label>
                      <select name="jenis_kelamin" class="form-select form-select-lg" required>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Pilihan Jurusan Peminatan *</label>
                      <select name="jurusan_dipilih" class="form-select form-select-lg" required>
                        <option value="MIPA">MIPA (Matematika & IPA)</option>
                        <option value="IPS">IPS (Ilmu Pengetahuan Sosial)</option>
                        <option value="Bahasa & Budaya">Bahasa & Budaya</option>
                      </select>
                    </div>
                  </div>

                  <div class="p-3 bg-success-subtle text-success rounded-3 fw-bold mb-4">
                    <i class="bi bi-telephone-fill me-2"></i> 2. Kontak & Riwayat Sekolah
                  </div>

                  <div class="row g-3 mb-4">
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Nomor Telepon / WhatsApp Aktif *</label>
                      <input type="text" name="telepon" class="form-control form-control-lg" placeholder="081234567890" required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Email Siswa / Orang Tua</label>
                      <input type="email" name="email" class="form-control form-control-lg" placeholder="email@domain.com" />
                    </div>
                    <div class="col-md-12">
                      <label class="form-label fw-bold">Asal Sekolah (SMP/MTs)</label>
                      <input type="text" name="asal_sekolah" class="form-control form-control-lg" placeholder="SMP Negeri 1 Jakarta" />
                    </div>
                    <div class="col-md-12">
                      <label class="form-label fw-bold">Alamat Rumah Lengkap *</label>
                      <textarea name="alamat" class="form-control form-control-lg" rows="3" placeholder="Jl. Pendidikan No. 123, RT 01/RW 02..." required></textarea>
                    </div>
                  </div>

                  <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-lg rounded-pill">
                    <i class="bi bi-send-fill me-2"></i> Kirim Formulir Pendaftaran PPDB Online
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Cek Status Section -->
    <section id="cek-status" class="py-5 bg-white">
      <div class="container py-4">
        <div class="row justify-content-center">
          <div class="col-lg-7 text-center">
            <span class="section-tag tag-blue"><i class="bi bi-search me-1"></i> Tracking Pendaftaran</span>
            <h2 class="fw-bold mb-3">Cek Status Seleksi PPDB</h2>
            <p class="text-muted mb-4">Masukkan Nomor Pendaftaran (Contoh: <code>PPDB-20260802-1234</code>) untuk melihat pengumuman seleksi.</p>

            <form action="<?= base_url('home/cek_status') ?>" method="get" class="d-flex gap-2">
              <input type="text" name="no_pendaftaran" class="form-control form-control-lg" placeholder="Masukkan Nomor Pendaftaran PPDB..." required />
              <button type="submit" class="btn btn-primary btn-lg fw-bold px-4"><i class="bi bi-search me-1"></i> Cek</button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5 bg-light">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="section-tag tag-green"><i class="bi bi-question-circle me-1"></i> Bantuan & FAQ</span>
          <h2 class="fw-bold display-6">Pertanyaan Sering Diajukan</h2>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="accordion accordion-compro" id="faqAccordion">
              <?php if (!empty($faqs)): ?>
                <?php foreach ($faqs as $idx => $f): ?>
                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button <?= ($idx > 0) ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq_item_<?= $f['id'] ?>">
                        <?= $f['pertanyaan'] ?>
                      </button>
                    </h2>
                    <div id="faq_item_<?= $f['id'] ?>" class="accordion-collapse collapse <?= ($idx === 0) ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                      <div class="accordion-body text-secondary">
                        <?= nl2br($f['jawaban']) ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                      Bagaimana alur pendaftaran PPDB Online di Ultimate School?
                    </button>
                  </h2>
                  <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary">
                      Isi formulir pendaftaran online pada halaman ini. Anda akan mendapatkan Nomor Pendaftaran unik. Panitia PPDB akan memverifikasi berkas dan mengumumkan hasil seleksi di menu Cek Status.
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4">
      <div class="container">
        <div class="row g-4 mb-4">
          <div class="col-lg-4">
            <h3 class="fw-bold text-primary mb-3"><?= isset($sekolah['nama_sekolah']) ? strtoupper($sekolah['nama_sekolah']) : 'ULTIMATE SCHOOL' ?></h3>
            <p class="text-secondary small mb-3">Sistem Informasi Manajemen Sekolah Terpadu dengan fasilitas terlengkap, kurikulum unggulan, dan layanan digital modern.</p>
            <div class="d-flex gap-2">
              <a href="<?= isset($sekolah['facebook_url']) && $sekolah['facebook_url'] ? $sekolah['facebook_url'] : '#' ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-facebook"></i></a>
              <a href="<?= isset($sekolah['instagram_url']) && $sekolah['instagram_url'] ? $sekolah['instagram_url'] : '#' ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-instagram"></i></a>
              <a href="<?= isset($sekolah['youtube_url']) && $sekolah['youtube_url'] ? $sekolah['youtube_url'] : '#' ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-youtube"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <h5 class="fw-bold mb-3">Navigasi Pintar</h5>
            <ul class="list-unstyled small">
              <li class="mb-2"><a href="#hero" class="text-secondary text-decoration-none">Beranda</a></li>
              <li class="mb-2"><a href="#profil" class="text-secondary text-decoration-none">Visi & Misi</a></li>
              <li class="mb-2"><a href="#jurusan" class="text-secondary text-decoration-none">Jurusan</a></li>
              <li class="mb-2"><a href="#ppdb" class="text-secondary text-decoration-none">PPDB Online</a></li>
            </ul>
          </div>

          <div class="col-lg-5">
            <h5 class="fw-bold mb-3">Kontak & Lokasi Sekolah</h5>
            <p class="small text-secondary mb-1"><i class="bi bi-geo-alt-fill text-danger me-2"></i> <?= isset($sekolah['alamat']) ? $sekolah['alamat'] : 'Jl. Pendidikan No. 123, Jakarta' ?></p>
            <p class="small text-secondary mb-1"><i class="bi bi-telephone-fill text-success me-2"></i> <?= isset($sekolah['telepon']) ? $sekolah['telepon'] : '021-12345678' ?></p>
            <p class="small text-secondary mb-0"><i class="bi bi-envelope-fill text-warning me-2"></i> <?= isset($sekolah['email']) ? $sekolah['email'] : 'info@ultimateschool.com' ?></p>
          </div>
        </div>

        <div class="border-top border-secondary pt-3 text-center small text-secondary">
          &copy; <?= date('Y') ?> <?= isset($sekolah['nama_sekolah']) ? $sekolah['nama_sekolah'] : 'Ultimate School' ?>. All Rights Reserved. AdminLTE v4.1.0 Integrated.
        </div>
      </div>
    </footer>

    <!-- Back to top floating button -->
    <a href="#hero" class="floating-back-top shadow-lg" title="Ke Atas">
      <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
