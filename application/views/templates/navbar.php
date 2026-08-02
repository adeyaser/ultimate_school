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
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                <img
                  src="<?= base_url('dist/assets/img/avatar.png') ?>"
                  class="user-image rounded-circle shadow-sm me-2"
                  alt="User Image"
                  width="32"
                  height="32"
                />
                <span class="d-none d-md-inline fw-bold me-1"><?= isset($user['full_name']) ? $user['full_name'] : 'Administrator' ?></span>
                <span class="badge text-bg-primary text-capitalize fs-7"><?= isset($user['role']) ? str_replace('_', ' ', $user['role']) : 'Admin' ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 rounded-4 p-3">
                <!-- User image -->
                <li class="user-header text-bg-primary rounded-3 text-center p-3 mb-3">
                  <img
                    src="<?= base_url('dist/assets/img/avatar.png') ?>"
                    class="rounded-circle shadow mb-2"
                    alt="User Image"
                    width="64"
                    height="64"
                  />
                  <h6 class="fw-bold mb-0"><?= isset($user['full_name']) ? $user['full_name'] : 'Administrator' ?></h6>
                  <small class="opacity-90"><?= isset($user['email']) ? $user['email'] : 'admin@ultimateschool.com' ?></small>
                </li>
                <!-- Menu Footer-->
                <li class="d-grid gap-2">
                  <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger fw-bold rounded-pill">
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
