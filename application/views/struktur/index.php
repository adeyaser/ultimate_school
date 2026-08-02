<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Struktur Organisasi Sekolah & Kelas</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Struktur Organisasi</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle-fill me-2"></i><?= $this->session->flashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
          <form action="<?= base_url('struktur') ?>" method="get" class="row g-3 align-items-end">
            <div class="col-md-5">
              <label class="form-label fw-bold">Level Struktur Organisasi:</label>
              <select name="level" class="form-select" required>
                <option value="Sekolah" <?= ($level === 'Sekolah') ? 'selected' : '' ?>>Level Sekolah (Kepsek, Wakasek, Komite)</option>
                <option value="Kelas" <?= ($level === 'Kelas') ? 'selected' : '' ?>>Level Kelas (Wali Kelas, Ketua, Pengurus Kelas)</option>
              </select>
            </div>
            <div class="col-md-4">
              <button type="submit" class="btn btn-primary fw-bold w-100"><i class="bi bi-diagram-3 me-1"></i> Tampilkan Struktur</button>
            </div>
            <div class="col-md-3 text-end">
              <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahStruktur"><i class="bi bi-plus-lg me-1"></i> Tambah Jabatan</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-header border-0 bg-white">
          <h4 class="card-title fw-bold text-primary mb-0"><i class="bi bi-diagram-3-fill me-2"></i> Struktur Organisasi Tingkat <?= $level ?></h4>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
              <thead class="table-light">
                <tr>
                  <th width="10%">Urutan</th>
                  <th>Jabatan Organisasi</th>
                  <th>Pejabat / Personel</th>
                  <th>Role User</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($struktur)): ?>
                  <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada struktur organisasi terdaftar untuk level ini.</td></tr>
                <?php else: ?>
                  <?php foreach ($struktur as $s): ?>
                    <tr>
                      <td><span class="badge text-bg-dark fs-6"><?= $s['urutan'] ?></span></td>
                      <td class="fw-bold fs-5 text-primary"><?= $s['jabatan'] ?></td>
                      <td><i class="bi bi-person-fill text-success me-1"></i><?= $s['full_name'] ?></td>
                      <td><span class="badge text-bg-info"><?= $s['role'] ?></span></td>
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

<!-- Modal Tambah Jabatan -->
<div class="modal fade" id="modalTambahStruktur" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('struktur/simpan') ?>" method="post">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill me-2"></i> Tambah Posisi / Jabatan Organisasi</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Level Organisasi *</label>
              <select name="level" class="form-select" required>
                <option value="Sekolah">Sekolah</option>
                <option value="Kelas">Kelas</option>
              </select>
            </div>
            <div class="col-md-6"><label class="form-label fw-semibold">Urutan Hirarki</label><input type="number" name="urutan" class="form-control" value="1" /></div>
            <div class="col-md-12"><label class="form-label fw-semibold">Nama Jabatan *</label><input type="text" name="jabatan" class="form-control" placeholder="Contoh: Kepala Sekolah / Ketua Kelas" required /></div>
            <div class="col-md-12">
              <label class="form-label fw-semibold">Pilih Pejabat (User) *</label>
              <select name="user_id" class="form-select" required>
                <?php foreach ($users_list as $u): ?>
                  <option value="<?= $u['id'] ?>"><?= $u['full_name'] ?> (<?= $u['role'] ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold">Simpan Jabatan</button>
        </div>
      </form>
    </div>
  </div>
</div>
