<?php
  $user_role = isset($user_data['role']) ? $user_data['role'] : 'super_admin';
  $is_admin_or_teacher = in_array($user_role, array('super_admin', 'admin', 'guru', 'wali_kelas', 'kepala_sekolah'));
  $jenjang = isset($school_info['jenjang']) ? $school_info['jenjang'] : 'SMP';
  $view_mode = isset($view_mode) ? $view_mode : 'detail';
  $selected_ta_name = isset($selected_ta['nama']) ? $selected_ta['nama'] : (isset($selected_ta['tahun_ajaran']) ? $selected_ta['tahun_ajaran'] : '-');
?>
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0 fw-bold">
            <i class="bi bi-journal-bookmark-fill text-primary me-2"></i> 
            <?= $is_admin_or_teacher ? 'Kelola Raport Pembelajaran Murid' : 'Raport Digital Saya (Portal Siswa)' ?>
          </h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Raport Digital</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-3" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= $this->session->flashdata('success') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Filter Selection Card -->
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-3">
          <form action="<?= base_url('akademik/raport') ?>" method="get" class="row g-3 align-items-end">
            
            <!-- Tahun Ajaran Filter -->
            <div class="col-md-<?= $is_admin_or_teacher ? '5' : '8' ?>">
              <label class="form-label fw-bold text-primary mb-1"><i class="bi bi-calendar-range-fill me-1"></i> Pilih Periode Tahun Ajaran & Semester *</label>
              <select name="tahun_ajaran_id" class="form-select fw-bold border-primary py-2" onchange="this.form.submit()">
                <?php foreach ($ta_list as $ta): ?>
                  <?php $ta_name = isset($ta['nama']) ? $ta['nama'] : (isset($ta['tahun_ajaran']) ? $ta['tahun_ajaran'] : '-'); ?>
                  <option value="<?= $ta['id'] ?>" <?= ($selected_ta_id == $ta['id']) ? 'selected' : '' ?>>
                    TA <?= $ta_name ?> - Semester <?= $ta['semester'] ?> <?= ($ta['is_active'] ? '(AKTIF)' : '') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <?php if ($is_admin_or_teacher): ?>
              <div class="col-md-4">
                <label class="form-label fw-bold text-primary mb-1"><i class="bi bi-door-open-fill me-1"></i> Pilih Kelas / Rombel *</label>
                <select name="kelas_id" class="form-select fw-bold border-primary py-2" onchange="this.form.submit()">
                  <option value="">-- Pilih Kelas --</option>
                  <?php foreach ($kelas_list as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($selected_kelas == $k['id']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-3">
                <button type="submit" class="btn btn-primary fw-bold w-100 py-2"><i class="bi bi-search me-1"></i> Tampilkan</button>
              </div>
            <?php else: ?>
              <div class="col-md-4">
                <button type="submit" class="btn btn-primary fw-bold w-100 py-2"><i class="bi bi-search me-1"></i> Tampilkan Raport</button>
              </div>
            <?php endif; ?>
          </form>
        </div>
      </div>

      <!-- ===================== -->
      <!-- ADMIN/GURU: TABLE VIEW (Ringkasan Semua Siswa) -->
      <!-- ===================== -->
      <?php if ($is_admin_or_teacher && $view_mode == 'table'): ?>

        <?php if (!empty($selected_kelas) && !empty($students_summary)): ?>
          <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
              <div>
                <h5 class="fw-bold text-primary mb-0"><i class="bi bi-table me-2"></i> Rekap Nilai Siswa Per Kelas</h5>
                <small class="text-muted">Tahun Ajaran <?= $selected_ta_name ?> - Semester <?= isset($selected_ta['semester']) ? $selected_ta['semester'] : '-' ?></small>
              </div>
              <span class="badge text-bg-secondary px-3 py-2 fs-6 rounded-pill">Total: <?= count($students_summary) ?> Siswa</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive p-3">
                <table id="tableRaportSummary" class="table table-hover table-striped align-middle w-100 m-0">
                  <thead class="table-dark">
                    <tr>
                      <th style="width: 45px;">No</th>
                      <th>Nama Siswa</th>
                      <th>NISN</th>
                      <th>Kelas</th>
                      <th class="text-center">Jml Mapel</th>
                      <th class="text-center">Rata² UH</th>
                      <th class="text-center">Rata² Tugas</th>
                      <th class="text-center">Rata² UTS</th>
                      <th class="text-center">Rata² UKK</th>
                      <th class="text-center">Rata² Akhir</th>
                      <th class="text-center">Status</th>
                      <th style="width: 200px;" class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 1; foreach ($students_summary as $s): ?>
                      <?php
                        $avg = $s['avg_akhir'];
                        if ($avg >= 90) $predikat_class = 'text-bg-success';
                        elseif ($avg >= 80) $predikat_class = 'text-bg-primary';
                        elseif ($avg >= 75) $predikat_class = 'text-bg-warning text-dark';
                        else $predikat_class = 'text-bg-danger';
                      ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><strong class="text-dark"><?= $s['full_name'] ?></strong></td>
                        <td><code><?= $s['nisn'] ?></code></td>
                        <td><span class="badge text-bg-info text-white"><?= $s['nama_kelas'] ?></span></td>
                        <td class="text-center"><?= $s['jml_mapel'] ?></td>
                        <td class="text-center fw-semibold"><?= $s['avg_uh'] > 0 ? $s['avg_uh'] : '-' ?></td>
                        <td class="text-center fw-semibold"><?= $s['avg_tugas'] > 0 ? $s['avg_tugas'] : '-' ?></td>
                        <td class="text-center fw-semibold"><?= $s['avg_uts'] > 0 ? $s['avg_uts'] : '-' ?></td>
                        <td class="text-center fw-semibold"><?= $s['avg_ukk'] > 0 ? $s['avg_ukk'] : '-' ?></td>
                        <td class="text-center"><span class="badge <?= $predikat_class ?> fs-6 px-3"><?= $s['avg_akhir'] > 0 ? $s['avg_akhir'] : '-' ?></span></td>
                        <td class="text-center">
                          <?php if ($s['raport_published']): ?>
                            <span class="badge text-bg-success rounded-pill px-2 py-1 me-1"><i class="bi bi-check-circle-fill"></i></span>
                          <?php endif; ?>
                        </td>
                        <td class="text-center">
                          <div class="btn-group" role="group">
                            <a href="<?= base_url('akademik/raport?murid_id=' . $s['id'] . '&kelas_id=' . $selected_kelas . '&tahun_ajaran_id=' . $selected_ta_id) ?>" class="btn btn-sm btn-info text-white">
                              <i class="bi bi-eye-fill me-1"></i> Lihat Raport
                            </a>
                            <a href="<?= base_url('akademik/cetak_raport/' . $s['id'] . '?tahun_ajaran_id=' . $selected_ta_id) ?>" target="_blank" class="btn btn-sm btn-danger">
                              <i class="bi bi-file-pdf-fill me-1"></i> PDF
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php elseif (!empty($selected_kelas)): ?>
          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body text-center py-5 text-muted">
              <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i> Belum ada siswa aktif di kelas ini atau belum ada nilai yang diinputkan.
            </div>
          </div>
        <?php else: ?>
          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body text-center py-5 text-muted">
              <i class="bi bi-arrow-up-circle fs-1 d-block mb-2 opacity-50"></i> Silakan pilih <strong>Kelas / Rombel</strong> di filter di atas untuk menampilkan rekap nilai siswa.
            </div>
          </div>
        <?php endif; ?>

      <?php endif; ?>

      <!-- ===================== -->
      <!-- DETAIL VIEW (Both roles: when specific student selected) -->
      <!-- ===================== -->
      <?php if ($view_mode == 'detail' && isset($murid) && $murid): ?>

        <?php if ($is_admin_or_teacher): ?>
          <div class="mb-3">
            <a href="<?= base_url('akademik/raport?kelas_id=' . $selected_kelas . '&tahun_ajaran_id=' . $selected_ta_id) ?>" class="btn btn-outline-secondary rounded-pill px-4">
              <i class="bi bi-arrow-left me-1"></i> Kembali ke Tabel Rekap Kelas
            </a>
          </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
          <!-- Card Header -->
          <div class="card-header bg-primary text-white p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
            <div>
              <span class="badge text-bg-warning text-dark mb-2 fw-bold px-3 py-1 rounded-pill">
                <i class="bi bi-journal-check me-1"></i> RAPORT DIGITAL <?= $jenjang ?> (TAHUN AJARAN <?= $selected_ta_name ?> - SEMESTER <?= isset($selected_ta['semester']) ? strtoupper($selected_ta['semester']) : '-' ?>)
              </span>
              <h3 class="fw-bold mb-1"><i class="bi bi-person-circle me-2"></i> <?= $murid['full_name'] ?></h3>
              <p class="mb-0 text-white-50 fs-6">
                NISN: <strong><?= $murid['nisn'] ?></strong> | NIS: <strong><?= $murid['nis'] ?></strong> | Kelas: <strong><?= isset($murid['nama_kelas']) ? $murid['nama_kelas'] : '-' ?></strong>
              </p>
            </div>

            <a href="<?= base_url('akademik/cetak_raport/' . $murid['id'] . '?tahun_ajaran_id=' . $selected_ta_id) ?>" target="_blank" class="btn btn-warning btn-lg fw-bold rounded-pill shadow px-4">
              <i class="bi bi-printer-fill me-2"></i> Cetak Raport Digital PDF
            </a>
          </div>

          <!-- Card Body: Grades Table -->
          <div class="card-body p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-award-fill me-2"></i> Transkrip Nilai Hasil Belajar Semester</h5>
            
            <div class="table-responsive mb-4">
              <table class="table table-hover table-bordered align-middle m-0">
                <thead class="table-dark">
                  <tr>
                    <th rowspan="2" style="width: 45px; vertical-align: middle;">No</th>
                    <th rowspan="2" style="vertical-align: middle;">Mata Pelajaran</th>
                    <th rowspan="2" style="width: 80px; vertical-align: middle;">Kelompok</th>
                    <th rowspan="2" style="width: 60px; vertical-align: middle;">KKM</th>
                    <th colspan="4" class="text-center">Komponen Penilaian</th>
                    <th rowspan="2" style="width: 90px; vertical-align: middle;" class="text-center">Nilai Akhir</th>
                    <th rowspan="2" style="width: 80px; vertical-align: middle;">Predikat</th>
                    <th rowspan="2" style="width: 120px; vertical-align: middle;">Ketuntasan</th>
                  </tr>
                  <tr>
                    <th style="width: 85px;" class="text-center bg-info text-white">Rata² UH<br><small>(20%)</small></th>
                    <th style="width: 85px;" class="text-center bg-warning text-dark">Rata² Tugas<br><small>(20%)</small></th>
                    <th style="width: 70px;" class="text-center bg-success text-white">UTS<br><small>(30%)</small></th>
                    <th style="width: 70px;" class="text-center bg-danger text-white">UKK<br><small>(30%)</small></th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($nilai_list)): ?>
                    <tr><td colspan="11" class="text-center py-4 text-muted"><i class="bi bi-journal-x fs-2 d-block mb-2 opacity-50"></i> Belum ada data nilai hasil belajar yang diinputkan untuk periode ini.</td></tr>
                  <?php else: ?>
                    <?php 
                      $no = 1; 
                      $total_uh = 0; $total_tugas = 0; $total_uts = 0; $total_ukk = 0; $total_akhir = 0;
                      $count = count($nilai_list);
                    ?>
                    <?php foreach ($nilai_list as $n): 
                      $uh    = isset($n['nilai_harian']) ? (float)$n['nilai_harian'] : 0;
                      $tugas = isset($n['nilai_tugas'])  ? (float)$n['nilai_tugas']  : 0;
                      $uts   = isset($n['nilai_pts'])    ? (float)$n['nilai_pts']    : 0;
                      $ukk   = isset($n['nilai_pas'])    ? (float)$n['nilai_pas']    : 0;
                      $akhir = isset($n['nilai_akhir'])  ? (float)$n['nilai_akhir']  : 0;
                      $total_uh += $uh; $total_tugas += $tugas; $total_uts += $uts; $total_ukk += $ukk; $total_akhir += $akhir;
                    ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td class="fw-bold text-primary fs-6"><?= $n['nama_mapel'] ?></td>
                        <td><span class="badge text-bg-secondary"><?= $n['kelompok'] ?></span></td>
                        <td><code><?= $n['kkm'] ?></code></td>
                        <td class="text-center fw-semibold"><?= $uh > 0 ? number_format($uh, 1) : '-' ?></td>
                        <td class="text-center fw-semibold"><?= $tugas > 0 ? number_format($tugas, 1) : '-' ?></td>
                        <td class="text-center fw-semibold"><?= $uts > 0 ? number_format($uts, 1) : '-' ?></td>
                        <td class="text-center fw-semibold"><?= $ukk > 0 ? number_format($ukk, 1) : '-' ?></td>
                        <td class="text-center"><strong class="fs-5 text-dark"><?= number_format($akhir, 1) ?></strong></td>
                        <td><span class="badge text-bg-primary fs-6 px-3"><?= $n['predikat'] ?></span></td>
                        <td>
                          <?php if ($n['is_tuntas']): ?>
                            <span class="badge text-bg-success px-3 py-1 fs-6"><i class="bi bi-check-circle-fill me-1"></i> TUNTAS</span>
                          <?php else: ?>
                            <span class="badge text-bg-danger px-3 py-1 fs-6"><i class="bi bi-x-circle-fill me-1"></i> BELUM TUNTAS</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    <tr class="table-warning fw-bold">
                      <td colspan="4" class="text-end">Rata-Rata Keseluruhan:</td>
                      <td class="text-center"><?= $count > 0 ? number_format($total_uh / $count, 1) : '-' ?></td>
                      <td class="text-center"><?= $count > 0 ? number_format($total_tugas / $count, 1) : '-' ?></td>
                      <td class="text-center"><?= $count > 0 ? number_format($total_uts / $count, 1) : '-' ?></td>
                      <td class="text-center"><?= $count > 0 ? number_format($total_ukk / $count, 1) : '-' ?></td>
                      <td class="text-center fs-5 text-primary"><?= $count > 0 ? number_format($total_akhir / $count, 1) : '-' ?></td>
                      <td colspan="2"></td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <div class="alert alert-info border-0 rounded-3 mb-4">
              <i class="bi bi-calculator-fill me-2"></i>
              <strong>Rumus Perhitungan Nilai Akhir:</strong> (Rata² UH × 20%) + (Rata² Tugas × 20%) + (UTS × 30%) + (UKK × 30%)
            </div>

            <div class="p-4 bg-light rounded-4 border-start border-4 border-success shadow-sm">
              <h6 class="fw-bold text-success mb-2"><i class="bi bi-chat-left-quote-fill me-2 fs-5"></i> Catatan & Preskripsi Wali Kelas:</h6>
              <p class="mb-0 fs-6 text-dark fw-semibold lh-base">
                <?= (isset($raport) && $raport && !empty($raport['catatan_wali_kelas'])) ? nl2br(htmlspecialchars($raport['catatan_wali_kelas'])) : 'Selamat atas pencapaian hasil belajar semester ini. Tingkatkan terus kedisiplinan dan keaktifan belajar!' ?>
              </p>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    if ($('#tableRaportSummary').length) {
      $('#tableRaportSummary').DataTable({
        language: {
          search: "Cari Siswa:",
          lengthMenu: "Tampilkan _MENU_ data per halaman",
          info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
          infoEmpty: "Tidak ada data siswa",
          infoFiltered: "(disaring dari _MAX_ total siswa)",
          zeroRecords: "Data siswa tidak ditemukan",
          paginate: {
            first: "Awal",
            last: "Akhir",
            next: "Selanjutnya",
            previous: "Sebelumnya"
          }
        },
        columnDefs: [
          { targets: [0, 10, 11], orderable: false }
        ]
      });
    }
  }
});
</script>
