<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Sertifikat & Ijazah Siswa</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Sertifikat</li></ol></div>
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
          <h3 class="card-title fw-bold"><i class="bi bi-award-fill me-2 text-warning"></i> Penerbitan Sertifikat Digital & QR Verification</h3>
          <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahSertifikat"><i class="bi bi-plus-lg me-1"></i> Terbitkan Sertifikat Baru</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
              <thead class="table-light">
                <tr>
                  <th>No Seri</th>
                  <th>Nama Siswa</th>
                  <th>Jenis Dokumen</th>
                  <th>Deskripsi</th>
                  <th>Tanggal Terbit</th>
                  <th>Verifikasi QR</th>
                  <th class="text-end">Aksi Cetak</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($sertifikat_list)): ?>
                  <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada sertifikat diterbitkan.</td></tr>
                <?php else: ?>
                  <?php foreach ($sertifikat_list as $s): ?>
                    <tr>
                      <td><span class="badge text-bg-dark"><?= $s['nomor_seri'] ?></span></td>
                      <td class="fw-bold"><?= $s['nama_murid'] ?></td>
                      <td><span class="badge text-bg-primary"><?= $s['jenis'] ?></span></td>
                      <td><?= $s['deskripsi'] ?></td>
                      <td><?= date('d M Y', strtotime($s['tanggal_terbit'])) ?></td>
                      <td><span class="badge text-bg-success"><i class="bi bi-qr-code me-1"></i> VERIFIED (<?= $s['qr_code'] ?>)</span></td>
                      <td class="text-end">
                        <a href="<?= base_url('akademik/cetak_sertifikat/' . $s['id']) ?>" target="_blank" class="btn btn-sm btn-outline-warning fw-bold"><i class="bi bi-printer-fill me-1"></i> Cetak Sertifikat</a>
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

<!-- Modal Terbit Sertifikat -->
<div class="modal fade" id="modalTambahSertifikat" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('akademik/simpan_sertifikat') ?>" method="post">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold"><i class="bi bi-award-fill me-2"></i> Terbitkan Sertifikat / Ijazah</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label fw-semibold">Pilih Siswa *</label>
              <select name="murid_id" class="form-select" required>
                <?php foreach ($murid_list as $m): ?>
                  <option value="<?= $m['id'] ?>"><?= $m['full_name'] ?> (NISN: <?= $m['nisn'] ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-semibold">Jenis *</label>
              <select name="jenis" class="form-select" required>
                <option value="Sertifikat">Sertifikat Kelulusan / Prestasi</option>
                <option value="Ijazah">Ijazah Resmi</option>
                <option value="Piagam">Piagam Penghargaan</option>
              </select>
            </div>
            <div class="col-md-12"><label class="form-label fw-semibold">Deskripsi / Judul Sertifikat *</label><textarea name="deskripsi" class="form-control" rows="2" placeholder="Contoh: Sertifikat Juara 1 Olimpiade Matematika" required></textarea></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning fw-bold">Terbitkan & Buat QR</button>
        </div>
      </form>
    </div>
  </div>
</div>
