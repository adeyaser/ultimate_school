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
                <a href="<?= base_url('banksoal/export_word/' . encrypt_id($bank_soal['id'])) ?>" class="btn btn-light btn-md fw-bold text-dark shadow-sm rounded-pill border-0 px-3">
                  <i class="bi bi-file-earmark-word-fill me-1 text-info fs-6"></i> Export Word
                </a>
                <button class="btn btn-light btn-md fw-bold text-dark shadow-sm rounded-pill border-0 px-3" data-bs-toggle="modal" data-bs-target="#modalGudangSoal">
                  <i class="bi bi-box-seam-fill me-1 text-success fs-6"></i> Gudang Soal
                </button>
                <button class="btn text-white btn-md fw-bold shadow-sm rounded-pill border-0 px-3" data-bs-toggle="modal" data-bs-target="#modalOcrFotoMateri" style="background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);">
                  <i class="bi bi-camera-fill me-1 text-warning fs-6"></i> OCR Foto Materi AI
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
              <input type="hidden" name="bank_soal_id" value="<?= encrypt_id($bank_soal['id']) ?>" />
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

      <!-- Bulk Delete Question Bar & Form -->
      <form action="<?= base_url('banksoal/hapus_massal_soal') ?>" method="POST" id="formHapusMassal">
        <input type="hidden" name="bank_soal_id" value="<?= encrypt_id($bank_soal['id']) ?>" />

        <?php if (!empty($soal_list)): ?>
          <div class="card border-0 shadow-sm mb-3 bg-light rounded-4 border">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div class="d-flex align-items-center">
                <input type="checkbox" id="checkAllSoal" class="form-check-input me-2 fs-5 cursor-pointer">
                <label for="checkAllSoal" class="form-check-label fw-bold cursor-pointer text-dark">
                  Pilih Semua Soal (<?= count($soal_list) ?> Butir)
                </label>
              </div>
              <div class="d-flex gap-2">
                <button type="submit" name="action" value="selected" id="btnHapusTerpilih" class="btn btn-danger btn-sm fw-bold rounded-pill px-3 d-none" onclick="return confirm('Hapus semua butir soal yang dipilih secara permanen?')">
                  <i class="bi bi-trash-fill me-1"></i> Hapus Soal Terpilih (<span id="countSelectedSoal">0</span>)
                </button>
                <button type="submit" name="action" value="empty_all" class="btn btn-outline-danger btn-sm fw-bold rounded-pill px-3" onclick="return confirm('PERINGATAN: Kosongkan SELURUH butir soal pada paket bank soal ini?')">
                  <i class="bi bi-trash3-fill me-1"></i> Kosongkan Semua Soal
                </button>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Question List -->
        <div class="row">
          <div class="col-12">
            <?php if (empty($soal_list)): ?>
              <div class="card shadow-sm p-5 text-center text-muted rounded-4">
                <i class="bi bi-journal-x fs-1 text-warning mb-2"></i>
                <h4>Belum Ada Soal di Paket Ini</h4>
                <p>Gunakan tombol 'Gudang Soal', 'Generate AI', 'Import Massal', atau 'Tambah Soal' untuk menginput butir soal.</p>
              </div>
            <?php else: ?>
              <?php foreach ($soal_list as $s): ?>
                <div class="card shadow-sm mb-3 border-start border-4 border-primary rounded-4">
                  <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
                    <div class="d-flex align-items-center">
                      <input type="checkbox" name="soal_ids[]" value="<?= encrypt_id($s['id']) ?>" class="form-check-input soal-item-check me-3 fs-5">
                      <h5 class="fw-bold mb-0 text-primary">Soal No. <?= $s['nomor_soal'] ?> <span class="badge text-bg-secondary ms-2"><?= $s['jenis'] ?></span></h5>
                    </div>
                    <div>
                      <span class="badge text-bg-info me-2">Bobot: <?= $s['bobot'] ?> Poin</span>
                      <a href="<?= base_url('banksoal/hapus_soal/' . encrypt_id($s['id']) . '/' . encrypt_id($bank_soal['id'])) ?>" onclick="return confirm('Hapus soal ini?')" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="bi bi-trash-fill"></i> Hapus</a>
                    </div>
                  </div>
                  <div class="card-body p-4">
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
      </form>
    </div>
  </div>
</main>

<!-- Modal Input Detail Soal (Multi-Item Repeater) -->
<div class="modal fade" id="modalTambahSoalItem" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <form action="<?= base_url('banksoal/simpan_soal_item') ?>" method="post">
        <input type="hidden" name="bank_soal_id" value="<?= encrypt_id($bank_soal['id']) ?>" />
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
        <input type="hidden" name="bank_soal_id" value="<?= encrypt_id($bank_soal['id']) ?>" />
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
        <input type="hidden" name="bank_soal_id" value="<?= encrypt_id($bank_soal['id']) ?>" />
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

<!-- Modal OCR Foto Materi & AI Summary Generator -->
<div class="modal fade" id="modalOcrFotoMateri" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <!-- Modal Header with Stepper -->
      <div class="modal-header text-white p-3 p-md-4" style="background: linear-gradient(135deg, #0369a1 0%, #0284c7 40%, #4f46e5 100%);">
        <div class="w-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="modal-title fw-bold mb-1 text-white"><i class="bi bi-camera-reels-fill me-2 text-warning"></i> Foto Materi & AI Summary Generator</h5>
              <p class="mb-0 text-white opacity-75 small">Ubah foto buku/catatan menjadi teks digital (OCR), rangkum konsep kunci (AI Summary), dan hasilkan paket soal otomatis.</p>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" id="btnCloseOcrModal"></button>
          </div>

          <!-- 3-Step Wizard Progress Indicator -->
          <div class="d-flex justify-content-between align-items-center bg-black bg-opacity-25 rounded-pill p-1 p-md-2">
            <button type="button" class="btn btn-sm rounded-pill fw-bold text-nowrap ocr-step-btn px-3 text-white bg-warning text-dark shadow-sm" id="btnStepIndicator1" onclick="ocrWizardGoTo(1)">
              <i class="bi bi-camera-fill me-1"></i> 1. Foto / Upload
            </button>
            <i class="bi bi-chevron-right text-white-50 small"></i>
            <button type="button" class="btn btn-sm rounded-pill fw-bold text-nowrap ocr-step-btn px-3 text-white-50" id="btnStepIndicator2" onclick="ocrWizardGoTo(2)">
              <i class="bi bi-file-earmark-text-fill me-1"></i> 2. OCR & Summary
            </button>
            <i class="bi bi-chevron-right text-white-50 small"></i>
            <button type="button" class="btn btn-sm rounded-pill fw-bold text-nowrap ocr-step-btn px-3 text-white-50" id="btnStepIndicator3" onclick="ocrWizardGoTo(3)">
              <i class="bi bi-stars me-1"></i> 3. Review & Simpan
            </button>
          </div>
        </div>
      </div>

      <div class="modal-body p-3 p-md-4">
        <!-- Container Alert / Feedback -->
        <div id="ocrGlobalAlert"></div>

        <!-- ================= STEP 1: FOTO / UPLOAD GAMBAR ================= -->
        <div id="ocrStep1" class="ocr-wizard-step">
          <div class="alert alert-info d-flex align-items-center mb-3 rounded-3 border-0 bg-info-subtle text-info-emphasis">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div class="small">
              Pilih foto atau ambil gambar halaman buku pelajaran, modul, rangkuman catatan tangan, atau dokumen soal untuk mata pelajaran <strong><?= isset($bank_soal['nama_mapel']) ? $bank_soal['nama_mapel'] : '' ?></strong> (Kelas <strong><?= isset($bank_soal['nama_kelas']) ? $bank_soal['nama_kelas'] : '' ?></strong>).
            </div>
          </div>

          <div class="row g-3">
            <div class="col-lg-7">
              <div class="card border rounded-4 shadow-sm h-100">
                <div class="card-header bg-light p-2">
                  <ul class="nav nav-pills nav-fill" id="ocrInputSourceTabs" role="tablist">
                    <li class="nav-item">
                      <button class="nav-link active fw-bold py-2 rounded-3" id="tab-upload-btn" data-bs-toggle="pill" data-bs-target="#tabUploadFile" type="button">
                        <i class="bi bi-cloud-arrow-up-fill me-1 text-primary"></i> Upload Berkas Gambar
                      </button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link fw-bold py-2 rounded-3" id="tab-camera-btn" data-bs-toggle="pill" data-bs-target="#tabCameraDirect" type="button">
                        <i class="bi bi-camera-video-fill me-1 text-danger"></i> Kamera Langsung (Webcam/HP)
                      </button>
                    </li>
                  </ul>
                </div>

                <div class="card-body p-3">
                  <div class="tab-content" id="ocrSourceTabContent">
                    <!-- Tab A: Upload File -->
                    <div class="tab-pane fade show active" id="tabUploadFile">
                      <div id="ocrDropZone" class="border border-2 border-dashed border-primary border-opacity-50 rounded-4 p-4 text-center bg-light cursor-pointer position-relative">
                        <input type="file" id="ocrFileInput" accept="image/jpeg,image/png,image/webp" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" />
                        <i class="bi bi-images fs-1 text-primary mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark mb-1">Klik atau Drag & Drop Foto Materi di Sini</h6>
                        <p class="text-secondary small mb-2">Mendukung format JPG, PNG, WEBP (Maksimal 10MB)</p>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3">
                          <i class="bi bi-folder2-open me-1"></i> Pilih Berkas Foto
                        </button>
                      </div>
                    </div>

                    <!-- Tab B: Kamera Langsung -->
                    <div class="tab-pane fade" id="tabCameraDirect">
                      <div class="text-center">
                        <div class="position-relative bg-dark rounded-4 overflow-hidden mb-3" style="min-height: 260px; max-height: 360px;">
                          <video id="ocrCameraVideo" autoplay playsinline muted class="w-100 h-100" style="object-fit: cover;"></video>
                          <canvas id="ocrCameraCanvas" class="d-none"></canvas>
                          <div id="ocrCameraPlaceholder" class="position-absolute top-50 start-50 translate-middle text-white text-center w-100 p-3">
                            <i class="bi bi-camera fs-1 mb-2 d-block"></i>
                            <p class="small mb-2">Kamera belum aktif. Klik tombol di bawah untuk menyalakan kamera.</p>
                            <button type="button" id="btnStartCamera" class="btn btn-warning btn-sm fw-bold rounded-pill px-3 text-dark">
                              <i class="bi bi-camera-video-fill me-1"></i> Buka Kamera
                            </button>
                          </div>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                          <button type="button" id="btnCapturePhoto" class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm" disabled>
                            <i class="bi bi-record-circle-fill me-1"></i> Ambil Foto (Snap)
                          </button>
                          <button type="button" id="btnSwitchCamera" class="btn btn-outline-secondary btn-sm fw-bold rounded-pill px-3" style="display: none;">
                            <i class="bi bi-arrow-repeat me-1"></i> Ganti Kamera
                          </button>
                          <button type="button" id="btnStopCamera" class="btn btn-outline-danger btn-sm fw-bold rounded-pill px-3" style="display: none;">
                            <i class="bi bi-camera-video-off-fill me-1"></i> Matikan
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pratinjau Gambar & Konfigurasi Judul -->
            <div class="col-lg-5">
              <div class="card border rounded-4 shadow-sm h-100 bg-body-tertiary">
                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                  <span class="fw-bold text-dark small"><i class="bi bi-eye-fill me-1 text-primary"></i> Pratinjau Foto Materi</span>
                  <button type="button" id="btnClearPhotoPreview" class="btn btn-link btn-sm text-danger text-decoration-none p-0 d-none">
                    <i class="bi bi-trash"></i> Reset Foto
                  </button>
                </div>
                <div class="card-body p-3 d-flex flex-column">
                  <div class="flex-grow-1 border rounded-3 p-2 bg-white text-center d-flex align-items-center justify-content-center overflow-hidden mb-3" style="min-height: 200px; max-height: 280px;">
                    <img id="ocrPreviewImage" src="" alt="Pratinjau Foto" class="img-fluid rounded d-none" style="max-height: 260px; object-fit: contain;" />
                    <div id="ocrNoPreviewText" class="text-muted small">
                      <i class="bi bi-card-image fs-1 opacity-50 mb-1 d-block"></i>
                      Belum ada foto yang dipilih / diambil
                    </div>
                  </div>

                  <div>
                    <label class="form-label fw-bold small text-dark mb-1">Judul / Topik Materi (Opsional)</label>
                    <input type="text" id="ocrJudulMateri" class="form-control form-control-sm" placeholder="Contoh: Bab 3 - Ekosistem & Rantai Makanan" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="button" id="btnProsesOcr" class="btn btn-primary fw-bold px-4 py-2 rounded-pill shadow-sm" disabled style="background: linear-gradient(135deg, #0284c7 0%, #4f46e5 100%); border: none;">
              <i class="bi bi-magic me-1 text-warning"></i> Ekstrak Teks OCR & Buat Summary AI <i class="bi bi-arrow-right ms-1"></i>
            </button>
          </div>
        </div>

        <!-- ================= STEP 2: HASIL OCR & AI SUMMARY ================= -->
        <div id="ocrStep2" class="ocr-wizard-step d-none">
          <div class="row g-3 mb-3">
            <!-- Box 1: Teks Hasil OCR -->
            <div class="col-lg-6">
              <div class="card border rounded-4 shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                  <div>
                    <span class="fw-bold text-primary small"><i class="bi bi-file-earmark-text-fill me-1"></i> 1. Teks Hasil Ekstraksi OCR</span>
                    <span class="badge text-bg-primary ms-1" id="ocrEngineBadge">Vision AI</span>
                  </div>
                  <div>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 rounded-pill small" id="btnCopyOcrText" title="Salin Teks OCR">
                      <i class="bi bi-clipboard"></i> Salin
                    </button>
                  </div>
                </div>
                <div class="card-body p-3">
                  <p class="small text-secondary mb-2">Teks hasil pindaian foto di bawah dapat Anda sunting/koreksi bila terdapat ejaan yang kurang pas:</p>
                  <textarea id="ocrExtractedText" class="form-control font-monospace bg-light" rows="12" style="font-size: 0.85rem;" placeholder="Teks hasil ekstraksi OCR akan muncul di sini..."></textarea>
                  <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted"><span id="ocrCharCount">0</span> karakter terdeteksi</small>
                    <button type="button" id="btnReSummarize" class="btn btn-outline-info btn-sm fw-bold rounded-pill">
                      <i class="bi bi-arrow-clockwise me-1"></i> Perbarui Rangkuman AI
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Box 2: Ringkasan / Summary AI -->
            <div class="col-lg-6">
              <div class="card border rounded-4 shadow-sm h-100" style="border-color: #c084fc !important;">
                <div class="card-header text-white py-2 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);">
                  <span class="fw-bold small"><i class="bi bi-stars me-1 text-warning"></i> 2. Rangkuman & Konsep Inti (AI Summary)</span>
                  <span class="badge bg-white text-dark small" id="summaryEngineBadge">Multi-AI</span>
                </div>
                <div class="card-body p-3">
                  <p class="small text-secondary mb-2">Kesimpulan pokok & konsep materi berikut akan dijadikan acuan utama dalam pembuatan butir soal:</p>
                  <textarea id="ocrSummaryText" class="form-control bg-light" rows="12" style="font-size: 0.88rem; line-height: 1.5;" placeholder="Ringkasan materi dari AI akan muncul di sini..."></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Parameter Pembuatan Soal -->
          <div class="card border rounded-4 shadow-sm bg-light p-3 mb-3">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-sliders me-2 text-primary"></i> Pengaturan Butir Soal yang Ingin Dibuat</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-bold small">Jumlah Soal *</label>
                <select id="ocrSoalJumlah" class="form-select form-select-sm">
                  <option value="5" selected>5 Soal</option>
                  <option value="10">10 Soal</option>
                  <option value="15">15 Soal</option>
                  <option value="20">20 Soal</option>
                  <option value="30">30 Soal</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold small">Jenis / Tipe Soal *</label>
                <select id="ocrSoalJenis" class="form-select form-select-sm">
                  <option value="Pilihan Ganda" selected>Pilihan Ganda</option>
                  <option value="Essay">Essay</option>
                  <option value="Campuran">Campuran (PG & Essay)</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold small">Tingkat Kesulitan *</label>
                <select id="ocrSoalKesulitan" class="form-select form-select-sm">
                  <option value="Mudah">Mudah (Pemahaman Dasar)</option>
                  <option value="Sedang" selected>Sedang (Aplikasi Konsep)</option>
                  <option value="Sulit">Sulit (HOTS & Analisis Kasus)</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label fw-bold small">Instruksi Tambahan Soal (Opsional)</label>
                <input type="text" id="ocrSoalInstruksi" class="form-control form-control-sm" placeholder="Contoh: Fokus pada soal analisis hubungan sebab-akibat dan studi kasus..." />
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary fw-bold px-4 rounded-pill" onclick="ocrWizardGoTo(1)">
              <i class="bi bi-arrow-left me-1"></i> Kembali ke Foto
            </button>
            <button type="button" id="btnGenerateSoalFromSummary" class="btn btn-success fw-bold px-4 py-2 rounded-pill shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
              <i class="bi bi-stars me-1 text-warning"></i> Generate Soal dari Summary AI <i class="bi bi-arrow-right ms-1"></i>
            </button>
          </div>
        </div>

        <!-- ================= STEP 3: REVIEW & SIMPAN SOAL ================= -->
        <div id="ocrStep3" class="ocr-wizard-step d-none">
          <div class="d-flex flex-wrap justify-content-between align-items-center bg-light p-3 rounded-4 border mb-3 gap-2">
            <div>
              <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-check2-all text-success fs-5 me-1"></i>
                <span id="ocrTotalGeneratedBadge">0</span> Butir Soal Berhasil Dibuat Berdasarkan Summary Materi
              </h6>
              <small class="text-secondary">Silakan periksa pertanyaan, opsi jawaban, dan kunci sebelum disimpan ke repositori Bank Soal.</small>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-primary btn-sm fw-bold rounded-pill px-3" onclick="ocrWizardGoTo(2)">
                <i class="bi bi-arrow-repeat me-1"></i> Atur Ulang / Generate Lagi
              </button>
              <button type="button" id="btnSaveOcrSoal" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
                <i class="bi bi-cloud-arrow-down-fill me-1"></i> Simpan Semua Soal ke Bank Soal
              </button>
            </div>
          </div>

          <!-- Container Kartu Soal Hasil Generate -->
          <div id="ocrGeneratedQuestionsContainer" style="max-height: 55vh; overflow-y: auto;">
            <!-- Rendered by JS -->
          </div>
        </div>

        <!-- ================= ANIMATED PROGRESS / LOADING OVERLAY ================= -->
        <div id="ocrLoadingOverlay" class="d-none py-5 text-center">
          <div class="card border-0 shadow-lg p-4 mx-auto text-white" style="max-width: 620px; background: linear-gradient(135deg, #0284c7 0%, #4f46e5 50%, #7c3aed 100%); border-radius: 1.5rem;">
            <div class="mb-3">
              <div class="d-inline-flex p-3 rounded-circle bg-white bg-opacity-25 shadow-lg position-relative" style="animation: pulseAiGlow 1.8s infinite;">
                <i class="bi bi-robot fs-1 text-white"></i>
              </div>
            </div>
            <h5 class="fw-bold mb-2 text-white" id="ocrLoadingTitle">Sedang Memproses Foto Materi dengan AI...</h5>
            <p id="ocrLoadingStepText" class="small text-white-50 fw-semibold mb-3">Mengekstrak teks materi dari gambar menggunakan Vision OCR...</p>
            
            <div class="progress mb-2 rounded-pill shadow-sm" style="height: 14px; background: rgba(255,255,255,0.2);">
              <div id="ocrProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning rounded-pill" role="progressbar" style="width: 25%;"></div>
            </div>
            <small class="text-white opacity-75 d-block mt-2"><i class="bi bi-hourglass-split me-1"></i> Proses ekstraksi dan pembuatan soal memerlukan waktu beberapa detik.</small>
          </div>
        </div>

      </div>

      <div class="modal-footer bg-light p-3 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
        <span class="small text-muted"><i class="bi bi-shield-check text-success me-1"></i> Didukung Gemini Multimodal Vision AI & Smart Curriculum Engine</span>
      </div>
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

  // Bulk Delete Checkbox Toggle & Counter
  const checkAll = document.getElementById('checkAllSoal');
  const itemChecks = document.querySelectorAll('.soal-item-check');
  const btnHapusTerpilih = document.getElementById('btnHapusTerpilih');
  const countSpan = document.getElementById('countSelectedSoal');

  function updateBulkDeleteUI() {
    const selected = document.querySelectorAll('.soal-item-check:checked');
    const count = selected.length;
    if (countSpan) countSpan.innerText = count;
    if (btnHapusTerpilih) {
      if (count > 0) {
        btnHapusTerpilih.classList.remove('d-none');
      } else {
        btnHapusTerpilih.classList.add('d-none');
      }
    }
    if (checkAll && itemChecks.length > 0) {
      checkAll.checked = (count === itemChecks.length);
    }
  }

  if (checkAll) {
    checkAll.addEventListener('change', function() {
      const isChecked = this.checked;
      itemChecks.forEach(chk => {
        chk.checked = isChecked;
      });
      updateBulkDeleteUI();
    });
  }

  itemChecks.forEach(chk => {
    chk.addEventListener('change', updateBulkDeleteUI);
  });

  // =========================================================================
  // OCR FOTO MATERI & AI SUMMARY WIZARD JS LOGIC
  // =========================================================================
  let ocrCurrentStep = 1;
  let ocrSessionId = null;
  let ocrActiveImageBase64 = null;
  let ocrActiveImageFile = null;
  let ocrCameraStream = null;
  let ocrFacingMode = 'environment';
  let generatedOcrQuestions = [];

  const modalOcrEl = document.getElementById('modalOcrFotoMateri');
  const ocrFileInput = document.getElementById('ocrFileInput');
  const ocrDropZone = document.getElementById('ocrDropZone');
  const ocrPreviewImg = document.getElementById('ocrPreviewImage');
  const ocrNoPreviewTxt = document.getElementById('ocrNoPreviewText');
  const btnClearPhoto = document.getElementById('btnClearPhotoPreview');
  const btnProsesOcr = document.getElementById('btnProsesOcr');
  const ocrVideo = document.getElementById('ocrCameraVideo');
  const ocrCanvas = document.getElementById('ocrCameraCanvas');
  const btnStartCam = document.getElementById('btnStartCamera');
  const btnCapturePhoto = document.getElementById('btnCapturePhoto');
  const btnSwitchCam = document.getElementById('btnSwitchCamera');
  const btnStopCam = document.getElementById('btnStopCamera');
  const ocrCamPlaceholder = document.getElementById('ocrCameraPlaceholder');

  const ocrExtractedText = document.getElementById('ocrExtractedText');
  const ocrSummaryText = document.getElementById('ocrSummaryText');
  const ocrCharCount = document.getElementById('ocrCharCount');
  const btnReSummarize = document.getElementById('btnReSummarize');
  const btnGenSoalFromSum = document.getElementById('btnGenerateSoalFromSummary');
  const btnSaveOcrSoal = document.getElementById('btnSaveOcrSoal');

  const ocrLoadingOverlay = document.getElementById('ocrLoadingOverlay');
  const ocrLoadingTitle = document.getElementById('ocrLoadingTitle');
  const ocrLoadingStepText = document.getElementById('ocrLoadingStepText');
  const ocrProgressBar = document.getElementById('ocrProgressBar');

  window.ocrWizardGoTo = function(step) {
    ocrCurrentStep = step;
    ['ocrStep1', 'ocrStep2', 'ocrStep3'].forEach((id, idx) => {
      const el = document.getElementById(id);
      if (el) {
        if (idx + 1 === step) {
          el.classList.remove('d-none');
        } else {
          el.classList.add('d-none');
        }
      }
    });

    if (ocrLoadingOverlay) ocrLoadingOverlay.classList.add('d-none');

    // Update Stepper Buttons
    for (let i = 1; i <= 3; i++) {
      const btn = document.getElementById('btnStepIndicator' + i);
      if (btn) {
        if (i === step) {
          btn.className = 'btn btn-sm rounded-pill fw-bold text-nowrap ocr-step-btn px-3 text-white bg-warning text-dark shadow-sm';
        } else if (i < step) {
          btn.className = 'btn btn-sm rounded-pill fw-bold text-nowrap ocr-step-btn px-3 text-white bg-success text-white shadow-sm';
        } else {
          btn.className = 'btn btn-sm rounded-pill fw-bold text-nowrap ocr-step-btn px-3 text-white-50';
        }
      }
    }

    if (step === 3 && window.MathJax && window.MathJax.typesetPromise) {
      window.MathJax.typesetPromise();
    }
  };

  function setOcrLoading(show, title = '', stepMsg = '', progressVal = 30) {
    if (!ocrLoadingOverlay) return;
    if (show) {
      document.getElementById('ocrStep1').classList.add('d-none');
      document.getElementById('ocrStep2').classList.add('d-none');
      document.getElementById('ocrStep3').classList.add('d-none');
      ocrLoadingOverlay.classList.remove('d-none');
      if (ocrLoadingTitle && title) ocrLoadingTitle.innerText = title;
      if (ocrLoadingStepText && stepMsg) ocrLoadingStepText.innerText = stepMsg;
      if (ocrProgressBar) ocrProgressBar.style.width = progressVal + '%';
    } else {
      ocrLoadingOverlay.classList.add('d-none');
    }
  }

  function setPreviewImage(src) {
    if (ocrPreviewImg && ocrNoPreviewTxt) {
      ocrPreviewImg.src = src;
      ocrPreviewImg.classList.remove('d-none');
      ocrNoPreviewTxt.classList.add('d-none');
      if (btnClearPhoto) btnClearPhoto.classList.remove('d-none');
      if (btnProsesOcr) btnProsesOcr.disabled = false;
    }
  }

  function clearPreviewImage() {
    ocrActiveImageBase64 = null;
    ocrActiveImageFile = null;
    if (ocrFileInput) ocrFileInput.value = '';
    if (ocrPreviewImg) {
      ocrPreviewImg.src = '';
      ocrPreviewImg.classList.add('d-none');
    }
    if (ocrNoPreviewTxt) ocrNoPreviewTxt.classList.remove('d-none');
    if (btnClearPhoto) btnClearPhoto.classList.add('d-none');
    if (btnProsesOcr) btnProsesOcr.disabled = true;
  }

  if (btnClearPhoto) {
    btnClearPhoto.addEventListener('click', clearPreviewImage);
  }

  // File Upload Drag & Drop & Input Handling
  if (ocrFileInput) {
    ocrFileInput.addEventListener('change', function(e) {
      if (this.files && this.files[0]) {
        const file = this.files[0];
        ocrActiveImageFile = file;
        ocrActiveImageBase64 = null;
        const reader = new FileReader();
        reader.onload = function(evt) {
          setPreviewImage(evt.target.result);
        };
        reader.readAsDataURL(file);
      }
    });
  }

  if (ocrDropZone) {
    ['dragenter', 'dragover'].forEach(eventName => {
      ocrDropZone.addEventListener(eventName, e => {
        e.preventDefault();
        ocrDropZone.classList.add('border-warning', 'bg-warning-subtle');
      }, false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
      ocrDropZone.addEventListener(eventName, e => {
        e.preventDefault();
        ocrDropZone.classList.remove('border-warning', 'bg-warning-subtle');
      }, false);
    });
    ocrDropZone.addEventListener('drop', e => {
      const dt = e.dataTransfer;
      const files = dt.files;
      if (files && files.length > 0) {
        const file = files[0];
        if (file.type.match('image.*')) {
          ocrActiveImageFile = file;
          ocrActiveImageBase64 = null;
          const reader = new FileReader();
          reader.onload = function(evt) {
            setPreviewImage(evt.target.result);
          };
          reader.readAsDataURL(file);
        }
      }
    });
  }

  // Live Camera Snapshot Handling
  async function startCamera() {
    try {
      stopCamera();
      const constraints = {
        video: {
          facingMode: ocrFacingMode,
          width: { ideal: 1280 },
          height: { ideal: 720 }
        },
        audio: false
      };
      ocrCameraStream = await navigator.mediaDevices.getUserMedia(constraints);
      if (ocrVideo) {
        ocrVideo.srcObject = ocrCameraStream;
        ocrVideo.play();
      }
      if (ocrCamPlaceholder) ocrCamPlaceholder.classList.add('d-none');
      if (btnCapturePhoto) btnCapturePhoto.disabled = false;
      if (btnSwitchCam) btnSwitchCam.style.display = 'inline-block';
      if (btnStopCam) btnStopCam.style.display = 'inline-block';
    } catch (err) {
      alert('Tidak dapat mengakses kamera perangkat: ' + (err.message || err.name) + '. Pastikan izin kamera aktif pada browser.');
      console.error(err);
    }
  }

  function stopCamera() {
    if (ocrCameraStream) {
      ocrCameraStream.getTracks().forEach(track => track.stop());
      ocrCameraStream = null;
    }
    if (ocrVideo) ocrVideo.srcObject = null;
    if (ocrCamPlaceholder) ocrCamPlaceholder.classList.remove('d-none');
    if (btnCapturePhoto) btnCapturePhoto.disabled = true;
    if (btnSwitchCam) btnSwitchCam.style.display = 'none';
    if (btnStopCam) btnStopCam.style.display = 'none';
  }

  if (btnStartCam) btnStartCam.addEventListener('click', startCamera);
  if (btnStopCam) btnStopCam.addEventListener('click', stopCamera);

  if (btnSwitchCam) {
    btnSwitchCam.addEventListener('click', function() {
      ocrFacingMode = (ocrFacingMode === 'environment') ? 'user' : 'environment';
      startCamera();
    });
  }

  if (btnCapturePhoto && ocrVideo && ocrCanvas) {
    btnCapturePhoto.addEventListener('click', function() {
      const width = ocrVideo.videoWidth || 640;
      const height = ocrVideo.videoHeight || 480;
      ocrCanvas.width = width;
      ocrCanvas.height = height;
      const ctx = ocrCanvas.getContext('2d');
      ctx.drawImage(ocrVideo, 0, 0, width, height);

      const dataUrl = ocrCanvas.toDataURL('image/jpeg', 0.92);
      ocrActiveImageBase64 = dataUrl;
      ocrActiveImageFile = null;

      setPreviewImage(dataUrl);
      stopCamera();
    });
  }

  // Stop camera when modal is closed
  if (modalOcrEl) {
    modalOcrEl.addEventListener('hidden.bs.modal', function() {
      stopCamera();
    });
  }

  function safeFetchJson(url, options) {
    return fetch(url, options)
      .then(res => res.text())
      .then(text => {
        try {
          return JSON.parse(text);
        } catch (e) {
          let errMatch = text.match(/<p>(.*?)<\/p>/i) || text.match(/<h1[^>]*>(.*?)<\/h1>/i) || text.match(/<title>(.*?)<\/title>/i);
          let errMsg = errMatch ? errMatch[1].replace(/<[^>]+>/g, '').trim() : text.substring(0, 150);
          console.error("Server raw HTML response:", text);
          throw new Error(errMsg || "Respon dari server tidak valid.");
        }
      });
  }

  // Step 1 -> Step 2: Ekstraksi OCR & AI Summary
  if (btnProsesOcr) {
    btnProsesOcr.addEventListener('click', function() {
      if (!ocrActiveImageFile && !ocrActiveImageBase64) {
        alert('Silakan pilih berkas foto atau ambil foto materi terlebih dahulu.');
        return;
      }

      const formData = new FormData();
      formData.append('bank_soal_id', '<?= encrypt_id($bank_soal["id"]) ?>');
      const judulVal = document.getElementById('ocrJudulMateri') ? document.getElementById('ocrJudulMateri').value : '';
      formData.append('judul_materi', judulVal);

      if (ocrActiveImageBase64) {
        formData.append('image_base64', ocrActiveImageBase64);
      } else if (ocrActiveImageFile) {
        formData.append('image_file', ocrActiveImageFile);
      }

      setOcrLoading(true, 'Sedang Memproses Foto Materi...', '1/2 Mengekstrak teks & rumus dari gambar via Vision OCR...', 35);

      let pVal = 35;
      const pInterval = setInterval(() => {
        pVal = Math.min(pVal + 5, 88);
        if (ocrProgressBar) ocrProgressBar.style.width = pVal + '%';
      }, 1200);

      safeFetchJson('<?= base_url("banksoal/ocr_upload_extract") ?>', {
        method: 'POST',
        body: formData
      })
      .then(data => {
        if (data.status === 'success') {
          ocrSessionId = data.session_id;
          if (ocrExtractedText) {
            ocrExtractedText.value = data.ocr_text;
            if (ocrCharCount) ocrCharCount.innerText = data.ocr_text.length;
          }
          const engineBadge = document.getElementById('ocrEngineBadge');
          if (engineBadge && data.engine) engineBadge.innerText = data.engine;

          if (ocrLoadingStepText) {
            ocrLoadingStepText.innerText = '2/2 Menganalisis kurikulum & membuat Rangkuman/Summary AI...';
          }
          if (ocrProgressBar) ocrProgressBar.style.width = '75%';

          // Otomatis request Summary AI
          return safeFetchJson('<?= base_url("banksoal/ocr_generate_summary") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
              session_id: ocrSessionId || '',
              bank_soal_id: '<?= encrypt_id($bank_soal["id"]) ?>',
              ocr_text: data.ocr_text,
              topik: judulVal
            })
          });
        } else {
          throw new Error(data.message || 'Gagal mengekstrak teks OCR.');
        }
      })
      .then(sumData => {
        clearInterval(pInterval);
        if (sumData && sumData.status === 'success') {
          if (ocrSummaryText) ocrSummaryText.value = sumData.summary;
          const sumEngineBadge = document.getElementById('summaryEngineBadge');
          if (sumEngineBadge && sumData.engine) sumEngineBadge.innerText = sumData.engine;

          ocrWizardGoTo(2);
        } else if (sumData) {
          if (ocrSummaryText) ocrSummaryText.value = 'Gagal membuat ringkasan otomatis: ' + (sumData.message || '') + '. Anda dapat klik tombol "Perbarui Rangkuman AI" untuk mencoba lagi.';
          ocrWizardGoTo(2);
        }
      })
      .catch(err => {
        clearInterval(pInterval);
        setOcrLoading(false);
        ocrWizardGoTo(1);
        alert('Terjadi kesalahan: ' + err.message);
        console.error(err);
      });
    });
  }

  // Update char count on OCR text input
  if (ocrExtractedText) {
    ocrExtractedText.addEventListener('input', function() {
      if (ocrCharCount) ocrCharCount.innerText = this.value.length;
    });
  }

  // Salin teks OCR ke clipboard
  const btnCopyOcr = document.getElementById('btnCopyOcrText');
  if (btnCopyOcr && ocrExtractedText) {
    btnCopyOcr.addEventListener('click', function() {
      navigator.clipboard.writeText(ocrExtractedText.value).then(() => {
        alert('Teks OCR berhasil disalin ke clipboard.');
      });
    });
  }

  // Tombol Buat Ulang / Perbarui Rangkuman AI
  if (btnReSummarize) {
    btnReSummarize.addEventListener('click', function() {
      const textVal = ocrExtractedText ? ocrExtractedText.value : '';
      if (!textVal.trim()) {
        alert('Teks materi OCR kosong.');
        return;
      }

      const origHtml = btnReSummarize.innerHTML;
      btnReSummarize.disabled = true;
      btnReSummarize.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Merangkum...';

      safeFetchJson('<?= base_url("banksoal/ocr_generate_summary") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          session_id: ocrSessionId || '',
          bank_soal_id: '<?= encrypt_id($bank_soal["id"]) ?>',
          ocr_text: textVal,
          topik: document.getElementById('ocrJudulMateri') ? document.getElementById('ocrJudulMateri').value : ''
        })
      })
      .then(data => {
        btnReSummarize.disabled = false;
        btnReSummarize.innerHTML = origHtml;
        if (data.status === 'success') {
          if (ocrSummaryText) ocrSummaryText.value = data.summary;
          const sumEngineBadge = document.getElementById('summaryEngineBadge');
          if (sumEngineBadge && data.engine) sumEngineBadge.innerText = data.engine;
          alert('Rangkuman & kesimpulan materi berhasil diperbarui.');
        } else {
          alert('Gagal merangkum: ' + data.message);
        }
      })
      .catch(err => {
        btnReSummarize.disabled = false;
        btnReSummarize.innerHTML = origHtml;
        alert('Terjadi kesalahan: ' + err.message);
        console.error(err);
      });
    });
  }

  // Step 2 -> Step 3: Generate Soal dari Summary AI
  if (btnGenSoalFromSum) {
    btnGenSoalFromSum.addEventListener('click', function() {
      const summaryVal = ocrSummaryText ? ocrSummaryText.value : '';
      const ocrVal = ocrExtractedText ? ocrExtractedText.value : '';

      if (!summaryVal.trim()) {
        alert('Ringkasan materi tidak boleh kosong.');
        return;
      }

      const jumlahVal = document.getElementById('ocrSoalJumlah') ? document.getElementById('ocrSoalJumlah').value : 5;
      const jenisVal = document.getElementById('ocrSoalJenis') ? document.getElementById('ocrSoalJenis').value : 'Pilihan Ganda';
      const diffVal = document.getElementById('ocrSoalKesulitan') ? document.getElementById('ocrSoalKesulitan').value : 'Sedang';
      const instVal = document.getElementById('ocrSoalInstruksi') ? document.getElementById('ocrSoalInstruksi').value : '';

      setOcrLoading(true, 'Sedang Membuat Butir Soal dari Summary AI...', 'Merumuskan ' + jumlahVal + ' butir soal berbobot tinggi...', 45);

      let pVal = 45;
      const pInterval = setInterval(() => {
        pVal = Math.min(pVal + 6, 92);
        if (ocrProgressBar) ocrProgressBar.style.width = pVal + '%';
      }, 1500);

      safeFetchJson('<?= base_url("banksoal/ocr_generate_soal") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          session_id: ocrSessionId || '',
          bank_soal_id: '<?= encrypt_id($bank_soal["id"]) ?>',
          summary_text: summaryVal,
          ocr_text: ocrVal,
          jumlah: jumlahVal,
          jenis: jenisVal,
          tingkat_kesulitan: diffVal,
          instruksi_tambahan: instVal
        })
      })
      .then(data => {
        clearInterval(pInterval);
        if (data.status === 'success' && Array.isArray(data.data) && data.data.length > 0) {
          generatedOcrQuestions = data.data;
          renderOcrQuestionsList(data.data);
          ocrWizardGoTo(3);
        } else {
          setOcrLoading(false);
          ocrWizardGoTo(2);
          alert('Gagal menghasilkan soal: ' + (data.message || 'Format tidak valid. Silakan coba kembali.'));
        }
      })
      .catch(err => {
        clearInterval(pInterval);
        setOcrLoading(false);
        ocrWizardGoTo(2);
        alert('Terjadi kesalahan: ' + err.message);
        console.error(err);
      });
    });
  }

  // Render Kartu Butir Soal Hasil Generate
  function renderOcrQuestionsList(items) {
    const container = document.getElementById('ocrGeneratedQuestionsContainer');
    const badge = document.getElementById('ocrTotalGeneratedBadge');
    if (badge) badge.innerText = items.length;
    if (!container) return;

    if (items.length === 0) {
      container.innerHTML = '<div class="alert alert-warning text-center">Belum ada butir soal. Silakan klik generate ulang.</div>';
      return;
    }

    let html = '';
    items.forEach((q, idx) => {
      const nomor = idx + 1;
      const jenis = q.jenis || 'Pilihan Ganda';
      const bobot = q.bobot || 10;
      const diff = q.tingkat_kesulitan || 'Sedang';
      const kunci = (q.kunci_jawaban || '').toUpperCase();

      html += `
        <div class="card border border-primary border-opacity-25 shadow-sm rounded-4 mb-3 card-ocr-item" data-index="${idx}">
          <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
            <div class="d-flex align-items-center gap-2">
              <span class="badge text-bg-primary fs-6">No. ${nomor}</span>
              <span class="badge text-bg-secondary">${jenis}</span>
              <span class="badge text-bg-info text-dark">Bobot: ${bobot} Poin</span>
              <span class="badge text-bg-warning text-dark">${diff}</span>
            </div>
            <div>
              <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 rounded-pill" onclick="removeGeneratedOcrQuestion(${idx})">
                <i class="bi bi-trash-fill"></i> Hapus
              </button>
            </div>
          </div>
          <div class="card-body p-3">
            <p class="fw-bold fs-6 mb-2">${escapeHtml(q.pertanyaan)}</p>
      `;

      if (jenis === 'Pilihan Ganda') {
        html += `
          <div class="row g-2 mb-2">
            <div class="col-md-6">
              <div class="p-2 border rounded small ${kunci === 'A' ? 'bg-success text-white fw-bold shadow-sm' : 'bg-white'}">
                <strong>A.</strong> ${escapeHtml(q.pilihan_a || '')}
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-2 border rounded small ${kunci === 'B' ? 'bg-success text-white fw-bold shadow-sm' : 'bg-white'}">
                <strong>B.</strong> ${escapeHtml(q.pilihan_b || '')}
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-2 border rounded small ${kunci === 'C' ? 'bg-success text-white fw-bold shadow-sm' : 'bg-white'}">
                <strong>C.</strong> ${escapeHtml(q.pilihan_c || '')}
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-2 border rounded small ${kunci === 'D' ? 'bg-success text-white fw-bold shadow-sm' : 'bg-white'}">
                <strong>D.</strong> ${escapeHtml(q.pilihan_d || '')}
              </div>
            </div>
            ${q.pilihan_e ? `
              <div class="col-md-6">
                <div class="p-2 border rounded small ${kunci === 'E' ? 'bg-success text-white fw-bold shadow-sm' : 'bg-white'}">
                  <strong>E.</strong> ${escapeHtml(q.pilihan_e)}
                </div>
              </div>
            ` : ''}
          </div>
        `;
      }

      html += `
            <div class="p-2 bg-light rounded-3 border small">
              <div class="text-success fw-bold mb-1"><i class="bi bi-key-fill me-1"></i> Kunci Jawaban: ${escapeHtml(q.kunci_jawaban || '')}</div>
              ${q.pembahasan ? `<div class="text-secondary"><strong>Pembahasan:</strong> ${escapeHtml(q.pembahasan)}</div>` : ''}
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
  }

  window.removeGeneratedOcrQuestion = function(index) {
    if (confirm('Hapus butir soal ini dari daftar?')) {
      generatedOcrQuestions.splice(index, 1);
      renderOcrQuestionsList(generatedOcrQuestions);
    }
  };

  function escapeHtml(text) {
    if (!text) return '';
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, m => map[m]);
  }

  // Simpan Semua Soal ke Bank Soal
  if (btnSaveOcrSoal) {
    btnSaveOcrSoal.addEventListener('click', function() {
      if (!generatedOcrQuestions || generatedOcrQuestions.length === 0) {
        alert('Tidak ada butir soal yang siap disimpan.');
        return;
      }

      const origHtml = btnSaveOcrSoal.innerHTML;
      btnSaveOcrSoal.disabled = true;
      btnSaveOcrSoal.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan Soal...';

      safeFetchJson('<?= base_url("banksoal/ocr_save_soal") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          bank_soal_id: '<?= encrypt_id($bank_soal["id"]) ?>',
          session_id: ocrSessionId || '',
          soal_json: JSON.stringify(generatedOcrQuestions)
        })
      })
      .then(data => {
        if (data.status === 'success') {
          btnSaveOcrSoal.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Tersimpan!';
          const modalInst = bootstrap.Modal.getInstance(modalOcrEl);
          if (modalInst) modalInst.hide();
          window.location.reload();
        } else {
          btnSaveOcrSoal.disabled = false;
          btnSaveOcrSoal.innerHTML = origHtml;
          alert('Gagal menyimpan soal: ' + data.message);
        }
      })
      .catch(err => {
        btnSaveOcrSoal.disabled = false;
        btnSaveOcrSoal.innerHTML = origHtml;
        alert('Terjadi kesalahan: ' + err.message);
        console.error(err);
      });
    });
  }
});
</script>

