<?php
  $seg1 = $this->uri->segment(1);
  $seg2 = $this->uri->segment(2);
  $jenjang = isset($school_info['jenjang']) ? $school_info['jenjang'] : 'SMP';
  $user_role = isset($user_data['role']) ? $user_data['role'] : ($this->session->userdata('role') ? $this->session->userdata('role') : 'super_admin');
  
  if (empty($allowed_menus)) {
      $CI =& get_instance();
      $CI->load->model('M_Acl');
      $allowed = $CI->M_Acl->get_allowed_menu_codes($user_role);
  } else {
      $allowed = $allowed_menus;
  }

  if (!function_exists('is_allowed_menu')) {
      function is_allowed_menu($code, $allowed, $user_role) {
          if ($user_role === 'super_admin') return true;
          return in_array($code, $allowed);
      }
  }

  // Dynamic Terminology Presets
  if ($jenjang === 'SD') {
      $badge_color   = 'bg-success';
      $label_jenjang = 'SD (Sekolah Dasar)';
      $label_murid   = 'Murid SD (Kelas 1-6)';
      $label_kelas   = 'Rombel Kelas I - VI';
      $label_guru    = 'Guru Tematik & Mapel SD';
      $label_raport  = 'Raport Merdeka SD';
      $label_ppdb    = 'PPDB SD (Usia 6-12 Thn)';
      $label_spp     = 'SPP & Uang Kegiatan SD';
      $label_cbt     = 'Ujian & Evaluasi SD';
  } elseif ($jenjang === 'SMP') {
      $badge_color   = 'bg-info';
      $label_jenjang = 'SMP (Menengah Pertama)';
      $label_murid   = 'Siswa SMP (Kelas 7-9)';
      $label_kelas   = 'Rombel Kelas VII - IX';
      $label_guru    = 'Guru Mapel & BK SMP';
      $label_raport  = 'Raport Digital SMP';
      $label_ppdb    = 'PPDB Online SMP';
      $label_spp     = 'SPP & Operasional SMP';
      $label_cbt     = 'CBT & Asesmen SMP';
  } elseif ($jenjang === 'SMK') {
      $badge_color   = 'bg-danger';
      $label_jenjang = 'SMK (Kejuruan)';
      $label_murid   = 'Siswa SMK (Kelas 10-12)';
      $label_kelas   = 'Rombel & Keahlian SMK';
      $label_guru    = 'Guru Produktif SMK';
      $label_raport  = 'Raport & Sertifikasi SMK';
      $label_ppdb    = 'PPDB Online SMK';
      $label_spp     = 'SPP & Lab Kejuruan';
      $label_cbt     = 'CBT & UKK Kejuruan';
  } else { // SMA
      $badge_color   = 'bg-warning text-dark';
      $label_jenjang = 'SMA (Menengah Atas)';
      $label_murid   = 'Siswa SMA (Kelas 10-12)';
      $label_kelas   = 'Rombel Kelas X - XII';
      $label_guru    = 'Guru Rumpun Mapel SMA';
      $label_raport  = 'Raport Akademik SMA';
      $label_ppdb    = 'PPDB Online SMA';
      $label_spp     = 'SPP & Semester SMA';
      $label_cbt     = 'CBT & UTBK SMA';
  }
?>
      <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand py-3">
          <a href="<?= base_url() ?>" class="brand-link d-flex align-items-center">
            <img
              src="<?= (isset($school_info['logo']) && strpos($school_info['logo'], 'http') === 0) ? $school_info['logo'] : base_url(isset($school_info['logo']) && $school_info['logo'] ? $school_info['logo'] : 'dist/assets/img/AdminLTELogo.png') ?>"
              alt="Logo"
              class="brand-image opacity-75 shadow rounded-circle"
            />
            <div class="d-flex flex-column lh-1">
              <span class="brand-text fw-bold text-white fs-6">ULTIMATE <span class="fw-normal">SCHOOL</span></span>
              <span class="badge <?= $badge_color ?> mt-1 me-auto fw-bold" style="font-size: 0.65rem;"><i class="bi bi-mortarboard-fill me-1"></i> <?= $jenjang ?></span>
            </div>
          </a>
        </div>
        <!--end::Sidebar Brand-->

        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2" aria-label="Main Navigation">
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="menu"
              data-accordion="false"
              id="navigation"
            >
              <!-- 1. UTAMA -->
              <li class="nav-header">UTAMA</li>
              <?php if (is_allowed_menu('dashboard', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('dashboard') ?>" class="nav-link <?= (empty($seg1) || $seg1 === 'dashboard') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>Dashboard <?= $jenjang ?></p>
                </a>
              </li>
              <?php endif; ?>

              <!-- 2. PUBLIC & PPDB -->
              <li class="nav-header">PUBLIC & PPDB</li>
              <?php if (is_allowed_menu('home_compro', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('home') ?>" target="_blank" class="nav-link">
                  <i class="nav-icon bi bi-globe text-info"></i>
                  <p>Website Compro <?= $jenjang ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('ppdb_online', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('ppdbadmin') ?>" class="nav-link <?= ($seg1 === 'ppdbadmin') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-person-plus-fill"></i>
                  <p><?= $label_ppdb ?></p>
                </a>
              </li>
              <?php endif; ?>

              <!-- 3. AKADEMIK & TU -->
              <?php if (is_allowed_menu('murid', $allowed, $user_role) || is_allowed_menu('guru', $allowed, $user_role) || is_allowed_menu('kelas', $allowed, $user_role) || is_allowed_menu('mapel', $allowed, $user_role) || is_allowed_menu('jadwal', $allowed, $user_role)): ?>
              <li class="nav-header">AKADEMIK & TU (EKOSISTEM <?= $jenjang ?>)</li>
              <?php if (is_allowed_menu('murid', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('murid') ?>" class="nav-link <?= ($seg1 === 'murid') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-people-fill"></i>
                  <p><?= $label_murid ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('guru', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('guru') ?>" class="nav-link <?= ($seg1 === 'guru') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-person-badge-fill"></i>
                  <p><?= $label_guru ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('kelas', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('kelas') ?>" class="nav-link <?= ($seg1 === 'kelas') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-door-open-fill"></i>
                  <p><?= $label_kelas ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('mapel', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('mapel') ?>" class="nav-link <?= ($seg1 === 'mapel' && empty($seg2)) ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-book-fill"></i>
                  <p>Kurikulum & Mapel <?= $jenjang ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('jadwal', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('mapel/jadwal') ?>" class="nav-link <?= ($seg1 === 'mapel' && $seg2 === 'jadwal') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-calendar3"></i>
                  <p>Jadwal Pelajaran <?= $jenjang ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php endif; ?>

              <!-- 4. PRESENSI & EVALUASI -->
              <?php if (is_allowed_menu('absensi', $allowed, $user_role) || is_allowed_menu('absensi_guru', $allowed, $user_role) || is_allowed_menu('tugas', $allowed, $user_role) || is_allowed_menu('nilai', $allowed, $user_role) || is_allowed_menu('raport', $allowed, $user_role) || is_allowed_menu('sertifikat', $allowed, $user_role)): ?>
              <li class="nav-header">PRESENSI & EVALUASI</li>
              <?php if (is_allowed_menu('absensi', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('absensi') ?>" class="nav-link <?= ($seg1 === 'absensi' && empty($seg2)) ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-calendar-check-fill text-success"></i>
                  <p>Presensi Siswa Harian</p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('absensi_guru', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('absensi/guru') ?>" class="nav-link <?= ($seg1 === 'absensi' && $seg2 === 'guru') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-person-check-fill text-warning"></i>
                  <p>Presensi Guru & Staf</p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('tugas', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('akademik/tugas') ?>" class="nav-link <?= ($seg1 === 'akademik' && $seg2 === 'tugas') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-file-earmark-text-fill"></i>
                  <p>Tugas & PR Murid</p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('nilai', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('akademik/nilai') ?>" class="nav-link <?= ($seg1 === 'akademik' && $seg2 === 'nilai') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-calculator-fill"></i>
                  <p>Pengolahan Nilai</p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('raport', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('akademik/raport') ?>" class="nav-link <?= ($seg1 === 'akademik' && $seg2 === 'raport') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-journal-bookmark-fill"></i>
                  <p><?= $label_raport ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('sertifikat', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('akademik/sertifikat') ?>" class="nav-link <?= ($seg1 === 'akademik' && $seg2 === 'sertifikat') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-award-fill"></i>
                  <p>Sertifikat & Ijazah</p>
                </a>
              </li>
              <?php endif; ?>
              <?php endif; ?>

              <!-- 5. CBT & UJIAN ONLINE -->
              <?php if (is_allowed_menu('banksoal', $allowed, $user_role) || is_allowed_menu('ujian', $allowed, $user_role) || is_allowed_menu('cbt_engine', $allowed, $user_role) || is_allowed_menu('kuislatihan', $allowed, $user_role)): ?>
              <li class="nav-header text-warning"><?= strtoupper($label_cbt) ?></li>
              <?php if (is_allowed_menu('banksoal', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('banksoal') ?>" class="nav-link <?= ($seg1 === 'banksoal') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-folder-symlink-fill text-warning"></i>
                  <p>Bank Soal & Quiz</p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('ujian', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('ujian') ?>" class="nav-link <?= ($seg1 === 'ujian') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-clock-history text-primary"></i>
                  <p>Sesi Ujian CBT <?= $jenjang ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('cbt_engine', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('cbt') ?>" class="nav-link <?= ($seg1 === 'cbt') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-laptop-fill text-success"></i>
                  <p>Safe Exam Engine CBT</p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('kuislatihan', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('kuislatihan') ?>" class="nav-link <?= ($seg1 === 'kuislatihan') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-ui-checks text-info"></i>
                  <p>Latihan Soal Kuis</p>
                </a>
              </li>
              <?php endif; ?>
              <?php endif; ?>

              <!-- 6. KEUANGAN & KESISWAAN -->
              <?php if (is_allowed_menu('tabungan', $allowed, $user_role) || is_allowed_menu('pembayaran', $allowed, $user_role) || is_allowed_menu('eskul', $allowed, $user_role) || is_allowed_menu('struktur', $allowed, $user_role)): ?>
              <li class="nav-header">KEUANGAN & KESISWAAN</li>
              <?php if (is_allowed_menu('tabungan', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('tabungan') ?>" class="nav-link <?= ($seg1 === 'tabungan') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-wallet2 text-success"></i>
                  <p>Tabungan Siswa <?= $jenjang ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('pembayaran', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('pembayaran') ?>" class="nav-link <?= ($seg1 === 'pembayaran') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-cash-stack text-danger"></i>
                  <p><?= $label_spp ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('eskul', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('eskul') ?>" class="nav-link <?= ($seg1 === 'eskul') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-trophy-fill text-warning"></i>
                  <p>Ekstrakurikuler (Eskul)</p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('struktur', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('struktur') ?>" class="nav-link <?= ($seg1 === 'struktur') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-diagram-3-fill"></i>
                  <p>Struktur Organisasi</p>
                </a>
              </li>
              <?php endif; ?>
              <?php endif; ?>

              <!-- 7. PENGATURAN SYSTEM -->
              <?php if (is_allowed_menu('sekolah', $allowed, $user_role) || is_allowed_menu('acl_management', $allowed, $user_role)): ?>
              <li class="nav-header">PENGATURAN SYSTEM</li>
              <li class="nav-item">
                <a href="<?= base_url('users') ?>" class="nav-link <?= ($seg1 === 'users') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-people-fill text-success"></i>
                  <p>Manajemen Users System</p>
                </a>
              </li>
              <?php if (is_allowed_menu('sekolah', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('sekolah') ?>" class="nav-link <?= ($seg1 === 'sekolah') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-gear-fill text-warning"></i>
                  <p>Konfigurasi Jenjang & Compro</p>
                </a>
              </li>
              <?php endif; ?>
              <?php if (is_allowed_menu('acl_management', $allowed, $user_role)): ?>
              <li class="nav-item">
                <a href="<?= base_url('acl') ?>" class="nav-link <?= ($seg1 === 'acl') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-shield-lock-fill text-info"></i>
                  <p>Manajemen ACL & Tree Role</p>
                </a>
              </li>
              <?php endif; ?>
              <?php endif; ?>

              <li class="nav-item mt-3 border-top pt-2">
                <a href="<?= base_url('auth/logout') ?>" class="nav-link text-danger">
                  <i class="nav-icon bi bi-box-arrow-right"></i>
                  <p>Logout / Keluar</p>
                </a>
              </li>
            </ul>
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->

<script>
document.addEventListener('DOMContentLoaded', function() {
  function scrollToActiveMenu() {
    var sidebarWrapper = document.querySelector('.sidebar-wrapper') || document.querySelector('.app-sidebar');
    var activeItem = document.querySelector('.sidebar-wrapper .nav-link.active') || document.querySelector('.app-sidebar .nav-link.active');
    
    if (sidebarWrapper && activeItem) {
      var parentTree = activeItem.closest('.nav-treeview');
      if (parentTree) {
        var parentNavItem = parentTree.closest('.nav-item');
        if (parentNavItem && !parentNavItem.classList.contains('menu-open')) {
          parentNavItem.classList.add('menu-open');
        }
      }

      var wrapperHeight = sidebarWrapper.clientHeight;
      var itemTop = activeItem.offsetTop;
      var itemHeight = activeItem.clientHeight;
      var targetScroll = itemTop - (wrapperHeight / 2) + (itemHeight / 2);

      sidebarWrapper.scrollTo({
        top: Math.max(0, targetScroll),
        behavior: 'smooth'
      });
    }
  }

  scrollToActiveMenu();
  setTimeout(scrollToActiveMenu, 150);
});
</script>
