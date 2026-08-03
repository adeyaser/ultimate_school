<?php
  $user_role = isset($user_data['role']) ? $user_data['role'] : 'super_admin';
?>
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0 fw-bold">
            <i class="bi bi-people-fill text-success me-2"></i> Daftar Pengumpulan Tugas
          </h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('akademik/tugas') ?>">Tugas & PR</a></li>
            <li class="breadcrumb-item active">Pengumpulan</li>
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

      <!-- Tugas Info Card -->
      <?php if ($tugas): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-4">
          <div class="card-body p-4" style="background: linear-gradient(135deg, #198754 0%, #157347 100%); border-radius: 1rem;">
            <div class="row text-white">
              <div class="col-md-4">
                <small class="opacity-75 fw-bold d-block">JUDUL TUGAS</small>
                <h4 class="fw-bold mb-0"><?= $tugas['judul'] ?></h4>
              </div>
              <div class="col-md-2">
                <small class="opacity-75 fw-bold d-block">MATA PELAJARAN</small>
                <span class="fs-6 fw-bold"><?= $tugas['nama_mapel'] ?></span>
              </div>
              <div class="col-md-2">
                <small class="opacity-75 fw-bold d-block">KELAS TARGET</small>
                <span class="fs-6 fw-bold"><?= $tugas['nama_kelas'] ?></span>
              </div>
              <div class="col-md-2">
                <small class="opacity-75 fw-bold d-block">DEADLINE</small>
                <span class="badge text-bg-warning text-dark fs-6"><?= date('d M Y', strtotime($tugas['deadline'])) ?></span>
              </div>
              <div class="col-md-2">
                <small class="opacity-75 fw-bold d-block">TOTAL PENGUMPULAN</small>
                <span class="badge text-bg-light text-dark fs-5 fw-bold"><?= count($pengumpulan_list) ?> Siswa</span>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Table Pengumpulan -->
      <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h5 class="fw-bold text-success mb-0"><i class="bi bi-clipboard-check me-2"></i> Daftar Siswa Yang Sudah Mengumpulkan</h5>
            <small class="text-muted">Review jawaban dan berikan penilaian untuk setiap siswa</small>
          </div>
          <a href="<?= base_url('akademik/tugas') ?>" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Tugas
          </a>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive p-3">
            <table id="tablePengumpulan" class="table table-hover table-striped align-middle w-100 m-0">
              <thead class="table-dark">
                <tr>
                  <th style="width: 50px;">No</th>
                  <th>Nama Siswa</th>
                  <th>NISN</th>
                  <th>Tanggal Pengumpulan</th>
                  <th>Catatan Jawaban</th>
                  <th>File Lampiran</th>
                  <th>Status</th>
                  <th>Nilai</th>
                  <th style="width: 150px;" class="text-center">Aksi Penilaian</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; foreach ($pengumpulan_list as $p): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($p['nama_murid']) ?></strong></td>
                    <td><code><?= htmlspecialchars($p['nisn']) ?></code></td>
                    <td>
                      <small class="text-muted">
                        <i class="bi bi-calendar-event me-1"></i> <?= date('d M Y H:i', strtotime($p['tanggal_kumpul'])) ?>
                      </small>
                    </td>
                    <td>
                      <small class="text-dark"><?= $p['catatan_jawaban'] ? htmlspecialchars(mb_substr($p['catatan_jawaban'], 0, 80)) . '...' : '-' ?></small>
                    </td>
                    <td>
                      <?php if ($p['file_jawaban']): ?>
                        <a href="<?= base_url('uploads/tugas/' . $p['file_jawaban']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                          <i class="bi bi-download me-1"></i> Unduh
                        </a>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($p['status'] == 'Dinilai'): ?>
                        <span class="badge text-bg-success px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i> Dinilai</span>
                      <?php elseif ($p['status'] == 'Revisi'): ?>
                        <span class="badge text-bg-warning text-dark px-3 py-1"><i class="bi bi-arrow-repeat me-1"></i> Revisi</span>
                      <?php else: ?>
                        <span class="badge text-bg-info px-3 py-1"><i class="bi bi-hourglass-split me-1"></i> Dikumpulkan</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($p['nilai'] !== null): ?>
                        <span class="badge text-bg-dark fs-6 px-3 py-1"><?= $p['nilai'] ?></span>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-sm btn-primary fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalNilai_<?= $p['id'] ?>">
                        <i class="bi bi-pencil-square me-1"></i> Beri Nilai
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<!-- Modals Penilaian -->
<?php if (!empty($pengumpulan_list)): ?>
  <?php foreach ($pengumpulan_list as $p): ?>
    <div class="modal fade" id="modalNilai_<?= $p['id'] ?>" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <form action="<?= base_url('akademik/nilai_pengumpulan') ?>" method="post">
            <input type="hidden" name="pengumpulan_id" value="<?= $p['id'] ?>" />
            <div class="modal-header bg-primary text-white p-3">
              <h5 class="modal-title fw-bold"><i class="bi bi-award-fill me-2"></i> Beri Nilai: <?= $p['nama_murid'] ?></h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
              <!-- Student Answer Preview -->
              <div class="mb-3">
                <label class="form-label fw-bold text-muted">JAWABAN SISWA:</label>
                <div class="p-3 bg-light rounded-3 border">
                  <p class="mb-0"><?= $p['catatan_jawaban'] ? nl2br(htmlspecialchars($p['catatan_jawaban'])) : '<em>Tidak ada catatan jawaban.</em>' ?></p>
                </div>
              </div>

              <?php if ($p['file_jawaban']): ?>
                <div class="mb-3">
                  <label class="form-label fw-bold text-muted">FILE LAMPIRAN:</label>
                  <a href="<?= base_url('uploads/tugas/' . $p['file_jawaban']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> <?= $p['file_jawaban'] ?>
                  </a>
                </div>
              <?php endif; ?>

              <hr>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">Nilai (0 - 100) *</label>
                  <input type="number" name="nilai" class="form-control form-control-lg text-center fw-bold" min="0" max="100" step="0.01" value="<?= $p['nilai'] ? $p['nilai'] : '' ?>" required placeholder="0-100" />
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Tanggal Pengumpulan</label>
                  <input type="text" class="form-control" value="<?= date('d M Y H:i', strtotime($p['tanggal_kumpul'])) ?>" disabled />
                </div>
                <div class="col-md-12">
                  <label class="form-label fw-bold">Catatan / Feedback Guru (Opsional)</label>
                  <textarea name="catatan_guru" class="form-control" rows="2" placeholder="Berikan catatan atau feedback untuk siswa..."><?= $p['catatan_guru'] ? htmlspecialchars($p['catatan_guru']) : '' ?></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer bg-light p-3">
              <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="bi bi-check-lg me-1"></i> Simpan Nilai</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    if ($('#tablePengumpulan').length) {
      $('#tablePengumpulan').DataTable({
        language: {
          search: "Cari Pengumpulan:",
          lengthMenu: "Tampilkan _MENU_ data per halaman",
          info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          infoEmpty: "Belum ada pengumpulan",
          infoFiltered: "(disaring dari _MAX_ total data)",
          zeroRecords: "Data pengumpulan tidak ditemukan",
          paginate: {
            first: "Awal",
            last: "Akhir",
            next: "Selanjutnya",
            previous: "Sebelumnya"
          }
        },
        columnDefs: [
          { targets: [0, 5, 8], orderable: false }
        ]
      });
    }
  }
});
</script>

