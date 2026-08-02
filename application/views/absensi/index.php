<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0 fw-bold"><i class="bi bi-calendar-check-fill text-primary me-2"></i> Presensi Siswa Harian</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Presensi</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= $this->session->flashdata('success') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if (isset($is_student_view) && $is_student_view): ?>
        
        <!-- Student / Parent Isolated Attendance History View -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
          <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
              <small class="text-muted fw-bold d-block">REKAP PRESENSI SAYA</small>
              <h5 class="fw-bold text-primary mb-0"><i class="bi bi-person-fill text-success me-1"></i> <?= $user_data['full_name'] ?> (NISN: <?= isset($my_student['nisn']) ? $my_student['nisn'] : '-' ?>)</h5>
            </div>
            <span class="badge text-bg-success px-3 py-2 fs-6 rounded-pill"><i class="bi bi-shield-lock-fill me-1"></i> Data Terisolasi Siswa</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle m-0">
                <thead class="table-dark">
                  <tr>
                    <th style="width: 50px;">No</th>
                    <th>Tanggal Presensi</th>
                    <th>Kelas</th>
                    <th>Jam Datang</th>
                    <th>Status Kehadiran</th>
                    <th>Keterangan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($self_absensi)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i> Belum ada catatan presensi terrekam untuk Anda.</td></tr>
                  <?php else: ?>
                    <?php $no = 1; foreach ($self_absensi as $sa): 
                      $st_badge = ($sa['status'] === 'Hadir') ? 'text-bg-success' : (($sa['status'] === 'Izin') ? 'text-bg-warning text-dark' : (($sa['status'] === 'Sakit') ? 'text-bg-info text-white' : 'text-bg-danger'));
                    ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td class="fw-bold text-dark"><?= date('d M Y', strtotime($sa['tanggal'])) ?></td>
                        <td><span class="badge text-bg-secondary"><?= $sa['nama_kelas'] ? $sa['nama_kelas'] : '-' ?></span></td>
                        <td><code><?= $sa['jam_datang'] ? date('H:i', strtotime($sa['jam_datang'])) : '-' ?></code></td>
                        <td><span class="badge <?= $st_badge ?> px-3 py-1 fs-6"><?= $sa['status'] ?></span></td>
                        <td><?= $sa['keterangan'] ? htmlspecialchars($sa['keterangan']) : '-' ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      <?php else: ?>

        <!-- Admin / Teacher Class Attendance View -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
          <div class="card-body p-3">
            <form action="<?= base_url('absensi') ?>" method="get" class="row g-3 align-items-end">
              <div class="col-md-5">
                <label class="form-label fw-bold text-primary">Pilih Kelas Target *</label>
                <select name="kelas_id" class="form-select fw-bold border-primary" required>
                  <option value="">-- Pilih Kelas --</option>
                  <?php foreach ($kelas_list as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($selected_kelas == $k['id']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold text-primary">Tanggal Presensi *</label>
                <input type="date" name="tanggal" class="form-control fw-bold border-primary" value="<?= $tanggal ?>" required />
              </div>
              <div class="col-md-3">
                <button type="submit" class="btn btn-primary fw-bold w-100"><i class="bi bi-search me-1"></i> Muat Siswa</button>
              </div>
            </form>
          </div>
        </div>

        <?php if ($selected_kelas): ?>
          <form action="<?= base_url('absensi/simpan') ?>" method="post">
            <input type="hidden" name="kelas_id" value="<?= $selected_kelas ?>" />
            <input type="hidden" name="tanggal" value="<?= $tanggal ?>" />

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
              <div class="card-header border-bottom bg-white d-flex justify-content-between align-items-center p-3">
                <h5 class="fw-bold text-primary mb-0"><i class="bi bi-calendar-check-fill me-2"></i> Lembar Presensi Kehadiran Siswa</h5>
                <button type="submit" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm"><i class="bi bi-save-fill me-1"></i> Simpan Presensi</button>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle m-0">
                    <thead class="table-light">
                      <tr>
                        <th width="5%">No</th>
                        <th>NISN / NIS</th>
                        <th>Nama Siswa</th>
                        <th width="40%">Status Kehadiran</th>
                        <th>Keterangan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($murid_list)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data siswa di kelas ini.</td></tr>
                      <?php else: ?>
                        <?php $no = 1; foreach ($murid_list as $m): ?>
                          <tr>
                            <td><?= $no++ ?></td>
                            <td><code><?= $m['nisn'] ?> / <?= $m['nis'] ?></code></td>
                            <td class="fw-bold text-primary"><?= $m['full_name'] ?></td>
                            <td>
                              <?php $st = isset($m['status']) ? $m['status'] : 'Hadir'; ?>
                              <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="status[<?= $m['murid_id'] ?>]" id="h_<?= $m['murid_id'] ?>" value="Hadir" <?= ($st == 'Hadir') ? 'checked' : '' ?>>
                                <label class="btn btn-outline-success btn-sm" for="h_<?= $m['murid_id'] ?>">Hadir</label>

                                <input type="radio" class="btn-check" name="status[<?= $m['murid_id'] ?>]" id="i_<?= $m['murid_id'] ?>" value="Izin" <?= ($st == 'Izin') ? 'checked' : '' ?>>
                                <label class="btn btn-outline-warning btn-sm" for="i_<?= $m['murid_id'] ?>">Izin</label>

                                <input type="radio" class="btn-check" name="status[<?= $m['murid_id'] ?>]" id="s_<?= $m['murid_id'] ?>" value="Sakit" <?= ($st == 'Sakit') ? 'checked' : '' ?>>
                                <label class="btn btn-outline-info btn-sm" for="s_<?= $m['murid_id'] ?>">Sakit</label>

                                <input type="radio" class="btn-check" name="status[<?= $m['murid_id'] ?>]" id="a_<?= $m['murid_id'] ?>" value="Alpa" <?= ($st == 'Alpa') ? 'checked' : '' ?>>
                                <label class="btn btn-outline-danger btn-sm" for="a_<?= $m['murid_id'] ?>">Alpa</label>
                              </div>
                            </td>
                            <td>
                              <input type="text" name="keterangan[<?= $m['murid_id'] ?>]" class="form-control form-control-sm" value="<?= isset($m['keterangan']) ? htmlspecialchars($m['keterangan']) : '' ?>" placeholder="Catatan opsional..." />
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </form>
        <?php endif; ?>

      <?php endif; ?>

    </div>
  </div>
</main>
