<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Pengolahan Nilai Siswa</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Nilai</li></ol></div>
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
          <form action="<?= base_url('akademik/nilai') ?>" method="get" class="row g-3 align-items-end">
            <div class="col-md-5">
              <label class="form-label fw-bold">Pilih Kelas:</label>
              <select name="kelas_id" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas_list as $k): ?>
                  <option value="<?= $k['id'] ?>" <?= ($selected_kelas == $k['id']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-5">
              <label class="form-label fw-bold">Pilih Mata Pelajaran:</label>
              <select name="mata_pelajaran_id" class="form-select" required>
                <option value="">-- Pilih Mapel --</option>
                <?php foreach ($mapel_list as $m): ?>
                  <option value="<?= $m['id'] ?>" <?= ($selected_mapel == $m['id']) ? 'selected' : '' ?>><?= $m['nama_mapel'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary fw-bold w-100"><i class="bi bi-search me-1"></i> Muat Form</button>
            </div>
          </form>
        </div>
      </div>

      <?php if ($selected_kelas && $selected_mapel): ?>
        <form action="<?= base_url('akademik/simpan_nilai') ?>" method="post">
          <input type="hidden" name="kelas_id" value="<?= $selected_kelas ?>" />
          <input type="hidden" name="mata_pelajaran_id" value="<?= $selected_mapel ?>" />

          <div class="card shadow-sm mb-4">
            <div class="card-header border-0 d-flex justify-content-between align-items-center bg-white">
              <h4 class="card-title fw-bold text-primary mb-0"><i class="bi bi-calculator-fill me-2"></i> Input Nilai (Harian, Tugas, PTS, PAS)</h4>
              <button type="submit" class="btn btn-success fw-bold px-4"><i class="bi bi-calculator me-1"></i> Hitung & Simpan Nilai</button>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                  <thead class="table-light">
                    <tr>
                      <th width="5%">No</th>
                      <th>Nama Siswa</th>
                      <th width="15%">Nilai Harian (20%)</th>
                      <th width="15%">Nilai Tugas (20%)</th>
                      <th width="15%">PTS (30%)</th>
                      <th width="15%">PAS (30%)</th>
                      <th>Nilai Akhir</th>
                      <th>Predikat</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($nilai_list)): ?>
                      <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada siswa di kelas ini.</td></tr>
                    <?php else: ?>
                      <?php foreach ($nilai_list as $idx => $n): ?>
                        <tr>
                          <td><?= $idx + 1 ?></td>
                          <td class="fw-bold"><?= $n['full_name'] ?></td>
                          <td><input type="number" step="0.01" name="nilai_harian[<?= $n['murid_id'] ?>]" class="form-control form-control-sm" value="<?= isset($n['nilai_harian']) ? $n['nilai_harian'] : '0' ?>" /></td>
                          <td><input type="number" step="0.01" name="nilai_tugas[<?= $n['murid_id'] ?>]" class="form-control form-control-sm" value="<?= isset($n['nilai_tugas']) ? $n['nilai_tugas'] : '0' ?>" /></td>
                          <td><input type="number" step="0.01" name="nilai_pts[<?= $n['murid_id'] ?>]" class="form-control form-control-sm" value="<?= isset($n['nilai_pts']) ? $n['nilai_pts'] : '0' ?>" /></td>
                          <td><input type="number" step="0.01" name="nilai_pas[<?= $n['murid_id'] ?>]" class="form-control form-control-sm" value="<?= isset($n['nilai_pas']) ? $n['nilai_pas'] : '0' ?>" /></td>
                          <td><strong class="text-primary fs-6"><?= isset($n['nilai_akhir']) ? $n['nilai_akhir'] : '-' ?></strong></td>
                          <td>
                            <?php if (isset($n['predikat'])): ?>
                              <span class="badge text-bg-<?= ($n['predikat'] === 'A' || $n['predikat'] === 'B') ? 'success' : 'warning' ?>"><?= $n['predikat'] ?></span>
                            <?php else: ?>
                              -
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer text-end bg-transparent p-3">
              <button type="submit" class="btn btn-success btn-lg fw-bold px-4"><i class="bi bi-calculator me-1"></i> Hitung & Simpan Nilai Akhir</button>
            </div>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</main>
