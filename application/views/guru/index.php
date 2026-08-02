<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Data Guru & Tenaga Kependidikan</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Data Guru</li></ol></div>
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
          <h3 class="card-title fw-bold"><i class="bi bi-person-badge-fill me-2 text-success"></i> Daftar Seluruh Guru</h3>
          <button class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahGuru"><i class="bi bi-plus-lg me-1"></i> Tambah Guru Baru</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
              <thead class="table-light">
                <tr>
                  <th>NIP / NUPTK</th>
                  <th>Nama Guru</th>
                  <th>Pendidikan</th>
                  <th>Jurusan</th>
                  <th>Status Kepegawaian</th>
                  <th>Status Akun</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($guru_list)): ?>
                  <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data guru terdaftar.</td></tr>
                <?php else: ?>
                  <?php foreach ($guru_list as $g): ?>
                    <tr>
                      <td><span class="badge text-bg-dark"><?= $g['nip'] ?></span> <br><small class="text-muted">NUPTK: <?= $g['nuptk'] ? $g['nuptk'] : '-' ?></small></td>
                      <td class="fw-bold"><?= $g['full_name'] ?></td>
                      <td><?= $g['pendidikan_terakhir'] ?></td>
                      <td><?= $g['jurusan_pendidikan'] ?></td>
                      <td><span class="badge text-bg-primary"><?= $g['status_kepegawaian'] ?></span></td>
                      <td><span class="badge text-bg-success"><?= $g['status'] ?></span></td>
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

<!-- Modal Tambah Guru -->
<div class="modal fade" id="modalTambahGuru" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?= base_url('guru/simpan') ?>" method="post">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i> Tambah Guru Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-semibold">NIP *</label><input type="text" name="nip" class="form-control" required /></div>
            <div class="col-md-6"><label class="form-label fw-semibold">NUPTK</label><input type="text" name="nuptk" class="form-control" /></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Nama Lengkap Guru *</label><input type="text" name="full_name" class="form-control" required /></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Username Login *</label><input type="text" name="username" class="form-control" required /></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Password Login</label><input type="password" name="password" class="form-control" placeholder="Default: 12345678" /></div>
            
            <div class="col-md-6"><label class="form-label fw-semibold">Pendidikan Terakhir *</label><input type="text" name="pendidikan_terakhir" class="form-control" placeholder="S1 Pend. Matematika" required /></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Jurusan Pendidikan *</label><input type="text" name="jurusan_pendidikan" class="form-control" required /></div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Status Kepegawaian *</label>
              <select name="status_kepegawaian" class="form-select" required>
                <option value="PNS">PNS</option>
                <option value="PPPK">PPPK</option>
                <option value="Honorer">Honorer</option>
                <option value="Tetap Yayasan">Tetap Yayasan</option>
              </select>
            </div>
            <div class="col-md-4"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control" /></div>
            <div class="col-md-4"><label class="form-label fw-semibold">Telepon/WA</label><input type="text" name="phone" class="form-control" /></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success fw-bold">Simpan Data Guru</button>
        </div>
      </form>
    </div>
  </div>
</div>
