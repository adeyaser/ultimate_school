<?php
  $user_role = isset($user_data['role']) ? $user_data['role'] : 'super_admin';
  $is_admin = in_array($user_role, array('super_admin', 'admin'));
  $cur_j = isset($active_jenjang) ? $active_jenjang : 'SMP';
  $j_badge = ($cur_j === 'SD') ? 'text-bg-success' : (($cur_j === 'SMP') ? 'text-bg-info text-white' : (($cur_j === 'SMA') ? 'text-bg-warning text-dark' : 'text-bg-primary'));
?>
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold"><i class="bi bi-book-fill text-primary me-2"></i> Kurikulum & Data Mata Pelajaran</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Kurikulum & Mapel</li>
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

      <!-- Mapel Table Card -->
      <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h5 class="fw-bold text-primary mb-1"><i class="bi bi-journal-text me-2"></i> Daftar Kurikulum & Mata Pelajaran</h5>
            <span class="badge <?= $j_badge ?> px-3 py-1 fs-6">Mode Aktif Sekolah: <?= htmlspecialchars($cur_j) ?></span>
          </div>
          <?php if ($is_admin): ?>
            <button class="btn btn-primary fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahMapel">
              <i class="bi bi-plus-lg me-1"></i> Tambah Mapel Baru
            </button>
          <?php endif; ?>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive p-3">
            <table id="tableMapel" class="table table-hover table-striped align-middle w-100 m-0">
              <thead class="table-dark">
                <tr>
                  <th style="width: 40px;">No</th>
                  <th>Kode</th>
                  <th>Nama Mata Pelajaran</th>
                  <th class="text-center">Jenjang</th>
                  <th class="text-center">Kelompok</th>
                  <th class="text-center">Jam / Mgg</th>
                  <th class="text-center">KKM Minimal</th>
                  <th>Deskripsi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; foreach ($mapel_list as $m): ?>
                  <?php 
                    $mb = ($m['jenjang'] === 'SD') ? 'text-bg-success' : (($m['jenjang'] === 'SMP') ? 'text-bg-info text-white' : (($m['jenjang'] === 'SMA') ? 'text-bg-warning text-dark' : 'text-bg-secondary'));
                  ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><span class="badge text-bg-dark font-monospace"><?= htmlspecialchars($m['kode_mapel']) ?></span></td>
                    <td class="fw-bold text-primary fs-6"><?= htmlspecialchars($m['nama_mapel']) ?></td>
                    <td class="text-center"><span class="badge <?= $mb ?> px-3 py-1 fs-6"><?= htmlspecialchars($m['jenjang']) ?></span></td>
                    <td class="text-center"><span class="badge text-bg-secondary px-3 py-1 fs-6"><?= htmlspecialchars($m['kelompok']) ?></span></td>
                    <td class="text-center fw-bold"><?= htmlspecialchars($m['jam_per_minggu']) ?> Jam</td>
                    <td class="text-center"><span class="badge text-bg-success px-3 py-1 fs-6"><?= htmlspecialchars($m['kkm']) ?></span></td>
                    <td><small class="text-muted"><?= htmlspecialchars($m['deskripsi'] ? $m['deskripsi'] : '-') ?></small></td>
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

<!-- Modal Tambah Mapel -->
<?php if ($is_admin): ?>
<div class="modal fade" id="modalTambahMapel" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <form action="<?= base_url('mapel/simpan') ?>" method="post">
        <div class="modal-header bg-primary text-white p-3">
          <h5 class="modal-title fw-bold"><i class="bi bi-book-half me-2"></i> Tambah Mata Pelajaran Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Kode Mapel *</label>
              <input type="text" name="kode_mapel" class="form-control" placeholder="IND-X / IPA-SMP" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Kelompok Mapel *</label>
              <select name="kelompok" class="form-select" required>
                <option value="Wajib">Wajib (Kurikulum Nasional)</option>
                <option value="Peminatan">Peminatan / Kejuruan</option>
                <option value="Muatan Lokal">Muatan Lokal</option>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Nama Mata Pelajaran *</label>
              <input type="text" name="nama_mapel" class="form-control" placeholder="Nama Lengkap Mata Pelajaran" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Jam Per Minggu *</label>
              <input type="number" name="jam_per_minggu" class="form-control" value="4" min="1" max="20" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">KKM Minimal *</label>
              <input type="number" name="kkm" class="form-control" value="75" min="0" max="100" required />
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Deskripsi Tambahan</label>
              <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi Singkat Pembelajaran..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-check-lg me-1"></i> Simpan Mapel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    if ($('#tableMapel').length) {
      $('#tableMapel').DataTable({
        language: {
          search: "Cari Mapel:",
          lengthMenu: "Tampilkan _MENU_ data",
          info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ mapel",
          infoEmpty: "Tidak ada data mapel",
          infoFiltered: "(disaring dari _MAX_ total mapel)",
          zeroRecords: "Mata pelajaran tidak ditemukan",
          paginate: {
            first: "Awal",
            last: "Akhir",
            next: "Selanjutnya",
            previous: "Sebelumnya"
          }
        },
        columnDefs: [
          { targets: [0], orderable: false }
        ]
      });
    }
  }
});
</script>
