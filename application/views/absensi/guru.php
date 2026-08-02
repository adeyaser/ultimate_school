<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0 fw-bold"><i class="bi bi-person-badge-fill text-primary me-2"></i> Presensi & Absensi Guru & Staf</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Absensi Guru</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= $this->session->flashdata('success') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Filter Presensi -->
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
          <form action="<?= base_url('absensi/guru') ?>" method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label fw-bold text-primary"><i class="bi bi-calendar-event me-1"></i> Pilih Tanggal Presensi *</label>
              <input type="date" name="tanggal" class="form-control fw-bold border-primary" value="<?= $tanggal ?>" required />
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary fw-bold w-100"><i class="bi bi-filter me-1"></i> Tampilkan Presensi</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Form & Table Presensi Guru -->
      <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-3 fw-bold text-primary border-bottom d-flex justify-content-between align-items-center">
          <span><i class="bi bi-clipboard-check-fill me-2"></i> Daftar Kehadiran Guru (Tanggal: <?= date('d M Y', strtotime($tanggal)) ?>)</span>
          <span class="badge text-bg-primary fs-6"><?= count($guru_list) ?> Guru Terdaftar</span>
        </div>
        
        <form action="<?= base_url('absensi/simpan_guru') ?>" method="post">
          <input type="hidden" name="tanggal" value="<?= $tanggal ?>" />
          
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                  <tr>
                    <th style="width: 40px;">No</th>
                    <th>NIP / NIK</th>
                    <th>Nama Guru & Staf</th>
                    <th>Status Kepegawaian</th>
                    <th>Jam Datang</th>
                    <th>Jam Pulang</th>
                    <th style="width: 220px;">Status Kehadiran</th>
                    <th>Keterangan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($guru_list)): ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data guru terdaftar di database.</td></tr>
                  <?php else: ?>
                    <?php $no = 1; foreach ($guru_list as $g): 
                      $current_status = $g['status'] ? $g['status'] : 'Hadir';
                      $jam_datang = $g['jam_datang'] ? $g['jam_datang'] : '07:30';
                      $jam_pulang = $g['jam_pulang'] ? $g['jam_pulang'] : '15:00';
                    ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><code><?= $g['nip'] ?></code></td>
                        <td class="fw-bold text-primary">
                          <i class="bi bi-person-circle me-1"></i> <?= $g['full_name'] ?>
                        </td>
                        <td><span class="badge text-bg-secondary"><?= $g['status_kepegawaian'] ?></span></td>
                        <td>
                          <input type="time" name="jam_datang[<?= $g['guru_id'] ?>]" class="form-control form-control-sm" value="<?= date('H:i', strtotime($jam_datang)) ?>" />
                        </td>
                        <td>
                          <input type="time" name="jam_pulang[<?= $g['guru_id'] ?>]" class="form-control form-control-sm" value="<?= date('H:i', strtotime($jam_pulang)) ?>" />
                        </td>
                        <td>
                          <select name="status[<?= $g['guru_id'] ?>]" class="form-select form-select-sm fw-bold">
                            <option value="Hadir" <?= ($current_status === 'Hadir') ? 'selected' : '' ?>>🟢 Hadir</option>
                            <option value="Izin" <?= ($current_status === 'Izin') ? 'selected' : '' ?>>🟡 Izin</option>
                            <option value="Sakit" <?= ($current_status === 'Sakit') ? 'selected' : '' ?>>🔵 Sakit</option>
                            <option value="Alpa" <?= ($current_status === 'Alpa') ? 'selected' : '' ?>>🔴 Alpa / Tanpa Ket</option>
                          </select>
                        </td>
                        <td>
                          <input type="text" name="keterangan[<?= $g['guru_id'] ?>]" class="form-control form-control-sm" placeholder="Catatan jam mengajar / dispensasi..." value="<?= htmlspecialchars($g['keterangan'] ? $g['keterangan'] : '') ?>" />
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          
          <?php if (!empty($guru_list)): ?>
            <div class="card-footer bg-light p-3 text-end">
              <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-save-fill me-1"></i> Simpan Presensi Guru
              </button>
            </div>
          <?php endif; ?>
        </form>
      </div>

    </div>
  </div>
</main>
