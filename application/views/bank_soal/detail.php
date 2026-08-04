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

      <div class="card border-0 shadow-sm mb-4 bg-primary text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <span class="badge text-bg-warning fs-6 mb-2"><?= $bank_soal['kode_soal'] ?></span>
              <h3 class="fw-bold mb-1"><?= $bank_soal['judul'] ?></h3>
              <p class="mb-0 opacity-90">Mata Pelajaran: <?= $bank_soal['nama_mapel'] ?> | Kelas: <?= $bank_soal['nama_kelas'] ?> | Total: <?= $bank_soal['jumlah_soal'] ?> Soal</p>
            </div>
            <div>
              <button class="btn btn-light btn-lg fw-bold shadow-sm me-2 text-primary" data-bs-toggle="modal" data-bs-target="#modalImportMassalSoal">
                <i class="bi bi-file-earmark-arrow-up-fill me-2"></i> Import Massal / Copas Soal & Kunci
              </button>
              <button class="btn btn-warning btn-lg fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSoalItem">
                <i class="bi bi-plus-circle-fill me-2"></i> Tambah Soal Satuan
              </button>
            </div>
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

<!-- Modal Input Detail Soal -->
<div class="modal fade" id="modalTambahSoalItem" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?= base_url('banksoal/simpan_soal_item') ?>" method="post">
        <input type="hidden" name="bank_soal_id" value="<?= $bank_soal['id'] ?>" />
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Tambah Butir Soal Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tipe / Jenis Soal *</label>
              <select name="jenis" class="form-select" required>
                <option value="Pilihan Ganda">Pilihan Ganda</option>
                <option value="Essay">Essay</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Bobot Poin *</label>
              <input type="number" name="bobot" class="form-control" value="10" required />
            </div>
            <div class="col-md-12">
              <label class="form-label fw-semibold">Pertanyaan / Soal *</label>
              <textarea name="pertanyaan" class="form-control" rows="4" placeholder="Tuliskan pertanyaan soal..." required></textarea>
            </div>

            <div class="col-md-12"><hr><h6 class="fw-bold text-primary">Pilihan Jawaban (Untuk Pilihan Ganda)</h6></div>
            <div class="col-md-6"><label class="form-label">Pilihan A</label><input type="text" name="pilihan_a" class="form-control" /></div>
            <div class="col-md-6"><label class="form-label">Pilihan B</label><input type="text" name="pilihan_b" class="form-control" /></div>
            <div class="col-md-6"><label class="form-label">Pilihan C</label><input type="text" name="pilihan_c" class="form-control" /></div>
            <div class="col-md-6"><label class="form-label">Pilihan D</label><input type="text" name="pilihan_d" class="form-control" /></div>
            <div class="col-md-6"><label class="form-label">Pilihan E (Opsional)</label><input type="text" name="pilihan_e" class="form-control" /></div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-success">Kunci Jawaban Benar *</label>
              <input type="text" name="kunci_jawaban" class="form-control fw-bold" placeholder="Contoh: A atau B atau Jawaban Essay" required />
            </div>
            <div class="col-md-12">
              <label class="form-label fw-semibold">Pembahasan / Solusi Soal</label>
              <textarea name="pembahasan" class="form-control" rows="2" placeholder="Penjelasan solusi soal..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold">Simpan Butir Soal</button>
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
});
</script>
