<?php
  $user_role = isset($user_data['role']) ? $user_data['role'] : 'super_admin';
  $jenjang   = isset($school_info['jenjang']) ? $school_info['jenjang'] : 'SMP';
?>
<main class="app-main">
  <!-- Header -->
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0 fw-bold">
            <i class="bi bi-speedometer2 text-primary me-2"></i> Dashboard Utama (Mode <?= $jenjang ?>)
          </h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <!-- ========================================== -->
      <!-- 1. DASHBOARD KHUSUS MURID / SISWA -->
      <!-- ========================================== -->
      <?php if ($user_role === 'murid'): ?>
        
        <!-- Student Welcome Hero Card -->
        <div class="card shadow-sm border-0 rounded-4 bg-primary text-white p-4 mb-4" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
          <div class="d-flex flex-column flex-md-row align-items-center gap-3">
            <?php 
              $m_photo = (!empty($murid['photo']) && file_exists(FCPATH . $murid['photo'])) ? base_url($murid['photo']) : base_url('dist/assets/img/avatar.png');
            ?>
            <img src="<?= $m_photo ?>" width="90" height="90" class="rounded-circle border border-3 border-white shadow" style="object-fit: cover;" alt="Foto Siswa" />
            <div>
              <span class="badge text-bg-warning text-dark mb-1 fw-bold px-3 py-1 rounded-pill"><i class="bi bi-mortarboard-fill me-1"></i> PORTAL SISWA <?= $jenjang ?></span>
              <h2 class="fw-bold mb-1">Selamat Datang, <?= $user_data['full_name'] ?>!</h2>
              <p class="mb-0 text-white-50 fs-6">
                Kelas: <strong><?= isset($murid['nama_kelas']) ? $murid['nama_kelas'] : '-' ?></strong> | NISN: <strong><?= isset($murid['nisn']) ? $murid['nisn'] : '-' ?></strong> | Status: <span class="badge text-bg-success">Aktif</span>
              </p>
            </div>
          </div>
        </div>

        <!-- Student Stat Widgets -->
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white border-start border-4 border-primary">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <small class="text-muted fw-semibold">Rombel Kelas</small>
                  <h4 class="fw-bold text-primary mb-0"><?= isset($murid['nama_kelas']) ? $murid['nama_kelas'] : '-' ?></h4>
                </div>
                <i class="bi bi-door-open-fill text-primary fs-1 opacity-75"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white border-start border-4 border-success">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <small class="text-muted fw-semibold">Saldo Tabungan</small>
                  <h4 class="fw-bold text-success mb-0">Rp <?= number_format(isset($saldo_tabungan) ? $saldo_tabungan : 0, 0, ',', '.') ?></h4>
                </div>
                <i class="bi bi-wallet2 text-success fs-1 opacity-75"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white border-start border-4 border-info">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <small class="text-muted fw-semibold">SPP Bulan Ini</small>
                  <h4 class="fw-bold text-info mb-0"><?= isset($status_spp) ? $status_spp : 'Lunas' ?></h4>
                </div>
                <i class="bi bi-cash-stack text-info fs-1 opacity-75"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white border-start border-4 border-warning">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <small class="text-muted fw-semibold">Status Kehadiran</small>
                  <h4 class="fw-bold text-warning mb-0">98% Hadir</h4>
                </div>
                <i class="bi bi-calendar-check-fill text-warning fs-1 opacity-75"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Student Main Content Cards -->
        <div class="row g-4 mb-4">
          <!-- Jadwal Pelajaran Hari Ini -->
          <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-header bg-white p-3 fw-bold text-primary border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar3 me-2"></i> Jadwal Pelajaran Hari Ini</span>
                <a href="<?= base_url('mapel/jadwal') ?>" class="btn btn-sm btn-outline-primary rounded-pill fw-bold">Semua Jadwal</a>
              </div>
              <div class="card-body p-0">
                <table class="table table-hover align-middle m-0">
                  <thead class="table-light">
                    <tr>
                      <th>Jam</th>
                      <th>Mata Pelajaran</th>
                      <th>Pengajar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($jadwal_today)): ?>
                      <tr><td colspan="3" class="text-center py-3 text-muted">Tidak ada jadwal pelajaran untuk hari ini.</td></tr>
                    <?php else: ?>
                      <?php foreach ($jadwal_today as $j): ?>
                        <tr>
                          <td><span class="badge text-bg-dark"><?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></span></td>
                          <td class="fw-bold text-primary"><?= $j['nama_mapel'] ?></td>
                          <td><?= $j['nama_guru'] ? $j['nama_guru'] : '-' ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Ujian CBT & Tugas Aktif -->
          <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
              <div class="card-header bg-white p-3 fw-bold text-success border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-laptop-fill me-2"></i> Ujian CBT Aktif (Siap Dikerjakan)</span>
                <a href="<?= base_url('cbt') ?>" class="btn btn-sm btn-success rounded-pill fw-bold">Buka CBT Engine</a>
              </div>
              <div class="card-body p-3">
                <?php if (empty($ujian_aktif)): ?>
                  <p class="text-muted text-center m-0 py-2">Belum ada jadwal ujian CBT aktif saat ini.</p>
                <?php else: ?>
                  <?php foreach ($ujian_aktif as $u): ?>
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                      <div>
                        <strong class="text-dark"><?= $u['judul_ujian'] ?></strong>
                        <br><small class="text-muted"><?= $u['nama_mapel'] ?> | Durasi: <?= $u['durasi'] ?> Menit</small>
                      </div>
                      <a href="<?= base_url('cbt/lembar_ujian/' . $u['id']) ?>" class="btn btn-sm btn-outline-success fw-bold rounded-pill">Kerjakan Ujian</a>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>

            <!-- Tugas / PR Pending -->
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white p-3 fw-bold text-warning border-bottom">
                <i class="bi bi-file-earmark-text-fill me-2"></i> Tugas & PR Murid Terbaru
              </div>
              <div class="card-body p-3">
                <?php if (empty($tugas_list)): ?>
                  <p class="text-muted text-center m-0 py-2">Tidak ada tugas baru yang perlu dikumpulkan.</p>
                <?php else: ?>
                  <?php foreach ($tugas_list as $t): ?>
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                      <div>
                        <strong class="text-dark"><?= $t['judul'] ?></strong>
                        <br><small class="text-muted"><?= $t['nama_mapel'] ?> | Deadline: <?= date('d M Y', strtotime($t['deadline'])) ?></small>
                      </div>
                      <a href="<?= base_url('akademik/tugas') ?>" class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill">Upload Tugas</a>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

      <!-- ========================================== -->
      <!-- 2. DASHBOARD KHUSUS GURU & WALI KELAS -->
      <!-- ========================================== -->
      <?php elseif ($user_role === 'guru' || $user_role === 'wali_kelas'): ?>

        <!-- Teacher Welcome Hero Card -->
        <div class="card shadow-sm border-0 rounded-4 bg-dark text-white p-4 mb-4" style="background: linear-gradient(135deg, #212529 0%, #343a40 100%);">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-primary rounded-circle p-3 text-white fs-2 shadow"><i class="bi bi-person-badge-fill"></i></div>
            <div>
              <span class="badge text-bg-info text-white mb-1 fw-bold px-3 py-1 rounded-pill"><i class="bi bi-person-fill-check me-1"></i> DASHBOARD GURU & TENAGA PENDIDIK</span>
              <h2 class="fw-bold mb-1">Selamat Datang, <?= $user_data['full_name'] ?>!</h2>
              <p class="mb-0 text-white-50">NIP: <strong><?= isset($guru['nip']) ? $guru['nip'] : '-' ?></strong> | Pendidikan: <strong><?= isset($guru['pendidikan_terakhir']) ? $guru['pendidikan_terakhir'] : '-' ?></strong></p>
            </div>
          </div>
        </div>

        <!-- Teacher Stat Widgets -->
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white border-start border-4 border-primary">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <small class="text-muted fw-semibold">Tugas Diberikan</small>
                  <h4 class="fw-bold text-primary mb-0"><?= isset($total_tugas) ? $total_tugas : 0 ?> Tugas</h4>
                </div>
                <i class="bi bi-file-earmark-text-fill text-primary fs-1 opacity-75"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white border-start border-4 border-success">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <small class="text-muted fw-semibold">Sesi Ujian CBT</small>
                  <h4 class="fw-bold text-success mb-0"><?= isset($total_ujian) ? $total_ujian : 0 ?> Ujian</h4>
                </div>
                <i class="bi bi-laptop-fill text-success fs-1 opacity-75"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white border-start border-4 border-warning">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <small class="text-muted fw-semibold">Presensi Harian Guru</small>
                  <h4 class="fw-bold text-warning mb-0">Hadir (07:30)</h4>
                </div>
                <i class="bi bi-calendar-check-fill text-warning fs-1 opacity-75"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white border-start border-4 border-info">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <small class="text-muted fw-semibold">Kelas Binaan</small>
                  <h4 class="fw-bold text-info mb-0"><?= count(isset($my_classes) ? $my_classes : array()) ?> Kelas</h4>
                </div>
                <i class="bi bi-door-open-fill text-info fs-1 opacity-75"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Teacher Main Content -->
        <div class="row g-4 mb-4">
          <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white p-3 fw-bold text-primary border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar3 me-2"></i> <?= (!empty($is_fallback_schedule)) ? 'Jadwal Mengajar Minggu Ini' : 'Jadwal Mengajar Hari Ini' ?></span>
                <a href="<?= base_url('mapel/jadwal') ?>" class="btn btn-sm btn-outline-primary fw-bold">Semua Jadwal</a>
              </div>
              <div class="card-body p-0">
                <table class="table table-hover align-middle m-0">
                  <thead class="table-light">
                    <tr>
                      <?php if (!empty($is_fallback_schedule)): ?>
                        <th>Hari</th>
                      <?php endif; ?>
                      <th>Jam</th>
                      <th>Kelas</th>
                      <th>Mata Pelajaran</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($jadwal_mengajar)): ?>
                      <tr><td colspan="<?= (!empty($is_fallback_schedule)) ? '4' : '3' ?>" class="text-center py-4 text-muted">Belum ada jadwal mengajar yang terdaftar.</td></tr>
                    <?php else: ?>
                      <?php foreach ($jadwal_mengajar as $jm): ?>
                        <tr>
                          <?php if (!empty($is_fallback_schedule)): ?>
                            <td><span class="badge text-bg-primary px-2 py-1"><?= $jm['hari'] ?></span></td>
                          <?php endif; ?>
                          <td><span class="badge text-bg-dark"><?= date('H:i', strtotime($jm['jam_mulai'])) ?> - <?= date('H:i', strtotime($jm['jam_selesai'])) ?></span></td>
                          <td><span class="badge text-bg-info text-white"><?= $jm['nama_kelas'] ?></span></td>
                          <td class="fw-bold text-primary"><?= $jm['nama_mapel'] ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white p-3 fw-bold text-success border-bottom">
                <i class="bi bi-lightning-charge-fill me-2"></i> Akses Cepat Guru
              </div>
              <div class="card-body p-3">
                <div class="d-grid gap-2">
                  <a href="<?= base_url('absensi') ?>" class="btn btn-outline-primary fw-bold text-start p-3 rounded-3">
                    <i class="bi bi-calendar-check-fill me-2 fs-5"></i> Input Presensi Siswa
                  </a>
                  <a href="<?= base_url('akademik/nilai') ?>" class="btn btn-outline-success fw-bold text-start p-3 rounded-3">
                    <i class="bi bi-calculator-fill me-2 fs-5"></i> Input & Olah Nilai Murid
                  </a>
                  <a href="<?= base_url('banksoal') ?>" class="btn btn-outline-warning text-dark fw-bold text-start p-3 rounded-3">
                    <i class="bi bi-folder-symlink-fill me-2 fs-5"></i> Kelola Bank Soal CBT
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

      <!-- ========================================== -->
      <!-- 3. DASHBOARD KHUSUS SUPER ADMIN & ADMIN -->
      <!-- ========================================== -->
      <?php else: ?>

        <!-- Stat Boxes -->
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary shadow-sm rounded-4 p-3">
              <div class="inner">
                <h3 class="fw-bold"><?= isset($total_siswa) ? $total_siswa : 0 ?></h3>
                <p class="mb-0">Total Siswa Aktif</p>
              </div>
              <i class="small-box-icon bi bi-people-fill opacity-50"></i>
              <a href="<?= base_url('murid') ?>" class="small-box-footer link-light link-underline-opacity-0">Data Murid <i class="bi bi-arrow-right-short"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success shadow-sm rounded-4 p-3">
              <div class="inner">
                <h3 class="fw-bold"><?= isset($total_guru) ? $total_guru : 0 ?></h3>
                <p class="mb-0">Guru & Tenaga Pendidik</p>
              </div>
              <i class="small-box-icon bi bi-person-badge-fill opacity-50"></i>
              <a href="<?= base_url('guru') ?>" class="small-box-footer link-light link-underline-opacity-0">Data Guru <i class="bi bi-arrow-right-short"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning text-dark shadow-sm rounded-4 p-3">
              <div class="inner">
                <h3 class="fw-bold"><?= isset($total_kelas) ? $total_kelas : 0 ?></h3>
                <p class="mb-0">Rombongan Belajar</p>
              </div>
              <i class="small-box-icon bi bi-door-open-fill opacity-50"></i>
              <a href="<?= base_url('kelas') ?>" class="small-box-footer link-dark link-underline-opacity-0">Data Kelas <i class="bi bi-arrow-right-short"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-danger shadow-sm rounded-4 p-3">
              <div class="inner">
                <h3 class="fw-bold"><?= isset($total_ppdb) ? $total_ppdb : 0 ?></h3>
                <p class="mb-0">Pendaftar PPDB Online</p>
              </div>
              <i class="small-box-icon bi bi-person-plus-fill opacity-50"></i>
              <a href="<?= base_url('ppdbadmin') ?>" class="small-box-footer link-light link-underline-opacity-0">PPDB Online <i class="bi bi-arrow-right-short"></i></a>
            </div>
          </div>
        </div>

        <!-- Recent Registrations & Quick Admin Actions -->
        <div class="row g-4 mb-4">
          <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white p-3 fw-bold text-primary border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i> Pendaftaran Siswa Terbaru</span>
                <a href="<?= base_url('murid') ?>" class="btn btn-sm btn-primary rounded-pill fw-bold">Kelola Murid</a>
              </div>
              <div class="card-body p-0">
                <table class="table table-hover align-middle m-0">
                  <thead class="table-light">
                    <tr>
                      <th>NISN / NIS</th>
                      <th>Nama Murid</th>
                      <th>Kelas</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($recent_students)): ?>
                      <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada murid terdaftar.</td></tr>
                    <?php else: ?>
                      <?php foreach ($recent_students as $rs): ?>
                        <tr>
                          <td><span class="badge text-bg-dark"><?= $rs['nisn'] ?></span></td>
                          <td class="fw-bold text-primary"><?= $rs['full_name'] ?></td>
                          <td><?= $rs['nama_kelas'] ?></td>
                          <td><span class="badge text-bg-success"><?= $rs['status_murid'] ?></span></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white p-3 fw-bold text-dark border-bottom">
                <i class="bi bi-gear-fill me-2 text-warning"></i> Kontrol Sistem & Jenjang
              </div>
              <div class="card-body p-3">
                <div class="d-grid gap-2">
                  <a href="<?= base_url('sekolah') ?>" class="btn btn-outline-primary fw-bold text-start p-3 rounded-3">
                    <i class="bi bi-mortarboard-fill me-2 fs-5"></i> Set Jenjang (SD/SMP/SMA/SMK)
                  </a>
                  <a href="<?= base_url('acl') ?>" class="btn btn-outline-info fw-bold text-start p-3 rounded-3">
                    <i class="bi bi-shield-lock-fill me-2 fs-5"></i> Konfigurasi ACL & Tree Role
                  </a>
                  <a href="<?= base_url('pembayaran') ?>" class="btn btn-outline-success fw-bold text-start p-3 rounded-3">
                    <i class="bi bi-cash-stack me-2 fs-5"></i> Kelola Pembayaran SPP
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php endif; ?>

    </div>
  </div>
</main>
