<?php
  $user_role = isset($user_data['role']) ? $user_data['role'] : 'super_admin';
  $is_admin_or_teacher = in_array($user_role, array('super_admin', 'admin', 'guru', 'wali_kelas', 'kepala_sekolah'));
?>
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0 fw-bold"><i class="bi bi-calendar3 text-primary me-2"></i> Jadwal Pelajaran Per Kelas</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Pelajaran</li>
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
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-3" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $this->session->flashdata('error') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Filter Selection & Actions -->
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-3">
          <?php if ($is_admin_or_teacher): ?>
            <form action="<?= base_url('mapel/jadwal') ?>" method="get" class="row g-3 align-items-end">
              <div class="col-md-5">
                <label class="form-label fw-bold text-primary mb-1"><i class="bi bi-door-open-fill me-1"></i> Pilih Kelas / Rombel *</label>
                <select name="kelas_id" class="form-select fw-bold border-primary py-2" onchange="this.form.submit()" required>
                  <?php foreach ($kelas_list as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($selected_kelas == $k['id']) ? 'selected' : '' ?>>
                      Kelas <?= $k['nama_kelas'] ?> (Tingkat <?= $k['tingkat'] ?> - <?= $k['jurusan'] ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-3">
                <button type="submit" class="btn btn-primary fw-bold w-100 py-2"><i class="bi bi-search me-1"></i> Tampilkan Jadwal</button>
              </div>

              <div class="col-md-4 text-end">
                <button type="button" class="btn btn-success fw-bold rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                  <i class="bi bi-plus-lg me-1"></i> Tambah Entri Jadwal
                </button>
              </div>
            </form>
          <?php else: ?>
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 p-2">
              <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white p-3 rounded-circle fs-3 shadow-sm"><i class="bi bi-door-open-fill"></i></div>
                <div>
                  <small class="text-muted fw-semibold d-block">INFORMASI KELAS ANDA</small>
                  <h4 class="fw-bold text-primary mb-0">
                    Kelas <?= isset($selected_class['nama_kelas']) ? $selected_class['nama_kelas'] : '-' ?> 
                    <span class="badge text-bg-info fs-6 ms-2">Tingkat <?= isset($selected_class['tingkat']) ? $selected_class['tingkat'] : '-' ?> (<?= isset($selected_class['jurusan']) ? $selected_class['jurusan'] : '-' ?>)</span>
                  </h4>
                </div>
              </div>
              <span class="badge text-bg-success px-3 py-2 fs-6 rounded-pill"><i class="bi bi-lock-fill me-1"></i> Terkunci Sesuai Kelas Terdaftar</span>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Schedule Table Card -->
      <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
          <h5 class="fw-bold text-primary mb-0">
            <i class="bi bi-calendar-week-fill me-2"></i> Jadwal Mingguan Kelas: 
            <span class="badge text-bg-primary fs-6 ms-1"><?= isset($selected_class['nama_kelas']) ? $selected_class['nama_kelas'] : 'Rombel' ?></span>
          </h5>
          <span class="badge text-bg-secondary px-3 py-2 fs-6">Total: <?= count($jadwal_list) ?> Jam Pelajaran</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
              <thead class="table-dark">
                <tr>
                  <th style="width: 120px;">Hari</th>
                  <th style="width: 160px;">Waktu Pelajaran</th>
                  <th>Mata Pelajaran</th>
                  <th>Guru Pengampu</th>
                  <th style="width: 120px;">Ruangan</th>
                  <?php if ($is_admin_or_teacher): ?>
                    <th style="width: 80px;" class="text-center">Aksi</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($jadwal_list)): ?>
                  <tr><td colspan="<?= $is_admin_or_teacher ? '6' : '5' ?>" class="text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i> Belum ada entri jadwal pelajaran untuk kelas ini.</td></tr>
                <?php else: ?>
                  <?php foreach ($jadwal_list as $j): ?>
                    <tr>
                      <td>
                        <?php 
                          $hari_color = ($j['hari'] === 'Senin') ? 'text-bg-primary' : (($j['hari'] === 'Selasa') ? 'text-bg-info text-white' : (($j['hari'] === 'Rabu') ? 'text-bg-success' : (($j['hari'] === 'Kamis') ? 'text-bg-warning text-dark' : (($j['hari'] === 'Jumat') ? 'text-bg-danger' : 'text-bg-dark'))));
                        ?>
                        <span class="badge <?= $hari_color ?> fs-6 px-3 py-1"><?= $j['hari'] ?></span>
                      </td>
                      <td><strong class="text-dark"><?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></strong></td>
                      <td class="fw-bold text-primary fs-6"><?= $j['nama_mapel'] ?></td>
                      <td><i class="bi bi-person-circle text-success me-1"></i> <?= $j['nama_guru'] ? $j['nama_guru'] : '-' ?></td>
                      <td><span class="badge text-bg-light border px-2 py-1"><?= $j['ruangan'] ? $j['ruangan'] : '-' ?></span></td>
                      <?php if ($is_admin_or_teacher): ?>
                        <td class="text-center">
                          <a href="<?= base_url('mapel/hapus_jadwal/' . $j['id'] . '?kelas_id=' . $selected_kelas) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus entri jadwal ini?')" title="Hapus Jadwal">
                            <i class="bi bi-trash-fill"></i>
                          </a>
                        </td>
                      <?php endif; ?>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<!-- Modal Tambah Jadwal (For Admin & Teachers) -->
<?php if ($is_admin_or_teacher): ?>
<div class="modal fade" id="modalTambahJadwal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <form action="<?= base_url('mapel/simpan_jadwal') ?>" method="post">
        <div class="modal-header bg-success text-white p-3">
          <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2"></i> Tambah Entri Jadwal Pelajaran</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Kelas Target *</label>
              <select name="kelas_id" class="form-select" required>
                <?php foreach ($kelas_list as $k): ?>
                  <option value="<?= $k['id'] ?>" <?= ($selected_kelas == $k['id']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Hari *</label>
              <select name="hari" class="form-select" required>
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Mata Pelajaran *</label>
              <select name="mata_pelajaran_id" class="form-select" required>
                <option value="">-- Pilih Mata Pelajaran --</option>
                <?php foreach ($mapel_list as $mp): ?>
                  <option value="<?= $mp['id'] ?>"><?= $mp['nama_mapel'] ?> (<?= $mp['kode_mapel'] ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Guru Pengampu *</label>
              <select name="guru_id" class="form-select" required>
                <option value="">-- Pilih Guru --</option>
                <?php foreach ($guru_list as $g): ?>
                  <option value="<?= $g['id'] ?>"><?= $g['full_name'] ?> (NIP: <?= $g['nip'] ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Jam Mulai *</label>
              <input type="time" name="jam_mulai" class="form-control" value="07:30" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Jam Selesai *</label>
              <input type="time" name="jam_selesai" class="form-control" value="09:00" required />
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Ruangan *</label>
              <input type="text" name="ruangan" class="form-control" placeholder="Contoh: R.101 / Lab Komputer" value="R.101" required />
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold"><i class="bi bi-save me-1"></i> Simpan Jadwal</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
