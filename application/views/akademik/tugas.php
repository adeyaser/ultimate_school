<?php
  $user_role = isset($user_data['role']) ? $user_data['role'] : 'super_admin';
  $is_admin_or_teacher = in_array($user_role, array('super_admin', 'admin', 'guru', 'wali_kelas', 'kepala_sekolah'));
?>
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0 fw-bold">
            <i class="bi bi-file-earmark-text-fill text-primary me-2"></i> 
            <?= $is_admin_or_teacher ? 'Kelola & Terbitkan Tugas Siswa' : 'Tugas & PR Saya (Portal Siswa)' ?>
          </h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Tugas & PR</li>
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
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-3" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $this->session->flashdata('error') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Filter & Quick Action Card (For Admin & Teachers) -->
      <?php if ($is_admin_or_teacher): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-4">
          <div class="card-body p-3">
            <form action="<?= base_url('akademik/tugas') ?>" method="get" class="row g-3 align-items-end">
              <div class="col-md-4">
                <label class="form-label fw-bold text-primary mb-1"><i class="bi bi-door-open-fill me-1"></i> Filter Kelas / Rombel</label>
                <select name="kelas_id" class="form-select fw-bold border-primary py-2" onchange="this.form.submit()">
                  <option value="">-- Semua Kelas --</option>
                  <?php foreach ($kelas_list as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= (isset($selected_kelas) && $selected_kelas == $k['id']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold text-primary mb-1"><i class="bi bi-book-fill me-1"></i> Filter Mata Pelajaran</label>
                <select name="mata_pelajaran_id" class="form-select fw-bold border-primary py-2" onchange="this.form.submit()">
                  <option value="">-- Semua Mapel --</option>
                  <?php foreach ($mapel_list as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= (isset($selected_mapel) && $selected_mapel == $m['id']) ? 'selected' : '' ?>><?= $m['nama_mapel'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4 text-end">
                <button type="button" class="btn btn-success fw-bold px-4 py-2 w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTugas">
                  <i class="bi bi-plus-lg me-1"></i> Buat & Terbitkan Tugas Baru
                </button>
              </div>
            </form>
          </div>
        </div>
      <?php endif; ?>

      <!-- Header & Table Card -->
      <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h5 class="fw-bold text-primary mb-0">
              <i class="bi bi-list-task me-2"></i> 
              <?= $is_admin_or_teacher ? 'Daftar Seluruh Tugas Terpublikasi' : 'Daftar Tugas & PR Kelas Anda' ?>
            </h5>
            <small class="text-muted"><?= $is_admin_or_teacher ? 'Kelola dan lihat rincian pengumpulan tugas siswa' : 'Kumpulkan jawaban tugas Anda sebelum batas waktu deadline' ?></small>
          </div>
          <span class="badge text-bg-secondary px-3 py-2 fs-6 rounded-pill">Total: <?= count($tugas_list) ?> Tugas</span>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive p-3">
            <table id="tableTugas" class="table table-hover table-striped align-middle w-100 m-0">
              <thead class="table-dark">
                <tr>
                  <th style="width: 50px;">No</th>
                  <th>Judul & Deskripsi Tugas</th>
                  <th>Mata Pelajaran</th>
                  <th>Kelas</th>
                  <th>Guru Pengampu</th>
                  <th>Deadline</th>
                  <th>Bobot</th>
                  <th style="width: 180px;" class="text-center">Aksi Management</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($tugas_list)): ?>
                  <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                      <i class="bi bi-file-earmark-x fs-1 d-block mb-2 opacity-50"></i> Belum ada tugas yang dipublikasikan.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php $no = 1; foreach ($tugas_list as $t): 
                    $is_expired = (strtotime($t['deadline']) < strtotime(date('Y-m-d')));
                    $dl_badge = $is_expired ? 'text-bg-danger' : 'text-bg-warning text-dark';
                  ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td>
                        <strong class="text-dark fs-6 d-block"><?= $t['judul'] ?></strong>
                        <small class="text-muted"><?= $t['deskripsi'] ? htmlspecialchars($t['deskripsi']) : 'Tidak ada instruksi khusus.' ?></small>
                      </td>
                      <td><span class="badge text-bg-primary fs-6"><?= $t['nama_mapel'] ?></span></td>
                      <td><span class="badge text-bg-info text-white fs-6"><?= $t['nama_kelas'] ?></span></td>
                      <td><i class="bi bi-person-circle text-success me-1"></i> <?= $t['nama_guru'] ? $t['nama_guru'] : 'Guru Pengampu' ?></td>
                      <td>
                        <span class="badge <?= $dl_badge ?> px-3 py-1 fs-6">
                          <i class="bi bi-clock me-1"></i> <?= date('d M Y', strtotime($t['deadline'])) ?>
                        </span>
                      </td>
                      <td><span class="badge text-bg-secondary fs-6"><?= $t['bobot'] ?> Poin</span></td>

                      <td class="text-center">
                        <?php if ($is_admin_or_teacher): ?>
                          <div class="btn-group" role="group">
                            <a href="<?= base_url('akademik/lihat_pengumpulan/' . $t['id']) ?>" class="btn btn-sm btn-success" title="Lihat Pengumpulan">
                              <i class="bi bi-people-fill me-1"></i> Pengumpulan
                            </a>
                            <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalDetailTugas_<?= $t['id'] ?>" title="Lihat Detail">
                              <i class="bi bi-eye-fill"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalEditTugas_<?= $t['id'] ?>" title="Edit Tugas">
                              <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="<?= base_url('akademik/hapus_tugas/' . $t['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')" title="Hapus Tugas">
                              <i class="bi bi-trash-fill"></i>
                            </a>
                          </div>
                        <?php else: ?>
                          <?php $submitted = isset($my_submissions[$t['id']]); ?>
                          <?php if ($submitted): ?>
                            <?php $sub = $my_submissions[$t['id']]; ?>
                            <?php if ($sub['status'] == 'Dinilai'): ?>
                              <span class="badge text-bg-success px-3 py-2 fs-6"><i class="bi bi-check-circle-fill me-1"></i> Dinilai: <?= $sub['nilai'] ?></span>
                            <?php else: ?>
                              <span class="badge text-bg-info text-white px-3 py-2 fs-6"><i class="bi bi-hourglass-split me-1"></i> Menunggu Penilaian</span>
                            <?php endif; ?>
                          <?php else: ?>
                            <button type="button" class="btn btn-sm btn-success fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSubmitTugas_<?= $t['id'] ?>">
                              <i class="bi bi-upload me-1"></i> Kumpulkan Tugas
                            </button>
                          <?php endif; ?>
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

<!-- TinyMCE Rich Text Editor CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
  function initTinyMCE(container) {
    var textareas = container.querySelectorAll('.tinymce-editor');
    if (textareas.length === 0) return;
    textareas.forEach(function(ta) {
      // Remove any existing TinyMCE instance on this textarea first
      var existingEditor = tinymce.get(ta.id);
      if (existingEditor) existingEditor.remove();
    });
    tinymce.init({
      target: textareas[0],
      height: 250,
      menubar: false,
      plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'table', 'help', 'wordcount'],
      toolbar: 'undo redo | blocks | bold italic underline backcolor forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link image | removeformat code fullscreen',
      content_style: 'body { font-family:system-ui,-apple-system,sans-serif; font-size:14px; line-height:1.6 }',
      setup: function (editor) {
        editor.on('change', function () { editor.save(); });
        editor.on('keyup', function () { editor.save(); });
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    // Init TinyMCE inside Bootstrap modals when they are shown
    document.querySelectorAll('.modal').forEach(function(modal) {
      modal.addEventListener('shown.bs.modal', function() {
        initTinyMCE(modal);
      });
      // Cleanup TinyMCE when modal closes to prevent orphaned editors
      modal.addEventListener('hidden.bs.modal', function() {
        modal.querySelectorAll('.tinymce-editor').forEach(function(ta) {
          var editor = tinymce.get(ta.id);
          if (editor) editor.remove();
        });
      });
    });
  });
</script>

<!-- Modals Detail & Edit for Teachers -->
<?php if ($is_admin_or_teacher && !empty($tugas_list)): ?>
  <?php foreach ($tugas_list as $t): ?>
    <!-- Modal Detail Tugas -->
    <div class="modal fade" id="modalDetailTugas_<?= $t['id'] ?>" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header bg-info text-white p-3">
            <h5 class="modal-title fw-bold"><i class="bi bi-info-circle-fill me-2"></i> Rincian Tugas: <?= $t['judul'] ?></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <small class="text-muted fw-bold d-block">MATA PELAJARAN</small>
                <span class="fs-6 fw-bold text-primary"><?= $t['nama_mapel'] ?></span>
              </div>
              <div class="col-md-6">
                <small class="text-muted fw-bold d-block">KELAS TARGET</small>
                <span class="fs-6 fw-bold text-dark"><?= $t['nama_kelas'] ?></span>
              </div>
              <div class="col-md-6">
                <small class="text-muted fw-bold d-block">DEADLINE PENGUMPULAN</small>
                <span class="badge text-bg-warning text-dark fs-6"><?= date('d M Y', strtotime($t['deadline'])) ?></span>
              </div>
              <div class="col-md-6">
                <small class="text-muted fw-bold d-block">BOBOT POIN</small>
                <span class="badge text-bg-secondary fs-6"><?= $t['bobot'] ?> Poin</span>
              </div>
            </div>

            <hr>

            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-card-text me-2"></i> Instruksi & Deskripsi Tugas (TinyMCE Rich Text):</h6>
            <div class="p-4 bg-light rounded-4 border mb-3 text-dark">
              <?= $t['deskripsi'] ? $t['deskripsi'] : '<em>Tidak ada instruksi khusus.</em>' ?>
            </div>
          </div>
          <div class="modal-footer bg-light p-3">
            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Edit Tugas -->
    <div class="modal fade" id="modalEditTugas_<?= $t['id'] ?>" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <form action="<?= base_url('akademik/simpan_tugas') ?>" method="post">
            <input type="hidden" name="id" value="<?= $t['id'] ?>" />
            <div class="modal-header bg-warning text-dark p-3">
              <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Tugas: <?= $t['judul'] ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
              <div class="row g-3">
                <div class="col-md-12">
                  <label class="form-label fw-bold">Judul Tugas *</label>
                  <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($t['judul']) ?>" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Mata Pelajaran *</label>
                  <select name="mata_pelajaran_id" class="form-select" required>
                    <?php foreach ($mapel_list as $m): ?>
                      <option value="<?= $m['id'] ?>" <?= ($t['mata_pelajaran_id'] == $m['id']) ? 'selected' : '' ?>><?= $m['nama_mapel'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Kelas Target *</label>
                  <select name="kelas_id" class="form-select" required>
                    <?php foreach ($kelas_list as $k): ?>
                      <option value="<?= $k['id'] ?>" <?= ($t['kelas_id'] == $k['id']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Deadline Pengumpulan *</label>
                  <input type="date" name="deadline" class="form-control" value="<?= $t['deadline'] ?>" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Bobot Poin</label>
                  <input type="number" name="bobot" class="form-control" value="<?= $t['bobot'] ?>" />
                </div>
                <div class="col-md-12">
                  <label class="form-label fw-bold">Deskripsi / Instruksi Tugas (TinyMCE Rich Text Editor)</label>
                  <textarea name="deskripsi" id="edit_deskripsi_<?= $t['id'] ?>" class="form-control tinymce-editor" rows="5"><?= htmlspecialchars($t['deskripsi']) ?></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer bg-light p-3">
              <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold"><i class="bi bi-save me-1"></i> Perbarui Tugas</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Modal Upload Jawaban (For Students) -->
<?php if (!$is_admin_or_teacher && !empty($tugas_list)): ?>
  <?php foreach ($tugas_list as $t): ?>
    <?php if (!isset($my_submissions[$t['id']])): ?>
    <div class="modal fade" id="modalSubmitTugas_<?= $t['id'] ?>" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <form action="<?= base_url('akademik/submit_tugas') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="tugas_id" value="<?= $t['id'] ?>" />
            <div class="modal-header bg-success text-white p-3">
              <h5 class="modal-title fw-bold"><i class="bi bi-upload me-2"></i> Form Pengumpulan Tugas</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
              <div class="alert alert-info border-0 rounded-3 mb-3">
                <strong class="d-block"><?= $t['judul'] ?></strong>
                <small>Mapel: <?= $t['nama_mapel'] ?> | Deadline: <?= date('d M Y', strtotime($t['deadline'])) ?></small>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Catatan / Teks Jawaban *</label>
                <textarea name="catatan_jawaban" class="form-control" rows="3" placeholder="Tuliskan ringkasan jawaban atau catatan tugas Anda di sini..." required></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Unggah Dokumen / Lampiran Jawaban (Opsional)</label>
                <input type="file" name="file_jawaban" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png,.zip" />
                <small class="text-muted">Format yang didukung: PDF, DOC, DOCX, JPG, PNG, ZIP (Maks 10 MB)</small>
              </div>
            </div>
            <div class="modal-footer bg-light p-3">
              <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold"><i class="bi bi-send-fill me-1"></i> Kirim Jawaban Tugas</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Modal Tambah Tugas (For Admin & Teachers) -->
<?php if ($is_admin_or_teacher): ?>
<div class="modal fade" id="modalTambahTugas" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <form action="<?= base_url('akademik/simpan_tugas') ?>" method="post">
        <div class="modal-header bg-primary text-white p-3">
          <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill me-2"></i> Buat & Terbitkan Tugas Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label fw-bold">Judul Tugas *</label>
              <input type="text" name="judul" class="form-control" placeholder="Contoh: Tugas 1 - Persamaan Kuadrat" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Mata Pelajaran *</label>
              <select name="mata_pelajaran_id" class="form-select" required>
                <option value="">-- Pilih Mapel --</option>
                <?php foreach ($mapel_list as $m): ?>
                  <option value="<?= $m['id'] ?>"><?= $m['nama_mapel'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Kelas Target *</label>
              <select name="kelas_id" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas_list as $k): ?>
                  <option value="<?= $k['id'] ?>"><?= $k['nama_kelas'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Deadline Pengumpulan *</label>
              <input type="date" name="deadline" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Bobot Poin</label>
              <input type="number" name="bobot" class="form-control" value="10" />
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Deskripsi / Instruksi Tugas (TinyMCE Rich Text Editor)</label>
              <textarea name="deskripsi" id="tambah_deskripsi" class="form-control tinymce-editor" rows="5" placeholder="Instruksi pengerjaan tugas..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="bi bi-send me-1"></i> Publikasikan Tugas</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    var selectedKelas = "<?= isset($selected_kelas) ? $selected_kelas : '' ?>";
    var selectedMapel = "<?= isset($selected_mapel) ? $selected_mapel : '' ?>";

    $('#tableTugas').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?= base_url('akademik/tugas_json') ?>",
        type: "GET",
        data: function(d) {
          d.kelas_id = selectedKelas;
          d.mata_pelajaran_id = selectedMapel;
        }
      },
      language: {
        search: "Cari Tugas:",
        lengthMenu: "Tampilkan _MENU_ data per halaman",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ tugas",
        infoEmpty: "Tidak ada data tugas",
        infoFiltered: "(disaring dari _MAX_ total tugas)",
        zeroRecords: "Data tugas tidak ditemukan",
        paginate: {
          first: "Awal",
          last: "Akhir",
          next: "Selanjutnya",
          previous: "Sebelumnya"
        }
      },
      columnDefs: [
        { targets: [0, 6, 7], orderable: false },
        { targets: [0, 5, 6, 7], className: "text-center" }
      ],
      order: [[0, 'desc']]
    });
  }
});
</script>
