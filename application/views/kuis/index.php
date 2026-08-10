<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Latihan Soal Mandiri & Smart Suggestion</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Latihan Soal</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $this->session->flashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- Generator Card -->
        <div class="col-lg-6">
          <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-success text-white p-3 rounded-top-4">
              <h4 class="fw-bold mb-0"><i class="bi bi-magic me-2"></i> Kuis Latihan Adaptif (Google Forms Style)</h4>
            </div>
            <div class="card-body p-4">
              <p class="text-secondary mb-4">Pilih Mata Pelajaran untuk memulai sesi latihan soal interaktif dari repositori bank soal sekolah. Sistem secara otomatis memberikan soal sesuai tingkat kemampuan Anda.</p>

              <form action="<?= base_url('kuislatihan/mulai') ?>" method="post">
                <div class="mb-3">
                  <label class="form-label fw-bold">Mata Pelajaran *</label>
                  <select name="mata_pelajaran_id" class="form-select form-select-lg" required>
                    <?php foreach ($mapel_list as $m): ?>
                      <option value="<?= $m['id'] ?>"><?= $m['kode_mapel'] ?> - <?= $m['nama_mapel'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="form-label fw-bold">Jumlah Soal</label>
                  <select name="jumlah_soal" class="form-select form-select-lg">
                    <option value="5">5 Soal (Kuis Singkat)</option>
                    <option value="10" selected>10 Soal (Standar)</option>
                    <option value="15">15 Soal (Pendalaman)</option>
                  </select>
                </div>
                <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">
                  <i class="bi bi-play-fill me-1"></i> Mulai Latihan Sekarang
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- History Card -->
        <div class="col-lg-6">
          <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white p-3 fw-bold text-primary border-bottom">
              <i class="bi bi-clock-history me-2"></i> Riwayat Latihan Soal Saya
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                  <thead class="table-light">
                    <tr>
                      <th>Tgl Selesai</th>
                      <th>Mata Pelajaran</th>
                      <th>Benar/Salah</th>
                      <th>Skor Nilai</th>
                      <th class="text-end">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($history)): ?>
                      <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat kuis latihan.</td></tr>
                    <?php else: ?>
                      <?php foreach ($history as $h): ?>
                        <tr>
                          <td><small><?= date('d M Y H:i', strtotime($h['tanggal_mulai'])) ?></small></td>
                          <td class="fw-bold"><?= isset($h['nama_mapel']) && $h['nama_mapel'] ? $h['nama_mapel'] : 'Mapel #' . $h['mata_pelajaran_id'] ?></td>
                          <td><span class="text-success fw-bold"><?= $h['jawaban_benar'] ?></span> / <span class="text-danger fw-bold"><?= $h['jawaban_salah'] ?></span></td>
                          <td><span class="badge text-bg-primary fs-6"><?= $h['nilai'] ?></span></td>
                          <td class="text-end">
                            <a href="<?= base_url('kuislatihan/hasil/' . $h['id']) ?>" class="btn btn-sm btn-outline-primary fw-bold">Pembahasan</a>
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
  </div>
</main>
