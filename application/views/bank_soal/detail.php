<!-- MathJax for rendering Mathematical LaTeX equations -->
<script>
MathJax = {
  tex: {
    inlineMath: [['$', '$'], ['\\(', '\\)']],
    displayMath: [['$$', '$$'], ['\\[', '\\]']]
  },
  svg: {
    fontCache: 'global'
  }
};
</script>
<script type="text/javascript" id="MathJax-script" async
  src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
</script>

<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Kelola Detail Soal Ujian</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="<?= base_url('banksoal') ?>">Bank Soal</a></li><li class="breadcrumb-item active">Detail Soal</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle-fill me-2"></i><?= $this->session->flashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <div class="card border-0 shadow-sm mb-4 bg-primary text-white rounded-4">
        <div class="card-body p-4">
          <div class="row align-items-center g-3">
            <div class="col-lg-5">
              <span class="badge text-bg-warning fs-6 mb-2"><i class="bi bi-qr-code me-1"></i> <?= $bank_soal['kode_soal'] ?></span>
              <h3 class="fw-bold mb-1 text-white"><?= $bank_soal['judul'] ?></h3>
              <p class="mb-0 text-white opacity-90 small"><i class="bi bi-book me-1"></i> Mata Pelajaran: <?= $bank_soal['nama_mapel'] ?> | Kelas: <?= $bank_soal['nama_kelas'] ?> | Total: <?= $bank_soal['jumlah_soal'] ?> Soal</p>
            </div>
            <div class="col-lg-7">
              <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                <a href="<?= base_url('banksoal/export_word/' . $bank_soal['id']) ?>" class="btn btn-light btn-md fw-bold text-dark shadow-sm rounded-pill border-0 px-3">
                  <i class="bi bi-file-earmark-word-fill me-1 text-info fs-6"></i> Export Word
                </a>
                <button class="btn btn-light btn-md fw-bold text-dark shadow-sm rounded-pill border-0 px-3" data-bs-toggle="modal" data-bs-target="#modalGudangSoal">
                  <i class="bi bi-box-seam-fill me-1 text-success fs-6"></i> Gudang Soal
                </button>
                <button class="btn btn-warning text-dark btn-md fw-bold shadow-sm rounded-pill border-0 px-3" data-bs-toggle="modal" data-bs-target="#modalGenerateAiSoal">
                  <i class="bi bi-stars me-1 text-dark fs-6"></i> Generate AI
                </button>
                <button class="btn btn-light btn-md fw-bold text-dark shadow-sm rounded-pill border-0 px-3" data-bs-toggle="modal" data-bs-target="#modalImportMassalSoal">
                  <i class="bi bi-file-earmark-arrow-up-fill me-1 text-primary fs-6"></i> Import Massal
                </button>
                <button class="btn btn-light btn-md fw-bold text-dark shadow-sm rounded-pill border-0 px-3" data-bs-toggle="modal" data-bs-target="#modalTambahSoalItem">
                  <i class="bi bi-plus-circle-fill me-1 text-warning fs-6"></i> Tambah Soal
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Gudang Soal -->
      <div class="modal fade" id="modalGudangSoal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= base_url('banksoal/import_gudang_soal') ?>" method="POST">
              <input type="hidden" name="bank_soal_id" value="<?= $bank_soal['id'] ?>" />
              <div class="modal-header text-white p-3 bg-success">
                <h5 class="modal-title fw-bold"><i class="bi bi-box-seam-fill me-2"></i> Ambil Soal dari Repositori Gudang Soal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4">
                <div class="alert alert-success d-flex align-items-center mb-3">
                  <i class="bi bi-check-circle-fill me-3 fs-3"></i>
                  <div>
                    <h6 class="fw-bold mb-1">Repositori Gudang Soal Aktif (14.977 Soal Terindeks)</h6>
                    <p class="mb-0 small">Sistem akan secara otomatis menyaring butir soal yang sesuai dengan Mata Pelajaran <strong><?= isset($bank_soal['nama_mapel']) ? $bank_soal['nama_mapel'] : '' ?></strong> (Kelas <strong><?= isset($bank_soal['nama_kelas']) ? $bank_soal['nama_kelas'] : '' ?></strong><?php if (!empty($bank_soal['jenjang'])): ?> / <strong><?= $bank_soal['jenjang'] ?></strong><?php endif; ?>).</p>
                  </div>
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Jumlah Soal yang Ingin Diambil *</label>
                    <select name="jumlah" class="form-select" required>
                      <option value="5">5 Soal</option>
                      <option value="10" selected>10 Soal</option>
                      <option value="15">15 Soal</option>
                      <option value="20">20 Soal</option>
                      <option value="30">30 Soal</option>
                      <option value="50">50 Soal</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold">Mode Pengambilan Soal *</label>
                    <select name="mode" class="form-select" required>
                      <option value="random" selected>Acak (Randomized Selection)</option>
                      <option value="seq">Berurutan (Sequential Selection)</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success fw-bold px-4 rounded-pill">
                  <i class="bi bi-cloud-arrow-down-fill me-1"></i> Impor dari Gudang Soal
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Question List -->
      <div class="row">
        <div class="col-12">
          <?php if (empty($soal_list)): ?>
            <div class="card shadow-sm p-5 text-center text-muted">
              <i class="bi bi-journal-x fs-1 text-warning mb-2"></i>
              <h4>Belum Ada Soal di Paket Ini</h4>
              <p>Klik tombol 'Tambah Soal Baru' untuk menginput butir soal Pilihan Ganda atau Essay.</p>
            </div>
          <?php else: ?>
            <?php foreach ($soal_list as $s): ?>
              <div class="card shadow-sm mb-3 border-start border-4 border-primary">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                  <h5 class="fw-bold mb-0 text-primary">Soal No. <?= $s['nomor_soal'] ?> <span class="badge text-bg-secondary ms-2"><?= $s['jenis'] ?></span></h5>
                  <div>
                    <span class="badge text-bg-info me-2">Bobot: <?= $s['bobot'] ?> Poin</span>
                    <a href="<?= base_url('banksoal/hapus_soal/' . $s['id'] . '/' . $bank_soal['id']) ?>" onclick="return confirm('Hapus soal ini?')" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash-fill"></i> Hapus</a>
                  </div>
                </div>
                <div class="card-body">
                  <p class="fs-5 fw-semibold mb-3"><?= nl2br($s['pertanyaan']) ?></p>
                  
                  <?php if ($s['jenis'] === 'Pilihan Ganda'): ?>
                    <div class="row g-2 mb-3">
                      <div class="col-md-6"><div class="p-2 border rounded <?= (strtoupper($s['kunci_jawaban']) === 'A') ? 'bg-success text-white fw-bold' : 'bg-light' ?>"><strong>A.</strong> <?= $s['pilihan_a'] ?></div></div>
                      <div class="col-md-6"><div class="p-2 border rounded <?= (strtoupper($s['kunci_jawaban']) === 'B') ? 'bg-success text-white fw-bold' : 'bg-light' ?>"><strong>B.</strong> <?= $s['pilihan_b'] ?></div></div>
                      <div class="col-md-6"><div class="p-2 border rounded <?= (strtoupper($s['kunci_jawaban']) === 'C') ? 'bg-success text-white fw-bold' : 'bg-light' ?>"><strong>C.</strong> <?= $s['pilihan_c'] ?></div></div>
                      <div class="col-md-6"><div class="p-2 border rounded <?= (strtoupper($s['kunci_jawaban']) === 'D') ? 'bg-success text-white fw-bold' : 'bg-light' ?>"><strong>D.</strong> <?= $s['pilihan_d'] ?></div></div>
                      <?php if ($s['pilihan_e']): ?>
                        <div class="col-md-6"><div class="p-2 border rounded <?= (strtoupper($s['kunci_jawaban']) === 'E') ? 'bg-success text-white fw-bold' : 'bg-light' ?>"><strong>E.</strong> <?= $s['pilihan_e'] ?></div></div>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>

                  <div class="p-3 bg-light rounded-3 border">
                    <small class="text-success fw-bold d-block mb-1"><i class="bi bi-key-fill me-1"></i> Kunci Jawaban: <?= strtoupper($s['kunci_jawaban']) ?></small>
                    <?php if ($s['pembahasan']): ?>
                      <small class="text-secondary d-block"><strong>Pembahasan:</strong> <?= nl2br($s['pembahasan']) ?></small>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Modal Input Detail Soal (Multi-Item Repeater) -->
<div class="modal fade" id="modalTambahSoalItem" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <form action="<?= base_url('banksoal/simpan_soal_item') ?>" method="post">
        <input type="hidden" name="bank_soal_id" value="<?= $bank_soal['id'] ?>" />
        <div class="modal-header bg-primary text-white p-3">
          <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Tambah Butir Soal Satuan (Bisa Input Banyak Soal)</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
          <div id="containerFormSoal">
            <!-- Item Form Soal #1 -->
            <div class="card card-item-soal border border-primary border-opacity-25 shadow-sm rounded-3 mb-4">
              <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold text-primary"><i class="bi bi-journal-plus me-1"></i> Form Soal #<span class="label-soal-num">1</span></span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-soal" style="display: none;"><i class="bi bi-trash-fill me-1"></i> Hapus Form Soal Ini</button>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipe / Jenis Soal *</label>
                    <select name="jenis[]" class="form-select select-jenis-soal" required>
                      <option value="Pilihan Ganda">Pilihan Ganda</option>
                      <option value="Essay">Essay</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Bobot Poin *</label>
                    <input type="number" name="bobot[]" class="form-control" value="10" required />
                  </div>
                  <div class="col-md-12">
                    <label class="form-label fw-semibold">Pertanyaan / Soal *</label>
                    <textarea name="pertanyaan[]" class="form-control" rows="3" placeholder="Tuliskan pertanyaan soal..." required></textarea>
                  </div>

                  <!-- Container Pilihan Ganda -->
                  <div class="col-12 block-pilihan-ganda">
                    <div class="row g-3">
                      <div class="col-md-12"><h6 class="fw-bold text-primary mb-0"><i class="bi bi-list-check me-1"></i> Pilihan Jawaban (Untuk Pilihan Ganda)</h6></div>
                      <div class="col-md-6"><label class="form-label small mb-1">Pilihan A</label><input type="text" name="pilihan_a[]" class="form-control" placeholder="Jawaban A" /></div>
                      <div class="col-md-6"><label class="form-label small mb-1">Pilihan B</label><input type="text" name="pilihan_b[]" class="form-control" placeholder="Jawaban B" /></div>
                      <div class="col-md-6"><label class="form-label small mb-1">Pilihan C</label><input type="text" name="pilihan_c[]" class="form-control" placeholder="Jawaban C" /></div>
                      <div class="col-md-6"><label class="form-label small mb-1">Pilihan D</label><input type="text" name="pilihan_d[]" class="form-control" placeholder="Jawaban D" /></div>
                      <div class="col-md-6"><label class="form-label small mb-1">Pilihan E (Opsional)</label><input type="text" name="pilihan_e[]" class="form-control" placeholder="Jawaban E" /></div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold text-success small mb-1">Kunci Jawaban Benar / Solusi Essay *</label>
                    <input type="text" name="kunci_jawaban[]" class="form-control fw-bold input-kunci-jawaban" placeholder="Contoh: A atau B atau Kunci Jawaban Essay" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small mb-1">Pembahasan / Catatan Solusi Soal (Opsional)</label>
                    <textarea name="pembahasan[]" class="form-control" rows="1" placeholder="Penjelasan solusi soal..."></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="text-center my-3">
            <button type="button" class="btn btn-outline-primary fw-bold px-4 rounded-pill shadow-sm" id="btnAddFormSoal">
              <i class="bi bi-plus-circle-fill me-1"></i> + Tambah Form Soal Lagi
            </button>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill"><i class="bi bi-save-fill me-1"></i> Simpan Semua Soal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Import Massal / Copas Soal & Kunci -->
<div class="modal fade" id="modalImportMassalSoal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <form action="<?= base_url('banksoal/import_massal') ?>" method="post">
        <input type="hidden" name="bank_soal_id" value="<?= $bank_soal['id'] ?>" />
        <div class="modal-header bg-success text-white p-3">
          <h5 class="modal-title fw-bold"><i class="bi bi-lightning-charge-fill me-2"></i> Fast Copy-Paste / Import Massal Soal & Kunci Jawaban</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          
          <!-- Download & Guide Header Bar -->
          <div class="d-flex flex-wrap justify-content-between align-items-center bg-light p-3 rounded-3 border mb-3 gap-2">
            <div>
              <h6 class="fw-bold text-dark mb-1"><i class="bi bi-journal-bookmark-fill text-success me-1"></i> Standar Penulisan & Format Dokumen Soal</h6>
              <small class="text-secondary">Unduh berkas templat bawaan atau pelajari aturan penulisan di bawah untuk kemudahan impor 1-click.</small>
            </div>
            <div>
              <a href="<?= base_url('banksoal/download_template') ?>" class="btn btn-warning btn-sm fw-bold shadow-sm me-2 text-dark">
                <i class="bi bi-download me-1"></i> Download File Template (.TXT)
              </a>
              <button type="button" class="btn btn-outline-primary btn-sm fw-bold shadow-sm" data-bs-toggle="collapse" data-bs-target="#collapseStandarPenulisan">
                <i class="bi bi-question-circle-fill me-1"></i> Lihat Aturan Penulisan
              </button>
            </div>
          </div>

          <!-- Collapsible Writing Standard Guide -->
          <div class="collapse mb-3" id="collapseStandarPenulisan">
            <div class="card card-body bg-body-tertiary border border-primary border-opacity-25 rounded-3">
              <h6 class="fw-bold text-primary mb-3"><i class="bi bi-check-square-fill me-2"></i> 4 Aturan Utama Standar Penulisan Soal Massal:</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="p-3 bg-white rounded border h-100">
                    <span class="badge text-bg-primary mb-2">1. Format Nomor Soal</span>
                    <p class="small mb-1 text-dark">Setiap butir pertanyaan wajib diawali nomor angka dan titik (contoh: <code>1. </code> atau <code>2. </code>).</p>
                    <code class="d-block bg-light p-2 rounded small text-dark">1. Apa yang dilakukan Rina di pantai?</code>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="p-3 bg-white rounded border h-100">
                    <span class="badge text-bg-success mb-2">2. Format Opsi Jawaban</span>
                    <p class="small mb-1 text-dark">Pilihan opsi jawaban diawali huruf kecil dan titik (contoh: <code>a. </code> <code>b. </code> <code>c. </code> <code>d. </code> <code>e. </code>).</p>
                    <code class="d-block bg-light p-2 rounded small text-dark">a. Berenang<br>b. Membuat istana pasir</code>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="p-3 bg-white rounded border h-100">
                    <span class="badge text-bg-info mb-2">3. Teks Bacaan / Wacana</span>
                    <p class="small mb-1 text-dark">Teks bacaan/puisi/wacana pendukung dituliskan sebelum nomor soal terkait.</p>
                    <code class="d-block bg-light p-2 rounded small text-dark">Teks Bacaan untuk soal nomor 1 dan 2:<br>Liburan sekolah kali ini...</code>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="p-3 bg-white rounded border h-100">
                    <span class="badge text-bg-warning text-dark mb-2">4. Kunci Jawaban & Pembahasan</span>
                    <p class="small mb-1 text-dark">Dituliskan di bagian paling bawah dengan baris judul <code>KUNCI JAWABAN</code> atau <code>KUNCI JAWABAN + PEMBAHASAN</code>.</p>
                    <code class="d-block bg-light p-2 rounded small text-dark">KUNCI JAWABAN + PEMBAHASAN<br>1  C  Membuat istana pasir<br>2  B  Pagi-pagi sekali</code>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label fw-bold text-dark mb-0"><i class="bi bi-file-text me-1 text-success"></i> Tempelkan Teks Soal & Kunci Jawaban Di Sini *</label>
            <div>
              <button type="button" class="btn btn-sm btn-outline-success fw-bold me-1" id="btnInsertTemplate">
                <i class="bi bi-box-arrow-in-down me-1"></i> Isi Contoh Template Soal
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" id="btnCopyTemplate">
                <i class="bi bi-clipboard-check me-1"></i> Salin Format Template
              </button>
            </div>
          </div>
          <textarea id="rawTextSoal" name="raw_text" class="form-control font-monospace text-dark bg-light" rows="14" style="font-size: 0.88rem;" placeholder="Copas seluruh teks soal dan kunci jawaban di sini... Contoh:&#10;&#10;Teks Bacaan untuk soal nomor 1 dan 2:&#10;Liburan sekolah kali ini...&#10;&#10;1. Apa yang dilakukan Rina?&#10;a. Berenang&#10;b. Membuat istana pasir&#10;c. Tidur&#10;d. Membaca&#10;&#10;KUNCI JAWABAN&#10;1. B (Membuat istana pasir)&#10;2. A" required></textarea>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success fw-bold px-4 rounded-pill"><i class="bi bi-magic me-1"></i> Proses & Import Massal Soal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Generate Soal AI Gemini -->
<div class="modal fade" id="modalGenerateAiSoal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <form id="formGenerateAiSoal">
        <input type="hidden" name="bank_soal_id" value="<?= $bank_soal['id'] ?>" />
        <div class="modal-header text-white p-3" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
          <h5 class="modal-title fw-bold"><i class="bi bi-robot me-2"></i> Generate Soal Otomatis dengan Multi-AI Engine</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div id="aiAlertContainer"></div>

          <div class="alert alert-primary d-flex align-items-center mb-3">
            <i class="bi bi-info-circle-fill me-2 fs-4"></i>
            <div>
              Pembuatan soal dilakukan menggunakan <strong>Multi-AI Engine Pool</strong> (Google Gemini, Groq Llama 3.3, GitHub Models GPT-4o, & OpenRouter) secara otomatis untuk Mata Pelajaran <strong><?= $bank_soal['nama_mapel'] ?></strong> (Kelas <strong><?= $bank_soal['nama_kelas'] ?></strong>).
            </div>
          </div>

          <input type="hidden" name="provider" value="auto" />
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label fw-bold">Topik / Materi Spesifik *</label>
              <input type="text" name="topik" class="form-control" placeholder="Contoh: Photosynthesis, Persamaan Kuadrat, Perang Diponegoro..." required />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold">Jumlah Soal *</label>
              <select name="jumlah" class="form-select" required>
                <option value="5">5 Soal</option>
                <option value="10" selected>10 Soal</option>
                <option value="15">15 Soal</option>
                <option value="20">20 Soal</option>
                <option value="30">30 Soal</option>
                <option value="40">40 Soal</option>
                <option value="50">50 Soal</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold">Jenis Soal *</label>
              <select name="jenis" class="form-select" required>
                <option value="Pilihan Ganda" selected>Pilihan Ganda</option>
                <option value="Essay">Essay</option>
                <option value="Campuran">Campuran (PG & Essay)</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold">Tingkat Kesulitan *</label>
              <select name="tingkat_kesulitan" class="form-select" required>
                <option value="Mudah">Mudah</option>
                <option value="Sedang" selected>Sedang</option>
                <option value="Sulit">Sulit (HOTS)</option>
              </select>
            </div>

            <div class="col-md-12">
              <label class="form-label fw-bold">Instruksi / Catatan Tambahan (Opsional)</label>
              <textarea name="instruksi_tambahan" class="form-control" rows="2" placeholder="Contoh: Fokus pada soal analisis, sertakan contoh kehidupan sehari-hari..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light p-3 d-flex justify-content-between">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" id="btnSubmitAiGenerate" class="btn btn-primary fw-bold px-4 rounded-pill" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border: none;">
            <i class="bi bi-stars me-1"></i> Buat Soal Sekarang
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sampleTemplateText = `Teks Bacaan untuk soal nomor 1 dan 2:
Liburan sekolah kali ini, Rina dan keluarga pergi ke pantai. Mereka berangkat pagi-pagi sekali menggunakan mobil. Di perjalanan, mereka melihat pemandangan sawah yang hijau. Sesampainya di pantai, Rina sangat senang. Ia bermain pasir dan membuat istana pasir yang besar. Ayahnya berenang di laut, sedangkan ibunya duduk di bawah payung sambil membaca buku.

1. Apa yang dilakukan Rina di pantai?
a. Berenang di laut
b. Membaca buku
c. Membuat istana pasir
d. Menyiram sawah

2. Kapan Rina dan keluarganya berangkat ke pantai?
a. Siang hari
b. Pagi-pagi sekali
c. Sore hari
d. Malam hari

3. Perhatikan kalimat berikut!
"Adik menangis karena jatuh dari sepeda."
Kata penghubung yang dicetak tebal (karena) menyatakan hubungan....
a. Waktu
b. Tujuan
c. Sebab-akibat
d. Cara

4. Bacalah puisi pendek di bawah ini!
Pagi ini mentari tersenyum
Burung-burung bernyanyi riang
Udara segar menerpa wajah
Semangat baru datang menyapa

Puisi di atas menggambarkan suasana....
a. Menyedihkan
b. Menakutkan
c. Meriah
d. Gembira

KUNCI JAWABAN + PEMBAHASAN
No	Jawaban	Pembahasan
1	C	Membuat istana pasir
2	B	Pagi-pagi sekali
3	C	Sebab-akibat
4	D	Suasana gembira`;

  const btnInsert = document.getElementById('btnInsertTemplate');
  const btnCopy = document.getElementById('btnCopyTemplate');
  const txtArea = document.getElementById('rawTextSoal');

  if (btnInsert && txtArea) {
    btnInsert.addEventListener('click', function() {
      txtArea.value = sampleTemplateText;
    });
  }

  if (btnCopy && txtArea) {
    btnCopy.addEventListener('click', function() {
      const textToCopy = txtArea.value || sampleTemplateText;
      navigator.clipboard.writeText(textToCopy).then(function() {
        alert('Format template berhasil disalin ke clipboard!');
      });
    });
  }

  // Multi-Item Form Repeater JS Logic
  const btnAddSoal = document.getElementById('btnAddFormSoal');
  const containerSoal = document.getElementById('containerFormSoal');

  if (btnAddSoal && containerSoal) {
    btnAddSoal.addEventListener('click', function() {
      const items = containerSoal.querySelectorAll('.card-item-soal');
      const nextNum = items.length + 1;
      const firstItem = items[0];

      const newItem = firstItem.cloneNode(true);
      newItem.querySelector('.label-soal-num').innerText = nextNum;
      
      // Clear values in cloned inputs
      newItem.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
      newItem.querySelectorAll('textarea').forEach(textarea => textarea.value = '');
      newItem.querySelector('input[type="number"]').value = '10';

      const removeBtn = newItem.querySelector('.btn-remove-soal');
      if (removeBtn) {
        removeBtn.style.display = 'inline-block';
        removeBtn.onclick = function() {
          newItem.remove();
          reindexFormSoal();
        };
      }

      containerSoal.appendChild(newItem);
      reindexFormSoal();
    });
  }

  // Dynamic Toggle Pilihan Ganda vs Essay
  function handleJenisSoalToggle(selectElem) {
    const card = selectElem.closest('.card-item-soal');
    if (!card) return;
    const blockPG = card.querySelector('.block-pilihan-ganda');
    const inputKunci = card.querySelector('.input-kunci-jawaban');

    if (selectElem.value === 'Essay') {
      if (blockPG) blockPG.style.display = 'none';
      if (inputKunci) inputKunci.placeholder = 'Tuliskan Kunci / Kata Kunci Jawaban Essay';
    } else {
      if (blockPG) blockPG.style.display = 'block';
      if (inputKunci) inputKunci.placeholder = 'Contoh: A atau B atau C';
    }
  }

  // Event Delegation for Jenis Soal Change
  document.addEventListener('change', function(e) {
    if (e.target && e.target.classList.contains('select-jenis-soal')) {
      handleJenisSoalToggle(e.target);
    }
  });

  function reindexFormSoal() {
    if (!containerSoal) return;
    const items = containerSoal.querySelectorAll('.card-item-soal');
    items.forEach((item, index) => {
      item.querySelector('.label-soal-num').innerText = index + 1;
      const removeBtn = item.querySelector('.btn-remove-soal');
      if (removeBtn) {
        removeBtn.style.display = (items.length > 1) ? 'inline-block' : 'none';
        removeBtn.onclick = function() {
          item.remove();
          reindexFormSoal();
        };
      }

      const selectJenis = item.querySelector('.select-jenis-soal');
      if (selectJenis) {
        handleJenisSoalToggle(selectJenis);
      }
    });
  }

  // Handle AI Generate Form Submit
  const formAi = document.getElementById('formGenerateAiSoal');
  const btnSubmitAi = document.getElementById('btnSubmitAiGenerate');
  const aiAlert = document.getElementById('aiAlertContainer');

  if (formAi) {
    formAi.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const originalBtnHtml = btnSubmitAi.innerHTML;
      btnSubmitAi.disabled = true;
      btnSubmitAi.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Sedang Menyusun Soal...';

      const steps = [
        "Menghubungkan ke server AI Google Gemini...",
        "Menganalisis topik, kurikulum, dan tingkat kesulitan...",
        "Menyusun butir-butir pertanyaan & opsi jawaban...",
        "Memvalidasi kunci jawaban dan menuliskan pembahasan...",
        "Menyimpan soal-soal baru secara otomatis ke Bank Soal..."
      ];
      let currentStep = 0;
      let progressVal = 12;

      aiAlert.innerHTML = `
        <div class="card border-0 shadow-sm mb-3 text-white overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #9333ea 100%); border-radius: 1rem;">
          <div class="card-body p-4 text-center">
            <div class="mb-3">
              <div class="d-inline-flex p-3 rounded-circle bg-white bg-opacity-25 shadow-lg position-relative" style="animation: pulseAiGlow 1.8s infinite;">
                <i class="bi bi-robot fs-1 text-white"></i>
              </div>
            </div>
            <h5 class="fw-bold mb-2 text-white"><i class="bi bi-stars me-2 text-warning"></i> Multi-AI Engine Sedang Memproses & Menyusun Soal...</h5>
            
            <div class="d-flex justify-content-center flex-wrap gap-2 my-3">
              <span class="badge bg-white bg-opacity-25 text-white fw-semibold px-3 py-2 border border-white border-opacity-25 rounded-pill"><i class="bi bi-cpu-fill me-1 text-warning"></i> Google Gemini Direct</span>
              <span class="badge bg-white bg-opacity-25 text-white fw-semibold px-3 py-2 border border-white border-opacity-25 rounded-pill"><i class="bi bi-lightning-charge-fill me-1 text-warning"></i> Groq Llama 3.3 70B</span>
              <span class="badge bg-white bg-opacity-25 text-white fw-semibold px-3 py-2 border border-white border-opacity-25 rounded-pill"><i class="bi bi-github me-1 text-warning"></i> GitHub Models GPT-4o</span>
              <span class="badge bg-white bg-opacity-25 text-white fw-semibold px-3 py-2 border border-white border-opacity-25 rounded-pill"><i class="bi bi-layers-fill me-1 text-warning"></i> OpenRouter Cascade</span>
            </div>

            <p id="aiStepText" class="small text-white-50 fw-semibold mb-3">${steps[0]}</p>
            
            <div class="progress mb-2 rounded-pill shadow-sm" style="height: 14px; background: rgba(255,255,255,0.2);">
              <div id="aiProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning rounded-pill" role="progressbar" style="width: 12%;"></div>
            </div>
            <small class="text-white opacity-75 d-block mt-2"><i class="bi bi-hourglass-split me-1"></i> Mohon tunggu, pembuatan hingga 50 soal memerlukan waktu beberapa detik.</small>
          </div>
        </div>
        <style>
          @keyframes pulseAiGlow {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
            70% { transform: scale(1.08); box-shadow: 0 0 0 16px rgba(255, 255, 255, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
          }
        </style>
      `;

      const progressInterval = setInterval(() => {
        progressVal = Math.min(progressVal + Math.floor(Math.random() * 8) + 4, 94);
        const pb = document.getElementById('aiProgressBar');
        if (pb) pb.style.width = progressVal + '%';

        if (progressVal > 25 && currentStep === 0) currentStep = 1;
        if (progressVal > 50 && currentStep === 1) currentStep = 2;
        if (progressVal > 72 && currentStep === 2) currentStep = 3;
        if (progressVal > 86 && currentStep === 3) currentStep = 4;

        const txt = document.getElementById('aiStepText');
        if (txt) txt.innerText = steps[currentStep];
      }, 1500);

      const formData = new FormData(formAi);

      fetch('<?= base_url("banksoal/generate_ai_soal") ?>', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        clearInterval(progressInterval);
        btnSubmitAi.disabled = false;
        btnSubmitAi.innerHTML = originalBtnHtml;

        if (data.status === 'success') {
          const pb = document.getElementById('aiProgressBar');
          if (pb) pb.style.width = '100%';

          aiAlert.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>' + data.message + ' Memuat ulang halaman...</div>';
          setTimeout(() => {
            window.location.reload();
          }, 1200);
        } else {
          aiAlert.innerHTML = '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill me-2"></i>' + data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
      })
      .catch(err => {
        clearInterval(progressInterval);
        btnSubmitAi.disabled = false;
        btnSubmitAi.innerHTML = originalBtnHtml;
        aiAlert.innerHTML = '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan jaringan atau server.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        console.error(err);
      });
    });
  }
});
</script>

