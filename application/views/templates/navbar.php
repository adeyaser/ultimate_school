      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body shadow-sm">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav align-items-center">
            <li class="nav-item">
              <a
                class="nav-link fs-4 me-2"
                data-lte-toggle="sidebar"
                href="#"
                role="button"
                aria-label="Toggle sidebar"
              >
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block me-3">
              <a href="<?= base_url('dashboard') ?>" class="nav-link fw-semibold">
                <i class="bi bi-speedometer2 text-primary me-1"></i> Dashboard
              </a>
            </li>
            <li class="nav-item d-none d-md-block me-3">
              <a href="<?= base_url('home') ?>" target="_blank" class="nav-link fw-semibold">
                <i class="bi bi-globe text-success me-1"></i> Website Utama
              </a>
            </li>
            <li class="nav-item dropdown d-none d-lg-block me-2">
              <?php 
                $cur_j = isset($active_jenjang) ? $active_jenjang : (isset($school_info['jenjang']) ? $school_info['jenjang'] : 'SMP');
                $badge_class = ($cur_j === 'SD') ? 'btn-success' : (($cur_j === 'SMP') ? 'btn-info text-white' : (($cur_j === 'SMA') ? 'btn-warning text-dark' : 'btn-primary'));
              ?>
              <button class="btn btn-sm <?= $badge_class ?> dropdown-toggle fw-bold shadow-sm px-3 py-1" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-mortarboard-fill me-1"></i> MODE: <?= $cur_j ?>
              </button>
              <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                <li><h6 class="dropdown-header text-primary fw-bold"><i class="bi bi-arrow-repeat me-1"></i> Kelola Sekolah (Switch Mode):</h6></li>
                <li><a class="dropdown-item fw-semibold <?= ($cur_j === 'SD') ? 'active' : '' ?>" href="<?= base_url('sekolah/switch_jenjang/SD') ?>"><i class="bi bi-bank me-2 text-success"></i> Mode SD (Sekolah Dasar)</a></li>
                <li><a class="dropdown-item fw-semibold <?= ($cur_j === 'SMP') ? 'active' : '' ?>" href="<?= base_url('sekolah/switch_jenjang/SMP') ?>"><i class="bi bi-bank2 me-2 text-info"></i> Mode SMP (Menengah Pertama)</a></li>
                <li><a class="dropdown-item fw-semibold <?= ($cur_j === 'SMA') ? 'active' : '' ?>" href="<?= base_url('sekolah/switch_jenjang/SMA') ?>"><i class="bi bi-building me-2 text-warning"></i> Mode SMA (Menengah Atas)</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item fw-semibold <?= ($cur_j === 'ALL') ? 'active' : '' ?>" href="<?= base_url('sekolah/switch_jenjang/ALL') ?>"><i class="bi bi-globe me-2 text-primary"></i> Tampilkan Semua Jenjang</a></li>
              </ul>
            </li>
            <li class="nav-item d-none d-lg-block">
              <span class="badge text-bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold shadow-sm">
                <i class="bi bi-calendar-event me-1"></i> T.A. <?= isset($active_ta['nama']) ? $active_ta['nama'] : '2026/2027' ?> (<?= isset($active_ta['semester']) ? $active_ta['semester'] : 'Ganjil' ?>)
              </span>
            </li>
          </ul>
          <!--end::Start Navbar Links-->

          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto align-items-center gap-2">
            <!-- Notifications Dropdown -->
            <li class="nav-item dropdown">
              <a
                class="nav-link position-relative"
                data-bs-toggle="dropdown"
                href="#"
                aria-label="Notifikasi"
              >
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                  <span class="visually-hidden">Notifikasi</span>
                </span>
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 rounded-3">
                <span class="dropdown-item dropdown-header fw-bold">Pemberitahuan Sistem</span>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('ppdbadmin') ?>" class="dropdown-item py-2">
                  <i class="bi bi-person-plus-fill me-2 text-primary"></i> Pendaftaran PPDB Masuk
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('cbt') ?>" class="dropdown-item py-2">
                  <i class="bi bi-laptop-fill me-2 text-success"></i> Sesi Ujian CBT Aktif
                </a>
              </div>
            </li>

            <!-- User Menu Dropdown-->
            <?php
              $u_info     = !empty($user_data) ? $user_data : array();
              $u_name     = !empty($u_info['full_name']) ? $u_info['full_name'] : ($this->session->userdata('full_name') ? $this->session->userdata('full_name') : 'Administrator');
              $u_email    = !empty($u_info['email']) ? $u_info['email'] : ($this->session->userdata('email') ? $this->session->userdata('email') : 'admin@ultimateschool.com');
              $u_role     = !empty($u_info['role']) ? $u_info['role'] : ($this->session->userdata('role') ? $this->session->userdata('role') : 'super_admin');
              $u_photo    = (!empty($u_info['photo']) && file_exists(FCPATH . $u_info['photo'])) ? base_url($u_info['photo']) : base_url('dist/assets/img/avatar.png');

              $role_map = array(
                  'super_admin'    => array('label' => 'Super Admin',    'badge' => 'text-bg-danger'),
                  'admin'          => array('label' => 'Admin Sekolah',  'badge' => 'text-bg-primary'),
                  'kepala_sekolah' => array('label' => 'Kepala Sekolah', 'badge' => 'text-bg-dark'),
                  'guru'           => array('label' => 'Guru',           'badge' => 'text-bg-success'),
                  'wali_kelas'     => array('label' => 'Wali Kelas',     'badge' => 'text-bg-info text-white'),
                  'murid'          => array('label' => 'Siswa / Murid',  'badge' => 'text-bg-warning text-dark'),
                  'orang_tua'      => array('label' => 'Orang Tua',      'badge' => 'text-bg-secondary')
              );

              $r_label = isset($role_map[$u_role]) ? $role_map[$u_role]['label'] : ucwords(str_replace('_', ' ', $u_role));
              $r_badge = isset($role_map[$u_role]) ? $role_map[$u_role]['badge'] : 'text-bg-primary';
            ?>
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                <img
                  src="<?= $u_photo ?>"
                  class="user-image rounded-circle shadow-sm me-2"
                  alt="User Image"
                  width="32"
                  height="32"
                />
                <span class="d-none d-md-inline fw-bold me-2 text-dark"><?= htmlspecialchars($u_name) ?></span>
                <span class="badge <?= $r_badge ?> fs-7 px-2 py-1"><?= htmlspecialchars($r_label) ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 rounded-4 p-3">
                <!-- User image -->
                <li class="user-header text-bg-primary rounded-3 text-center p-3 mb-3">
                  <img
                    src="<?= $u_photo ?>"
                    class="rounded-circle shadow mb-2"
                    alt="User Image"
                    width="64"
                    height="64"
                  />
                  <h6 class="fw-bold mb-1"><?= htmlspecialchars($u_name) ?></h6>
                  <small class="opacity-90 d-block mb-2"><?= htmlspecialchars($u_email) ?></small>
                  <span class="badge text-bg-light text-primary fw-bold px-3 py-1"><?= htmlspecialchars($r_label) ?></span>
                </li>
                <!-- Menu Footer-->
                <li class="d-grid gap-2">
                  <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger fw-bold rounded-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout / Keluar
                  </a>
                </li>
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
