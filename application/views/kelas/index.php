<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Data Rombongan Belajar (Kelas)</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Kelas</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle-fill me-2"></i><?= $this->session->flashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <div class="card shadow-sm mb-4">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
          <h3 class="card-title fw-bold"><i class="bi bi-door-open-fill me-2 text-warning"></i> Daftar Kelas Aktif T.A. <?= isset($ta_active['nama']) ? $ta_active['nama'] : '' ?></h3>
          <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahKelas"><i class="bi bi-plus-lg me-1"></i> Tambah Kelas Baru</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
              <thead class="table-light">
                <tr>
                  <th>Nama Kelas</th>
                  <th>Tingkat</th>
                  <th>Jurusan</th>
                  <th>Ruangan</th>
                  <th>Wali Kelas</th>
                  <th>Total Murid</th>
                  <th>Kapasitas</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($kelas_list)): ?>
                  <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada kelas terdaftar.</td></tr>
                <?php else: ?>
                  <?php foreach ($kelas_list as $k): ?>
                    <tr>
                      <td class="fw-bold text-primary"><?= $k['nama_kelas'] ?></td>
                      <td><span class="badge text-bg-secondary"><?= $k['tingkat'] ?></span></td>
                      <td><?= $k['jurusan'] ?></td>
                      <td><?= $k['ruangan'] ?></td>
                      <td><i class="bi bi-person-fill me-1 text-success"></i><?= $k['nama_wali_kelas'] ? $k['nama_wali_kelas'] : 'Belum Ditentukan' ?></td>
                      <td><span class="badge text-bg-success fs-6"><?= $k['total_murid'] ?> Siswa</span></td>
                      <td><?= $k['kapasitas'] ?> Siswa</td>
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

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modalTambahKelas" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('kelas/simpan') ?>" method="post">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold"><i class="bi bi-door-open-fill me-2"></i> Tambah Rombel / Kelas Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <?php 
              $jenjang = isset($sekolah['jenjang']) ? $sekolah['jenjang'] : 'SMA';
            ?>
            <div class="col-md-6"><label class="form-label fw-semibold">Nama Kelas *</label><input type="text" name="nama_kelas" class="form-control" placeholder="<?= ($jenjang === 'SD') ? '1-A / 2-B' : (($jenjang === 'SMP') ? '7-A / 8-B' : 'X MIPA 1') ?>" required /></div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tingkat Kelas (Jenjang: <?= $jenjang ?>) *</label>
              <select name="tingkat" class="form-select fw-bold" required>
                <?php if ($jenjang === 'SD'): ?>
                  <option value="I">Tingkat I (Kelas 1 SD)</option>
                  <option value="II">Tingkat II (Kelas 2 SD)</option>
                  <option value="III">Tingkat III (Kelas 3 SD)</option>
                  <option value="IV">Tingkat IV (Kelas 4 SD)</option>
                  <option value="V">Tingkat V (Kelas 5 SD)</option>
                  <option value="VI">Tingkat VI (Kelas 6 SD)</option>
                <?php elseif ($jenjang === 'SMP'): ?>
                  <option value="VII">Tingkat VII (Kelas 7 SMP)</option>
                  <option value="VIII">Tingkat VIII (Kelas 8 SMP)</option>
                  <option value="IX">Tingkat IX (Kelas 9 SMP)</option>
                <?php else: ?>
                  <option value="X">Tingkat X (Kelas 10 SMA/SMK)</option>
                  <option value="XI">Tingkat XI (Kelas 11 SMA/SMK)</option>
                  <option value="XII">Tingkat XII (Kelas 12 SMA/SMK)</option>
                <?php endif; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Jurusan / Program *</label>
              <input type="text" name="jurusan" class="form-control" 
                placeholder="<?= ($jenjang === 'SD') ? 'Tematik / General' : (($jenjang === 'SMP') ? 'Reguler / Billingual' : (($jenjang === 'SMK') ? 'RPL / TKJ / DKV' : 'MIPA / IPS / Bahasa')) ?>" 
                value="<?= ($jenjang === 'SD') ? 'Tematik' : (($jenjang === 'SMP') ? 'Reguler' : 'MIPA') ?>" required />
            </div>
            <div class="col-md-6"><label class="form-label fw-semibold">Ruangan</label><input type="text" name="ruangan" class="form-control" placeholder="R.101" /></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Kapasitas Maksimal</label><input type="number" name="kapasitas" class="form-control" value="36" /></div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Wali Kelas</label>
              <select name="wali_kelas_id" class="form-select">
                <option value="">-- Pilih Wali Kelas --</option>
                <?php foreach ($guru_list as $g): ?>
                  <option value="<?= $g['user_id'] ?>"><?= $g['full_name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning fw-bold">Simpan Kelas</button>
        </div>
      </form>
    </div>
  </div>
</div>
