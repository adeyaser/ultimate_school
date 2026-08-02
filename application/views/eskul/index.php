<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Kegiatan Ekstrakurikuler (Eskul)</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Eskul</li></ol></div>
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
          <h3 class="card-title fw-bold"><i class="bi bi-trophy-fill me-2 text-warning"></i> Daftar Kegiatan Ekstrakurikuler</h3>
          <?php if (in_array($this->role, array('super_admin', 'admin', 'guru'))): ?>
            <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahEskul"><i class="bi bi-plus-lg me-1"></i> Tambah Eskul Baru</button>
          <?php endif; ?>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
              <thead class="table-light">
                <tr>
                  <th>Kode</th>
                  <th>Nama Eskul</th>
                  <th>Pembina</th>
                  <th>Jadwal Pelatihan</th>
                  <th>Tempat</th>
                  <th>Kuota / Peserta</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($eskul_list)): ?>
                  <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada kegiatan eskul terdaftar.</td></tr>
                <?php else: ?>
                  <?php foreach ($eskul_list as $e): ?>
                    <tr>
                      <td><span class="badge text-bg-dark"><?= $e['kode_eskul'] ?></span></td>
                      <td class="fw-bold"><?= $e['nama_eskul'] ?></td>
                      <td><i class="bi bi-person-fill text-success me-1"></i><?= $e['nama_pembina'] ?></td>
                      <td><span class="badge text-bg-info"><?= $e['hari'] ?> (<?= date('H:i', strtotime($e['jam_mulai'])) ?>-<?= date('H:i', strtotime($e['jam_selesai'])) ?>)</span></td>
                      <td><?= $e['tempat'] ?></td>
                      <td><span class="badge text-bg-success"><?= $e['total_peserta'] ?> / <?= $e['kuota'] ?> Peserta</span></td>
                      <td class="text-end">
                        <?php if ($this->role === 'murid'): ?>
                          <form action="<?= base_url('eskul/daftar_peserta') ?>" method="post" class="d-inline">
                            <input type="hidden" name="eskul_id" value="<?= $e['id'] ?>" />
                            <button type="submit" class="btn btn-sm btn-outline-success fw-bold"><i class="bi bi-plus-circle me-1"></i> Daftar Eskul Ini</button>
                          </form>
                        <?php endif; ?>
                      </td>
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

<!-- Modal Tambah Eskul -->
<div class="modal fade" id="modalTambahEskul" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('eskul/simpan') ?>" method="post">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold"><i class="bi bi-trophy-fill me-2"></i> Tambah Kegiatan Eskul Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-12"><label class="form-label fw-semibold">Nama Eskul *</label><input type="text" name="nama_eskul" class="form-control" placeholder="Pramuka / Futsal / Basket" required /></div>
            <div class="col-md-12">
              <label class="form-label fw-semibold">Pembina Eskul *</label>
              <select name="pembina_id" class="form-select" required>
                <?php foreach ($guru_list as $g): ?>
                  <option value="<?= $g['id'] ?>"><?= $g['full_name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Hari *</label>
              <select name="hari" class="form-select" required>
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
              </select>
            </div>
            <div class="col-md-6"><label class="form-label fw-semibold">Tempat *</label><input type="text" name="tempat" class="form-control" placeholder="Lapangan Utama / Lab Komputer" required /></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Jam Mulai</label><input type="time" name="jam_mulai" class="form-control" value="15:30" /></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Jam Selesai</label><input type="time" name="jam_selesai" class="form-control" value="17:00" /></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning fw-bold">Simpan Eskul</button>
        </div>
      </form>
    </div>
  </div>
</div>
