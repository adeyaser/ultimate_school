<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold"><i class="bi bi-controller me-2 text-primary"></i>Latihan Soal Mandiri & Interactive Quiz</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Latihan Soal</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm border-0 rounded-3">
          <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><?= $this->session->flashdata('error') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- Generator & Mode Selection Card -->
        <div class="col-lg-7">
          <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
            <div class="card-header bg-gradient bg-primary text-white p-3">
              <div class="d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0"><i class="bi bi-sliders me-2"></i> Pilih Metode Latihan Soal</h5>
                <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill"><i class="bi bi-lightning-charge-fill me-1"></i> Multi-Engine</span>
              </div>
            </div>
            <div class="card-body p-4">
              <!-- Mode Nav Tabs -->
              <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded-3 gap-2 border" id="quizModeTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active fw-bold py-2 rounded-3" id="gudang-tab" data-bs-toggle="tab" data-bs-target="#gudang-pane" type="button" role="tab">
                    <i class="bi bi-box-seam me-1"></i> Repositori Gudang Soal
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-bold py-2 rounded-3 text-purple" id="ai-tab" data-bs-toggle="tab" data-bs-target="#ai-pane" type="button" role="tab">
                    <i class="bi bi-stars me-1 text-warning"></i> Generate AI (Gemini)
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-bold py-2 rounded-3" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual-pane" type="button" role="tab">
                    <i class="bi bi-pencil-square me-1"></i> Bank Soal & Manual
                  </button>
                </li>
              </ul>

              <div class="tab-content" id="quizModeTabContent">
                <!-- Mode 1: Gudang Soal -->
                <div class="tab-pane fade show active" id="gudang-pane" role="tabpanel">
                  <div class="alert alert-success d-flex align-items-center mb-3 rounded-3 bg-success bg-opacity-10 text-success border-0 p-3">
                    <i class="bi bi-database-check me-3 fs-2"></i>
                    <div>
                      <strong>Terhubung ke Gudang Soal (15.000+ Soal Terindeks)</strong><br>
                      <small class="opacity-75">Diambil dari repositori Kurikulum SD, SMP, SMA nasional secara adaptif.</small>
                    </div>
                  </div>

                  <form action="<?= base_url('kuislatihan/mulai') ?>" method="post">
                    <input type="hidden" name="mode" value="gudang" />
                    <div class="mb-3">
                      <label class="form-label fw-bold">Mata Pelajaran *</label>
                      <select name="mata_pelajaran_id" class="form-select form-select-lg rounded-3" required>
                        <?php foreach ($mapel_list as $m): ?>
                          <option value="<?= $m['id'] ?>"><?= $m['kode_mapel'] ?> - <?= $m['nama_mapel'] ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="mb-4">
                      <label class="form-label fw-bold">Jumlah Soal</label>
                      <select name="jumlah_soal" class="form-select form-select-lg rounded-3">
                        <option value="5">5 Soal (Kuis Singkat - Quick Practice)</option>
                        <option value="10" selected>10 Soal (Standar - Evaluasi)</option>
                        <option value="15">15 Soal (Pendalaman Materi)</option>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm rounded-3">
                      <i class="bi bi-play-circle-fill me-2"></i> Mulai Kuis Gudang Soal
                    </button>
                  </form>
                </div>

                <!-- Mode 2: Generate AI (Gemini AI Engine) -->
                <div class="tab-pane fade" id="ai-pane" role="tabpanel">
                  <div class="alert alert-primary d-flex align-items-center mb-3 rounded-3 bg-primary bg-opacity-10 text-primary border-0 p-3">
                    <i class="bi bi-robot me-3 fs-2"></i>
                    <div>
                      <strong>Multi-Engine AI Quiz Generator</strong><br>
                      <small class="opacity-75">Membuat soal kuis instan secara real-time berdasarkan topik spesifik menggunakan Gemini & Multi-AI Engine.</small>
                    </div>
                  </div>

                  <form action="<?= base_url('kuislatihan/mulai') ?>" method="post">
                    <input type="hidden" name="mode" value="ai" />
                    <div class="row g-3 mb-3">
                      <div class="col-md-6">
                        <label class="form-label fw-bold">Mata Pelajaran *</label>
                        <select name="mata_pelajaran_id" class="form-select" required>
                          <?php foreach ($mapel_list as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= $m['kode_mapel'] ?> - <?= $m['nama_mapel'] ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label fw-bold">Tingkat Kesulitan</label>
                        <select name="tingkat_kesulitan" class="form-select">
                          <option value="Mudah">Mudah (Dasar)</option>
                          <option value="Sedang" selected>Sedang (Menengah)</option>
                          <option value="Sulit">Sulit (HOTS)</option>
                        </select>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-bold">Topik / Materi Spesifik Kuis *</label>
                      <input type="text" name="topik" class="form-control form-control-lg rounded-3" placeholder="Contoh: Passive Voice & Narrative Text, Sel & Fotosintesis, Persamaan Kuadrat..." required />
                      <div class="form-text"><i class="bi bi-info-circle me-1"></i> Tulis topik spesifik yang ingin kamu latih agar AI membuat soal khusus untukmu.</div>
                    </div>

                    <div class="mb-4">
                      <label class="form-label fw-bold">Jumlah Soal AI</label>
                      <select name="jumlah_soal" class="form-select">
                        <option value="5" selected>5 Soal AI (Cepat & Akurat)</option>
                        <option value="10">10 Soal AI (Lengkap)</option>
                        <option value="15">15 Soal AI (Komprehensif)</option>
                      </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm rounded-3">
                      <i class="bi bi-stars me-2"></i> Generate & Mulai Kuis AI
                    </button>
                  </form>
                </div>

                <!-- Mode 3: Bank Soal Kustom / Input Manual -->
                <div class="tab-pane fade" id="manual-pane" role="tabpanel">
                  <div class="alert alert-info d-flex align-items-center mb-3 rounded-3 bg-info bg-opacity-10 text-dark border-0 p-3">
                    <i class="bi bi-journal-plus me-3 fs-2 text-info"></i>
                    <div>
                      <strong>Bank Soal Sekolah & Penambahan Manual</strong><br>
                      <small class="text-secondary">Pilih dari paket Bank Soal buatan Guru/Sekolah, atau buat paket kuis baru dan tambah soal secara manual.</small>
                    </div>
                  </div>

                  <form action="<?= base_url('kuislatihan/mulai') ?>" method="post" class="mb-4">
                    <input type="hidden" name="mode" value="kustom" />
                    <div class="mb-3">
                      <label class="form-label fw-bold">Pilih Paket Bank Soal Terpublikasi *</label>
                      <select name="bank_soal_id" class="form-select form-select-lg rounded-3" required>
                        <?php if (empty($bank_list)): ?>
                          <option value="">-- Belum ada Paket Bank Soal --</option>
                        <?php else: ?>
                          <?php foreach ($bank_list as $b): ?>
                            <option value="<?= $b['id'] ?>">[<?= $b['kode_soal'] ?>] <?= $b['judul'] ?> (<?= $b['nama_mapel'] ?> - <?= $b['nama_kelas'] ?>)</option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                    <div class="mb-4">
                      <label class="form-label fw-bold">Jumlah Soal Dikerjakan</label>
                      <select name="jumlah_soal" class="form-select">
                        <option value="5">5 Soal</option>
                        <option value="10" selected>10 Soal</option>
                        <option value="15">15 Soal</option>
                        <option value="20">20 Soal</option>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-info text-white btn-lg w-100 fw-bold shadow-sm rounded-3 mb-3">
                      <i class="bi bi-play-circle-fill me-2"></i> Mulai Kuis dari Bank Soal Ini
                    </button>
                  </form>

                  <hr class="my-3" />

                  <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                    <div>
                      <h6 class="fw-bold mb-1"><i class="bi bi-plus-circle me-1 text-success"></i> Ingin Buat Paket Soal / Input Soal Sendiri?</h6>
                      <small class="text-secondary">Anda dapat membuat paket kuis baru dan menambahkan butir soal manual (pilihan ganda, kunci jawaban, pembahasan).</small>
                    </div>
                    <a href="<?= base_url('banksoal/tambah') ?>" class="btn btn-outline-primary fw-bold text-nowrap ms-3">
                      <i class="bi bi-pencil-square me-1"></i> + Buat Bank Soal Manual
                    </a>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- History Card -->
        <div class="col-lg-5">
          <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white p-3 fw-bold text-primary border-bottom d-flex align-items-center justify-content-between">
              <span><i class="bi bi-clock-history me-2"></i> Riwayat Latihan Soal Saya</span>
              <span class="badge text-bg-light border text-secondary fw-normal"><?= count($history) ?> Sesi</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                  <thead class="table-light">
                    <tr>
                      <th>Tgl Selesai</th>
                      <th>Mata Pelajaran</th>
                      <th>B/S</th>
                      <th>Skor</th>
                      <th class="text-end">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($history)): ?>
                      <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>Belum ada riwayat kuis latihan.</td></tr>
                    <?php else: ?>
                      <?php foreach ($history as $h): ?>
                        <tr>
                          <td><small class="text-secondary"><?= date('d M H:i', strtotime($h['tanggal_mulai'])) ?></small></td>
                          <td>
                            <div class="fw-bold text-dark"><?= isset($h['nama_mapel']) && $h['nama_mapel'] ? $h['nama_mapel'] : 'Mapel #' . $h['mata_pelajaran_id'] ?></div>
                            <small class="text-muted"><?= $h['jumlah_soal'] ?> Butir Soal</small>
                          </td>
                          <td><span class="text-success fw-bold"><?= $h['jawaban_benar'] ?></span> / <span class="text-danger fw-bold"><?= $h['jawaban_salah'] ?></span></td>
                          <td><span class="badge bg-primary-subtle text-primary fs-6 fw-bold border border-primary-subtle"><?= round($h['nilai']) ?></span></td>
                          <td class="text-end">
                            <a href="<?= base_url('kuislatihan/hasil/' . $h['id']) ?>" class="btn btn-sm btn-outline-primary fw-bold rounded-pill">
                              <i class="bi bi-eye-fill me-1"></i> Hasil
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
    </div>
  </div>
</main>
