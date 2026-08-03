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
          <div class="alert alert-info border-0 shadow-sm rounded-3 mb-3">
            <h6 class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-1"></i> Cara Penggunaan Parser Massal:</h6>
            <small class="d-block">Copy & Paste seluruh teks dokumen soal (Microsoft Word / PDF / Teks Biasa) langsung ke dalam kotak teks di bawah. Sistem secara otomatis mendeteksi nomor soal (`1.`, `2.`), opsi jawaban (`a.`, `b.`, `c.`, `d.`), teks bacaan, serta tabel / blok `KUNCI JAWABAN` & Pembahasannya!</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold text-dark"><i class="bi bi-file-text me-1 text-success"></i> Tempelkan Teks Soal & Kunci Jawaban Di Sini *</label>
            <textarea name="raw_text" class="form-control font-monospace text-dark bg-light" rows="14" style="font-size: 0.88rem;" placeholder="Copas seluruh teks soal dan kunci jawaban di sini... Contoh:&#10;&#10;Teks Bacaan untuk soal nomor 1 dan 2:&#10;Liburan sekolah kali ini...&#10;&#10;1. Apa yang dilakukan Rina?&#10;a. Berenang&#10;b. Membuat istana pasir&#10;c. Tidur&#10;d. Membaca&#10;&#10;KUNCI JAWABAN&#10;1. B (Membuat istana pasir)&#10;2. A" required></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success fw-bold px-4 rounded-pill"><i class="bi bi-magic me-1"></i> Proses & Import Massal Soal</button>
        </div>
      </form>
    </div>
  </div>
</div>
