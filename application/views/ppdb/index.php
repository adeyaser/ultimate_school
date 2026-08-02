<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill text-primary me-2"></i> Kelola PPDB Online</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">PPDB Online</li>
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



      <!-- PPDB Table Card -->
      <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
          <h5 class="fw-bold text-primary mb-0"><i class="bi bi-list-stars me-2"></i> Pendaftar Calon Murid Baru (PPDB)</h5>
          <span class="badge text-bg-secondary px-3 py-2 fs-6">Total Pendaftar: <?= count($ppdb_list) ?></span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive p-3">
            <table id="tablePpdb" class="table table-hover table-striped align-middle w-100 m-0">
              <thead class="table-dark">
                <tr>
                  <th style="width: 40px;">No</th>
                  <th>No Reg</th>
                  <th>Nama Calon Siswa</th>
                  <th class="text-center">Jenjang</th>
                  <th>JK</th>
                  <th>Asal Sekolah</th>
                  <th>No Telepon</th>
                  <th class="text-center">Status</th>
                  <th style="width: 140px;" class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; foreach ($ppdb_list as $p): ?>
                  <?php 
                    $j_badge = ($p['jenjang'] === 'SD') ? 'text-bg-success' : (($p['jenjang'] === 'SMP') ? 'text-bg-info text-white' : (($p['jenjang'] === 'SMA') ? 'text-bg-warning text-dark' : 'text-bg-secondary'));
                  ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><span class="badge text-bg-dark font-monospace"><?= htmlspecialchars($p['no_pendaftaran']) ?></span></td>
                    <td class="fw-bold text-dark"><?= htmlspecialchars($p['nama_lengkap']) ?></td>
                    <td class="text-center"><span class="badge <?= $j_badge ?> px-3 py-1 fs-6"><?= htmlspecialchars($p['jenjang']) ?></span></td>
                    <td><?= htmlspecialchars($p['jenis_kelamin']) ?></td>
                    <td><?= htmlspecialchars($p['asal_sekolah'] ? $p['asal_sekolah'] : '-') ?></td>
                    <td><?= htmlspecialchars($p['telepon'] ? $p['telepon'] : '-') ?></td>
                    <td class="text-center">
                      <?php if ($p['status'] === 'Daftar'): ?>
                        <span class="badge text-bg-warning px-3 py-1 fs-6">Pending</span>
                      <?php elseif ($p['status'] === 'Lulus' || $p['status'] === 'Daftar Ulang'): ?>
                        <span class="badge text-bg-success px-3 py-1 fs-6"><?= htmlspecialchars($p['status']) ?></span>
                      <?php else: ?>
                        <span class="badge text-bg-danger px-3 py-1 fs-6"><?= htmlspecialchars($p['status']) ?></span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-info text-white fw-bold" onclick="openApprovalModal(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nama_lengkap'])) ?>')">
                        <i class="bi bi-pencil-square me-1"></i> Status
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

<!-- Modal Approval PPDB -->
<div class="modal fade" id="modalApprovalPPDB" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <form id="formApproval" action="" method="post">
        <div class="modal-header bg-primary text-white p-3">
          <h5 class="modal-title fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Update Status Calon Siswa</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <p class="mb-3 fs-6">Calon Siswa: <strong id="ppdb_nama" class="text-primary"></strong></p>
          <div class="mb-3">
            <label class="form-label fw-bold">Status Hasil Seleksi PPDB *</label>
            <select name="status" class="form-select fw-bold" required>
              <option value="Daftar">Daftar (Belum Diseleksi)</option>
              <option value="Seleksi">Proses Seleksi</option>
              <option value="Lulus">LULUS (Diterima)</option>
              <option value="Daftar Ulang">Daftar Ulang (Konfirmasi Siswa)</option>
              <option value="Tidak Lulus">TIDAK LULUS</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Penempatan Kelas / Rombel (Jika Lulus)</label>
            <select name="kelas_id" class="form-select">
              <?php foreach ($kelas_list as $k): ?>
                <option value="<?= $k['id'] ?>"><?= $k['nama_kelas'] ?> (Tingkat <?= $k['tingkat'] ?> - <?= $k['jenjang'] ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Catatan Panitia PPDB</label>
            <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan Tambahan Panitia..."></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-save me-1"></i> Simpan Status</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openApprovalModal(id, nama) {
  document.getElementById('ppdb_nama').innerText = nama;
  document.getElementById('formApproval').action = '<?= base_url('ppdbadmin/update_status/') ?>' + id;
  var modal = new bootstrap.Modal(document.getElementById('modalApprovalPPDB'));
  modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    if ($('#tablePpdb').length) {
      var table = $('#tablePpdb').DataTable({
        language: {
          search: "Cari Pendaftar PPDB:",
          lengthMenu: "Tampilkan _MENU_ data",
          info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pendaftar",
          infoEmpty: "Tidak ada data pendaftar",
          infoFiltered: "(disaring dari _MAX_ total pendaftar)",
          zeroRecords: "Data pendaftar PPDB tidak ditemukan",
          paginate: {
            first: "Awal",
            last: "Akhir",
            next: "Selanjutnya",
            previous: "Sebelumnya"
          }
        },
        columnDefs: [
          { targets: [0, 8], orderable: false }
        ]
      });

      <?php if (!empty($selected_jenjang) && $selected_jenjang !== 'ALL'): ?>
        table.column(3).search('^<?= $selected_jenjang ?>$', true, false).draw();
      <?php endif; ?>
    }
  }
});
</script>
