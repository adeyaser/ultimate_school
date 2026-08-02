<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Detail Profil & Pemberkasan Siswa</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="<?= base_url('murid') ?>">Data Murid</a></li><li class="breadcrumb-item active">Detail</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= $this->session->flashdata('success') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $this->session->flashdata('error') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- Student Info -->
        <div class="col-md-5">
          <div class="card shadow-sm border-0 rounded-4 text-center p-4">
            <div class="card-body">
              <?php 
                $student_photo = (!empty($murid['photo']) && (file_exists(FCPATH . $murid['photo']) || strpos($murid['photo'], 'http') === 0)) 
                  ? ((strpos($murid['photo'], 'http') === 0) ? $murid['photo'] : base_url($murid['photo'])) 
                  : base_url('dist/assets/img/avatar.png');
              ?>
              <img src="<?= $student_photo ?>" class="rounded-circle shadow mb-3 border border-3 border-primary" width="130" height="130" style="object-fit: cover;" alt="Pas Foto Siswa" />
              <h3 class="fw-bold mb-1"><?= $murid['full_name'] ?></h3>
              <p class="text-primary fw-bold mb-3"><?= $murid['nama_kelas'] ?> (NISN: <?= $murid['nisn'] ?>)</p>

              <table class="table table-bordered text-start bg-light">
                <tr><th>NIS</th><td><?= $murid['nis'] ?></td></tr>
                <tr><th>Tempat, Tgl Lahir</th><td><?= $murid['tempat_lahir'] ?>, <?= date('d M Y', strtotime($murid['tanggal_lahir'])) ?></td></tr>
                <tr><th>Jenis Kelamin</th><td><?= ($murid['gender'] === 'L') ? 'Laki-laki' : 'Perempuan' ?></td></tr>
                <tr><th>Agama</th><td><?= $murid['agama'] ?></td></tr>
                <tr><th>Telepon</th><td><?= $murid['phone'] ?></td></tr>
                <tr><th>Email</th><td><?= $murid['email'] ?></td></tr>
                <tr><th>Status Siswa</th><td><span class="badge text-bg-success"><?= $murid['status_murid'] ?></span></td></tr>
              </table>
            </div>
          </div>
        </div>

        <!-- Parents & Documents -->
        <div class="col-md-7">
          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white p-3 fw-bold text-primary border-bottom">
              <i class="bi bi-people-fill me-2"></i> Data Orang Tua / Wali
            </div>
            <div class="card-body p-3">
              <table class="table table-hover">
                <tr><th>Nama Ayah</th><td><?= isset($ortu['ayah_nama']) ? $ortu['ayah_nama'] : '-' ?></td><th>Pekerjaan</th><td><?= isset($ortu['ayah_pekerjaan']) ? $ortu['ayah_pekerjaan'] : '-' ?></td></tr>
                <tr><th>Nama Ibu</th><td><?= isset($ortu['ibu_nama']) ? $ortu['ibu_nama'] : '-' ?></td><th>Pekerjaan</th><td><?= isset($ortu['ibu_pekerjaan']) ? $ortu['ibu_pekerjaan'] : '-' ?></td></tr>
              </table>
            </div>
          </div>

          <!-- Section Pemberkasan / Dokumen Siswa -->
          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white p-3 fw-bold text-primary border-bottom d-flex justify-content-between align-items-center">
              <span><i class="bi bi-folder-check me-2"></i> Pemberkasan / Dokumen Siswa</span>
              <button class="btn btn-primary btn-sm fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalUploadDokumen">
                <i class="bi bi-cloud-upload-fill me-1"></i> Upload Dokumen Baru
              </button>
            </div>
            <div class="card-body p-0">
              <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                  <tr>
                    <th>Jenis Dokumen</th>
                    <th>Nama / Berkas</th>
                    <th>Status</th>
                    <th>Lihat Dokumen</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($dokumen)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada berkas dokumen yang diunggah.</td></tr>
                  <?php else: ?>
                    <?php foreach ($dokumen as $d): ?>
                      <tr>
                        <td><span class="badge text-bg-primary"><?= $d['jenis_dokumen'] ?></span></td>
                        <td class="fw-bold"><?= $d['nama_file'] ?></td>
                        <td>
                          <?php if ($d['status'] === 'Verified'): ?><span class="badge text-bg-success"><i class="bi bi-patch-check-fill me-1"></i> Verified</span>
                          <?php elseif ($d['status'] === 'Rejected'): ?><span class="badge text-bg-danger"><i class="bi bi-x-circle-fill me-1"></i> Rejected</span>
                          <?php else: ?><span class="badge text-bg-warning"><i class="bi bi-clock-fill me-1"></i> Pending</span><?php endif; ?>
                        </td>
                        <td>
                          <?php if (!empty($d['file_path'])): ?>
                            <a href="<?= base_url($d['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-info fw-bold">
                              <i class="bi bi-file-earmark-arrow-down me-1"></i> Buka File
                            </a>
                          <?php else: ?>
                            <span class="text-muted small">-</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div class="d-flex gap-1">
                            <form action="<?= base_url('murid/verifikasi_dokumen/' . $d['id']) ?>" method="post" class="d-flex gap-1">
                              <input type="hidden" name="murid_id" value="<?= $murid['id'] ?>" />
                              <select name="status" class="form-select form-select-sm" style="width: 100px;">
                                <option value="Verified" <?= ($d['status'] === 'Verified') ? 'selected' : '' ?>>Verify</option>
                                <option value="Rejected" <?= ($d['status'] === 'Rejected') ? 'selected' : '' ?>>Reject</option>
                              </select>
                              <button type="submit" class="btn btn-sm btn-success" title="Update Status"><i class="bi bi-check-lg"></i></button>
                            </form>
                            <a href="<?= base_url('murid/hapus_dokumen/' . $d['id'] . '/' . $murid['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus berkas dokumen ini?')" class="btn btn-sm btn-outline-danger" title="Hapus Berkas">
                              <i class="bi bi-trash-fill"></i>
                            </a>
                          </div>
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
  </div>
</main>

<!-- Modal Upload Dokumen Siswa -->
<div class="modal fade" id="modalUploadDokumen" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-primary text-white p-3">
        <h5 class="modal-title fw-bold"><i class="bi bi-cloud-upload me-2"></i> Upload Dokumen Pemberkasan Siswa</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('murid/upload_dokumen') ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="murid_id" value="<?= $murid['id'] ?>" />
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold">Jenis Dokumen *</label>
            <select name="jenis_dokumen" class="form-select" required>
              <option value="KK">Kartu Keluarga (KK)</option>
              <option value="Akta">Akta Kelahiran</option>
              <option value="Ijazah">Ijazah Kelulusan (SMP/MTS)</option>
              <option value="Sertifikat">Sertifikat Prestasi / Piagam</option>
              <option value="Foto">Pas Foto Resmi (3x4)</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Judul / Keterangan Berkas *</label>
            <input type="text" name="nama_file" class="form-control" placeholder="Contoh: Kartu Keluarga Asli (PDF/JPG)" required />
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Pilih Berkas Dokumen (PDF / JPG / PNG / DOCX) *</label>
            <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required />
            <small class="text-muted">Maksimal ukuran berkas 10 MB.</small>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="bi bi-upload me-1"></i> Unggah Berkas</button>
        </div>
      </form>
    </div>
  </div>
</div>
