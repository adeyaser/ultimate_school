<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Pengaturan Company Profile & Profil Sekolah</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Profil Sekolah</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle-fill me-2"></i><?= $this->session->flashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white p-3 border-bottom">
          <ul class="nav nav-pills card-header-pills fw-bold" id="comproTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-umum-btn" data-bs-toggle="tab" data-bs-target="#tab-umum" type="button" role="tab"><i class="bi bi-building me-1"></i> Identitas Sekolah</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-hero-btn" data-bs-toggle="tab" data-bs-target="#tab-hero" type="button" role="tab"><i class="bi bi-display me-1"></i> Hero & Running Text</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-visimisi-btn" data-bs-toggle="tab" data-bs-target="#tab-visimisi" type="button" role="tab"><i class="bi bi-compass me-1"></i> Visi, Misi & Sambutan</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-fasilitas-btn" data-bs-toggle="tab" data-bs-target="#tab-fasilitas" type="button" role="tab"><i class="bi bi-building-check me-1"></i> Kelola Fasilitas</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-faq-btn" data-bs-toggle="tab" data-bs-target="#tab-faq" type="button" role="tab"><i class="bi bi-question-circle me-1"></i> Kelola FAQ</button>
            </li>
          </ul>
        </div>
        <div class="card-body p-4">
          <form action="<?= base_url('sekolah/simpan') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= isset($sekolah['id']) ? $sekolah['id'] : 1 ?>" />

            <div class="tab-content" id="comproTabsContent">
              
              <!-- Tab 1: Identitas Sekolah -->
              <div class="tab-pane fade show active" id="tab-umum" role="tabpanel">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i> Identitas & Logo Utama Sekolah</h5>
                
                <!-- Logo Upload & Preview Box -->
                <div class="p-3 bg-light rounded-4 border mb-4 d-flex align-items-center gap-3">
                  <?php $logo_src = isset($sekolah['logo']) && $sekolah['logo'] ? $sekolah['logo'] : 'dist/assets/img/AdminLTELogo.png'; ?>
                  <img src="<?= (strpos($logo_src, 'http') === 0) ? $logo_src : base_url($logo_src) ?>" alt="Logo Preview" width="64" height="64" class="rounded-circle shadow-sm border bg-white p-1" />
                  <div class="flex-grow-1">
                    <label class="form-label fw-bold mb-1"><i class="bi bi-upload me-1 text-primary"></i> Upload Logo Baru (Pilih File) *</label>
                    <input type="file" name="logo_file" class="form-control form-control-sm mb-1" accept="image/*" />
                    <input type="text" name="logo" class="form-control form-control-sm" value="<?= htmlspecialchars($logo_src) ?>" placeholder="Path logo saat ini / URL" />
                    <small class="text-muted">Pilih file dari komputer Anda untuk di-upload, atau masukkan URL/Path manual.</small>
                  </div>
                </div>

                <div class="row g-3">
                  <div class="col-md-4"><label class="form-label fw-semibold">Nama Sekolah *</label><input type="text" name="nama_sekolah" class="form-control" value="<?= isset($sekolah['nama_sekolah']) ? htmlspecialchars($sekolah['nama_sekolah']) : 'Ultimate School' ?>" required /></div>
                  <div class="col-md-4">
                    <label class="form-label fw-bold text-primary"><i class="bi bi-mortarboard-fill me-1"></i> Jenjang Pendidikan *</label>
                    <select name="jenjang" class="form-select fw-bold border-primary text-primary" required>
                      <option value="SD" <?= (isset($sekolah['jenjang']) && $sekolah['jenjang'] === 'SD') ? 'selected' : '' ?>>🏫 SD (Sekolah Dasar - Kelas 1 s/d 6)</option>
                      <option value="SMP" <?= (isset($sekolah['jenjang']) && $sekolah['jenjang'] === 'SMP') ? 'selected' : '' ?>>🏫 SMP (Sekolah Menengah Pertama - Kelas 7 s/d 9)</option>
                      <option value="SMA" <?= (isset($sekolah['jenjang']) && $sekolah['jenjang'] === 'SMA') ? 'selected' : '' ?>>🏫 SMA (Sekolah Menengah Atas - Kelas 10 s/d 12)</option>
                      <option value="SMK" <?= (isset($sekolah['jenjang']) && $sekolah['jenjang'] === 'SMK') ? 'selected' : '' ?>>🏫 SMK (Sekolah Menengah Kejuruan - Kelas 10 s/d 12)</option>
                    </select>
                  </div>
                  <div class="col-md-4"><label class="form-label fw-semibold">NPSN *</label><input type="text" name="npsn" class="form-control" value="<?= isset($sekolah['npsn']) ? htmlspecialchars($sekolah['npsn']) : '12345678' ?>" required /></div>
                  
                  <div class="col-md-6"><label class="form-label fw-semibold">Kepala Sekolah</label><input type="text" name="kepala_sekolah" class="form-control" value="<?= isset($sekolah['kepala_sekolah']) ? htmlspecialchars($sekolah['kepala_sekolah']) : '' ?>" /></div>
                  <div class="col-md-6"><label class="form-label fw-semibold">Telepon Sekolah</label><input type="text" name="telepon" class="form-control" value="<?= isset($sekolah['telepon']) ? htmlspecialchars($sekolah['telepon']) : '' ?>" /></div>
                  <div class="col-md-6"><label class="form-label fw-semibold">Email Sekolah</label><input type="email" name="email" class="form-control" value="<?= isset($sekolah['email']) ? htmlspecialchars($sekolah['email']) : '' ?>" /></div>
                  <div class="col-md-6"><label class="form-label fw-semibold">Website</label><input type="text" name="website" class="form-control" value="<?= isset($sekolah['website']) ? htmlspecialchars($sekolah['website']) : '' ?>" /></div>

                  <div class="col-md-4"><label class="form-label fw-semibold">Kota</label><input type="text" name="kota" class="form-control" value="<?= isset($sekolah['kota']) ? htmlspecialchars($sekolah['kota']) : 'Jakarta' ?>" /></div>
                  <div class="col-md-4"><label class="form-label fw-semibold">Provinsi</label><input type="text" name="provinsi" class="form-control" value="<?= isset($sekolah['provinsi']) ? htmlspecialchars($sekolah['provinsi']) : 'DKI Jakarta' ?>" /></div>
                  <div class="col-md-4"><label class="form-label fw-semibold">Kode Pos</label><input type="text" name="kode_pos" class="form-control" value="<?= isset($sekolah['kode_pos']) ? htmlspecialchars($sekolah['kode_pos']) : '12345' ?>" /></div>

                  <div class="col-md-12"><label class="form-label fw-semibold">Alamat Lengkap</label><textarea name="alamat" class="form-control" rows="2"><?= isset($sekolah['alamat']) ? htmlspecialchars($sekolah['alamat']) : '' ?></textarea></div>

                  <!-- Multi-Jenjang NPSN & Kepala Sekolah Settings Box -->
                  <div class="col-12 mt-3">
                    <div class="card border border-primary-subtle shadow-sm rounded-4 overflow-hidden">
                      <div class="card-header bg-primary-subtle p-3">
                        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-award-fill me-2"></i> Pengaturan NPSN & Kepala Sekolah Per Jenjang (SD, SMP, SMA)</h6>
                        <small class="text-muted">NPSN dan Nama Kepala Sekolah akan otomatis disesuaikan sesuai mode jenjang yang aktif.</small>
                      </div>
                      <div class="card-body p-3 bg-white">
                        <div class="row g-3">
                          <!-- SD -->
                          <div class="col-md-4 p-3 bg-success-subtle rounded-3 border border-success">
                            <h6 class="fw-bold text-success"><i class="bi bi-bank me-1"></i> Mode SD (Sekolah Dasar)</h6>
                            <label class="form-label fw-semibold small mb-1">NPSN SD *</label>
                            <input type="text" name="npsn_sd" class="form-control form-control-sm mb-2 font-monospace" value="<?= isset($sekolah['npsn_sd']) ? htmlspecialchars($sekolah['npsn_sd']) : '10100001' ?>" placeholder="NPSN SD" />
                            <label class="form-label fw-semibold small mb-1">Kepala Sekolah SD</label>
                            <input type="text" name="kepala_sd" class="form-control form-control-sm" value="<?= isset($sekolah['kepala_sd']) ? htmlspecialchars($sekolah['kepala_sd']) : '' ?>" placeholder="Nama Kepala SD" />
                          </div>

                          <!-- SMP -->
                          <div class="col-md-4 p-3 bg-info-subtle rounded-3 border border-info">
                            <h6 class="fw-bold text-info"><i class="bi bi-bank2 me-1"></i> Mode SMP (Menengah Pertama)</h6>
                            <label class="form-label fw-semibold small mb-1">NPSN SMP *</label>
                            <input type="text" name="npsn_smp" class="form-control form-control-sm mb-2 font-monospace" value="<?= isset($sekolah['npsn_smp']) ? htmlspecialchars($sekolah['npsn_smp']) : '20200002' ?>" placeholder="NPSN SMP" />
                            <label class="form-label fw-semibold small mb-1">Kepala Sekolah SMP</label>
                            <input type="text" name="kepala_smp" class="form-control form-control-sm" value="<?= isset($sekolah['kepala_smp']) ? htmlspecialchars($sekolah['kepala_smp']) : '' ?>" placeholder="Nama Kepala SMP" />
                          </div>

                          <!-- SMA -->
                          <div class="col-md-4 p-3 bg-warning-subtle rounded-3 border border-warning">
                            <h6 class="fw-bold text-dark"><i class="bi bi-building me-1"></i> Mode SMA (Menengah Atas)</h6>
                            <label class="form-label fw-semibold small mb-1">NPSN SMA *</label>
                            <input type="text" name="npsn_sma" class="form-control form-control-sm mb-2 font-monospace" value="<?= isset($sekolah['npsn_sma']) ? htmlspecialchars($sekolah['npsn_sma']) : '30300003' ?>" placeholder="NPSN SMA" />
                            <label class="form-label fw-semibold small mb-1">Kepala Sekolah SMA</label>
                            <input type="text" name="kepala_sma" class="form-control form-control-sm" value="<?= isset($sekolah['kepala_sma']) ? htmlspecialchars($sekolah['kepala_sma']) : '' ?>" placeholder="Nama Kepala SMA" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tab 2: Hero & Konten Compro -->
              <div class="tab-pane fade" id="tab-hero" role="tabpanel">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-display me-2"></i> Teks Hero & Media Banner (Gambar / GIF / Video)</h5>
                <div class="row g-3">
                  <div class="col-md-12">
                    <label class="form-label fw-semibold">Pengumuman Running Text (Top Bar) *</label>
                    <textarea name="running_text" class="form-control" rows="2" placeholder="Pengumuman berjalan pada bagian paling atas website..."><?= isset($sekolah['running_text']) ? htmlspecialchars($sekolah['running_text']) : '' ?></textarea>
                  </div>
                  <div class="col-md-12">
                    <label class="form-label fw-semibold">Judul Hero Banner (Headline) *</label>
                    <input type="text" name="hero_title" class="form-control form-control-lg fw-bold text-primary" value="<?= isset($sekolah['hero_title']) ? htmlspecialchars($sekolah['hero_title']) : '' ?>" />
                  </div>
                  <div class="col-md-12">
                    <label class="form-label fw-semibold">Subjudul Hero Banner (Deskripsi) *</label>
                    <textarea name="hero_subtitle" class="form-control" rows="2"><?= isset($sekolah['hero_subtitle']) ? htmlspecialchars($sekolah['hero_subtitle']) : '' ?></textarea>
                  </div>

                  <h6 class="fw-bold text-dark mt-4 mb-2"><i class="bi bi-file-media me-2"></i> Upload Media Hero (Video / GIF / Gambar)</h6>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Jenis Media Hero *</label>
                    <select name="hero_type" class="form-select fw-bold" required>
                      <option value="image" <?= (isset($sekolah['hero_type']) && $sekolah['hero_type'] === 'image') ? 'selected' : '' ?>>🖼️ Gambar Static (JPG / PNG)</option>
                      <option value="gif" <?= (isset($sekolah['hero_type']) && $sekolah['hero_type'] === 'gif') ? 'selected' : '' ?>>🎬 GIF Animasi (.gif)</option>
                      <option value="video" <?= (isset($sekolah['hero_type']) && $sekolah['hero_type'] === 'video') ? 'selected' : '' ?>>🎥 Video (MP4 / WebM)</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Upload File Media Hero (Perangkat)</label>
                    <input type="file" name="hero_media_file" class="form-control mb-1" accept="image/*,video/*,.gif" />
                    <small class="text-muted">Upload file Gambar / GIF / Video MP4 langsung dari perangkat Anda.</small>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">URL / Path Media Saat Ini</label>
                    <input type="text" name="hero_media" class="form-control" value="<?= isset($sekolah['hero_media']) ? htmlspecialchars($sekolah['hero_media']) : 'dist/assets/img/photo1.png' ?>" placeholder="dist/assets/img/hero.gif atau video.mp4" />
                    <small class="text-muted">Path/URL media hero aktif.</small>
                  </div>

                  <h6 class="fw-bold text-dark mt-4 mb-2"><i class="bi bi-share me-2"></i> Tautan Media Sosial Footer</h6>
                  <div class="col-md-4"><label class="form-label fw-semibold">URL Facebook</label><input type="text" name="facebook_url" class="form-control" value="<?= isset($sekolah['facebook_url']) ? htmlspecialchars($sekolah['facebook_url']) : '' ?>" placeholder="https://facebook.com/..." /></div>
                  <div class="col-md-4"><label class="form-label fw-semibold">URL Instagram</label><input type="text" name="instagram_url" class="form-control" value="<?= isset($sekolah['instagram_url']) ? htmlspecialchars($sekolah['instagram_url']) : '' ?>" placeholder="https://instagram.com/..." /></div>
                  <div class="col-md-4"><label class="form-label fw-semibold">URL Youtube</label><input type="text" name="youtube_url" class="form-control" value="<?= isset($sekolah['youtube_url']) ? htmlspecialchars($sekolah['youtube_url']) : '' ?>" placeholder="https://youtube.com/..." /></div>
                </div>
              </div>

              <!-- Tab 3: Visi Misi & Sambutan -->
              <div class="tab-pane fade" id="tab-visimisi" role="tabpanel">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-compass me-2"></i> Sambutan Kepsek, Visi & Misi</h5>
                <div class="row g-3">
                  <div class="col-md-12">
                    <label class="form-label fw-semibold">Teks Sambutan Kepala Sekolah</label>
                    <textarea name="sambutan_kepsek" class="form-control" rows="4" placeholder="Sambutan resmi kepala sekolah..."><?= isset($sekolah['sambutan_kepsek']) ? htmlspecialchars($sekolah['sambutan_kepsek']) : '' ?></textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Visi Sekolah</label>
                    <textarea name="visi" class="form-control" rows="5"><?= isset($sekolah['visi']) ? htmlspecialchars($sekolah['visi']) : '' ?></textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Misi Sekolah</label>
                    <textarea name="misi" class="form-control" rows="5"><?= isset($sekolah['misi']) ? htmlspecialchars($sekolah['misi']) : '' ?></textarea>
                  </div>
                </div>
              </div>

              <!-- Tab 4: Kelola Fasilitas -->
              <div class="tab-pane fade" id="tab-fasilitas" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="fw-bold text-primary mb-0"><i class="bi bi-building-check me-2"></i> Daftar Fasilitas Modern Sekolah</h5>
                  <button type="button" class="btn btn-sm btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahFasilitas"><i class="bi bi-plus-lg me-1"></i> Tambah Fasilitas</button>
                </div>
                <div class="table-responsive mb-3">
                  <table class="table table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th width="5%">Urutan</th>
                        <th>Nama Fasilitas</th>
                        <th>Deskripsi Ringkas</th>
                        <th width="10%" class="text-end">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($fasilitas)): ?>
                        <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada data fasilitas.</td></tr>
                      <?php else: ?>
                        <?php foreach ($fasilitas as $fas): ?>
                          <tr>
                            <td class="text-center font-monospace fw-bold"><?= $fas['urutan'] ?></td>
                            <td class="fw-bold text-primary"><?= $fas['nama_fasilitas'] ?></td>
                            <td><?= $fas['deskripsi'] ?></td>
                            <td class="text-end">
                              <a href="<?= base_url('sekolah/hapus_fasilitas/' . $fas['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin menghapus fasilitas ini?')"><i class="bi bi-trash"></i></a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Tab 5: Kelola FAQ -->
              <div class="tab-pane fade" id="tab-faq" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="fw-bold text-primary mb-0"><i class="bi bi-question-circle me-2"></i> Daftar FAQ (Pertanyaan Sering Diajukan)</h5>
                  <button type="button" class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahFAQ"><i class="bi bi-plus-lg me-1"></i> Tambah Item FAQ</button>
                </div>
                <div class="table-responsive mb-3">
                  <table class="table table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th width="5%">Urutan</th>
                        <th>Pertanyaan</th>
                        <th>Jawaban</th>
                        <th width="10%" class="text-end">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($faqs)): ?>
                        <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada item FAQ.</td></tr>
                      <?php else: ?>
                        <?php foreach ($faqs as $f): ?>
                          <tr>
                            <td class="text-center font-monospace fw-bold"><?= $f['urutan'] ?></td>
                            <td class="fw-bold text-primary"><?= $f['pertanyaan'] ?></td>
                            <td><?= nl2br($f['jawaban']) ?></td>
                            <td class="text-end">
                              <a href="<?= base_url('sekolah/hapus_faq/' . $f['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin menghapus item FAQ ini?')"><i class="bi bi-trash"></i></a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>

            <div class="text-end mt-4 pt-3 border-top">
              <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 shadow-sm"><i class="bi bi-save-fill me-1"></i> Simpan Perubahan Perangkat Compro</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Modal Tambah FAQ -->
<div class="modal fade" id="modalTambahFAQ" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('sekolah/tambah_faq') ?>" method="post">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Tambah Item FAQ</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nomor Urutan Tampil</label>
            <input type="number" name="urutan" class="form-control" value="1" />
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Pertanyaan *</label>
            <input type="text" name="pertanyaan" class="form-control" placeholder="Contoh: Bagaimana alur pendaftaran PPDB?" required />
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Jawaban *</label>
            <textarea name="jawaban" class="form-control" rows="3" placeholder="Penjelasan rincian jawaban..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success fw-bold">Simpan Item FAQ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Tambah Fasilitas -->
<div class="modal fade" id="modalTambahFasilitas" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('sekolah/tambah_fasilitas') ?>" method="post">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold"><i class="bi bi-building-plus me-2"></i> Tambah Fasilitas Sekolah</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nomor Urutan Tampil</label>
            <input type="number" name="urutan" class="form-control" value="1" />
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Fasilitas *</label>
            <input type="text" name="nama_fasilitas" class="form-control" placeholder="Contoh: Studio Rekaman Podcast" required />
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi Ringkas *</label>
            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi fasilitas..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning fw-bold">Simpan Fasilitas</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const jenjangSelect = document.querySelector('select[name="jenjang"]');
  const mainNpsn = document.querySelector('input[name="npsn"]');
  const mainKepala = document.querySelector('input[name="kepala_sekolah"]');

  const npsnSd = document.querySelector('input[name="npsn_sd"]');
  const npsnSmp = document.querySelector('input[name="npsn_smp"]');
  const npsnSma = document.querySelector('input[name="npsn_sma"]');

  const kepalaSd = document.querySelector('input[name="kepala_sd"]');
  const kepalaSmp = document.querySelector('input[name="kepala_smp"]');
  const kepalaSma = document.querySelector('input[name="kepala_sma"]');

  function syncMainFields() {
    if (!jenjangSelect) return;
    const mode = jenjangSelect.value;
    if (mode === 'SD') {
      if (npsnSd && npsnSd.value) mainNpsn.value = npsnSd.value;
      if (kepalaSd && kepalaSd.value) mainKepala.value = kepalaSd.value;
    } else if (mode === 'SMP') {
      if (npsnSmp && npsnSmp.value) mainNpsn.value = npsnSmp.value;
      if (kepalaSmp && kepalaSmp.value) mainKepala.value = kepalaSmp.value;
    } else if (mode === 'SMA' || mode === 'SMK') {
      if (npsnSma && npsnSma.value) mainNpsn.value = npsnSma.value;
      if (kepalaSma && kepalaSma.value) mainKepala.value = kepalaSma.value;
    }
  }

  if (jenjangSelect) {
    jenjangSelect.addEventListener('change', syncMainFields);
  }

  if (npsnSd) npsnSd.addEventListener('input', function() { if (jenjangSelect && jenjangSelect.value === 'SD') mainNpsn.value = this.value; });
  if (npsnSmp) npsnSmp.addEventListener('input', function() { if (jenjangSelect && jenjangSelect.value === 'SMP') mainNpsn.value = this.value; });
  if (npsnSma) npsnSma.addEventListener('input', function() { if (jenjangSelect && (jenjangSelect.value === 'SMA' || jenjangSelect.value === 'SMK')) mainNpsn.value = this.value; });

  if (kepalaSd) kepalaSd.addEventListener('input', function() { if (jenjangSelect && jenjangSelect.value === 'SD') mainKepala.value = this.value; });
  if (kepalaSmp) kepalaSmp.addEventListener('input', function() { if (jenjangSelect && jenjangSelect.value === 'SMP') mainKepala.value = this.value; });
  if (kepalaSma) kepalaSma.addEventListener('input', function() { if (jenjangSelect && (jenjangSelect.value === 'SMA' || jenjangSelect.value === 'SMK')) mainKepala.value = this.value; });
});
</script>
