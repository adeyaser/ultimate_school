<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Repositori Bank Soal</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Bank Soal</li></ol></div>
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
          <h3 class="card-title fw-bold"><i class="bi bi-folder-symlink-fill me-2 text-primary"></i> Daftar Paket Bank Soal</h3>
          <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahBank"><i class="bi bi-plus-lg me-1"></i> Buat Paket Soal Baru</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
              <thead class="table-light">
                <tr>
                  <th>Kode Soal</th>
                  <th>Judul Paket Soal</th>
                  <th>Mata Pelajaran</th>
                  <th>Kelas</th>
                  <th>Jenis</th>
                  <th>Tingkat</th>
                  <th>Jml Soal</th>
                  <th>Status</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($bank_list)): ?>
                  <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada paket bank soal. Klik 'Buat Paket Soal Baru'.</td></tr>
                <?php else: ?>
                  <?php foreach ($bank_list as $b): ?>
                    <tr>
                      <td><span class="badge text-bg-dark"><?= $b['kode_soal'] ?></span></td>
                      <td class="fw-bold"><?= $b['judul'] ?></td>
                      <td><?= $b['nama_mapel'] ?></td>
                      <td><span class="badge text-bg-info"><?= $b['nama_kelas'] ?></span></td>
                      <td><?= $b['jenis_soal'] ?></td>
                      <td>
                        <?php if ($b['tingkat_kesulitan'] === 'Mudah'): ?><span class="badge text-bg-success">Mudah</span>
                        <?php elseif ($b['tingkat_kesulitan'] === 'Sedang'): ?><span class="badge text-bg-warning">Sedang</span>
                        <?php else: ?><span class="badge text-bg-danger">Sulit</span><?php endif; ?>
                      </td>
                      <td><span class="badge text-bg-secondary"><?= $b['jumlah_soal'] ?> Soal</span></td>
                      <td><span class="badge text-bg-success"><?= $b['status'] ?></span></td>
                      <td class="text-end">
                        <a href="<?= base_url('banksoal/export_word/' . $b['id']) ?>" class="btn btn-sm btn-outline-info me-1 fw-bold" title="Export to Word (.doc)">
                          <i class="bi bi-file-earmark-word-fill me-1"></i> Word
                        </a>
                        <a href="<?= base_url('banksoal/detail/' . $b['id']) ?>" class="btn btn-sm btn-outline-primary fw-bold">
                          <i class="bi bi-gear-fill me-1"></i> Kelola Soal
                        </a>
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

<!-- Modal Tambah Bank Soal -->
<div class="modal fade" id="modalTambahBank" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?= base_url('banksoal/simpan_bank') ?>" method="post">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill me-2"></i> Buat Paket Bank Soal Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label fw-semibold">Judul Paket Soal *</label>
              <input type="text" name="judul" class="form-control" placeholder="Contoh: Bank Soal Matematika Wajib Bab 1" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Mata Pelajaran *</label>
              <select name="mata_pelajaran_id" class="form-select" required>
                <?php foreach ($mapel_list as $m): ?>
                  <option value="<?= $m['id'] ?>"><?= $m['kode_mapel'] ?> - <?= $m['nama_mapel'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kelas Target *</label>
              <select name="kelas_id" class="form-select" required>
                <?php foreach ($kelas_list as $k): ?>
                  <option value="<?= $k['id'] ?>"><?= $k['nama_kelas'] ?> (<?= $k['tingkat'] ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Jenis Soal</label>
              <select name="jenis_soal" class="form-select">
                <option value="Pilihan Ganda">Pilihan Ganda</option>
                <option value="Essay">Essay</option>
                <option value="Campuran">Campuran</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Tingkat Kesulitan</label>
              <select name="tingkat_kesulitan" class="form-select">
                <option value="Mudah">Mudah</option>
                <option value="Sedang" selected>Sedang</option>
                <option value="Sulit">Sulit</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Durasi Pengerjaan (Menit)</label>
              <input type="number" name="durasi" class="form-control" value="60" required />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold">Simpan Paket Soal</button>
        </div>
      </form>
    </div>
  </div>
</div>
