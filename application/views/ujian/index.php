<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Jadwal & Sesi Ujian Online (CBT)</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Jadwal Ujian</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle-fill me-2"></i><?= $this->session->flashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <div class="card shadow-sm mb-4">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
          <h3 class="card-title fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> Daftar Sesi Ujian Online Aktif</h3>
          <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahUjian"><i class="bi bi-plus-lg me-1"></i> Jadwalkan Ujian Baru</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
              <thead class="table-light">
                <tr>
                  <th>Token Ujian</th>
                  <th>Judul Ujian</th>
                  <th>Jenis</th>
                  <th>Mata Pelajaran</th>
                  <th>Kelas Target</th>
                  <th>Waktu Pelaksanaan</th>
                  <th>Durasi</th>
                  <th>Status</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($ujian_list)): ?>
                  <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada sesi ujian yang dijadwalkan.</td></tr>
                <?php else: ?>
                  <?php foreach ($ujian_list as $u): ?>
                    <tr>
                      <td><span class="badge text-bg-warning fs-6 px-3 py-2 fw-bold"><?= $u['token'] ?></span></td>
                      <td class="fw-bold"><?= $u['judul_ujian'] ?></td>
                      <td><span class="badge text-bg-primary"><?= $u['jenis_ujian'] ?></span></td>
                      <td><?= $u['nama_mapel'] ?></td>
                      <td><span class="badge text-bg-info"><?= $u['nama_kelas'] ?></span></td>
                      <td><small><?= date('d M H:i', strtotime($u['tanggal_mulai'])) ?> s.d <?= date('d M H:i', strtotime($u['tanggal_selesai'])) ?></small></td>
                      <td><?= $u['durasi'] ?> Mnt</td>
                      <td>
                        <?php if ($u['is_active']): ?>
                          <span class="badge text-bg-success">Aktif / Berjalan</span>
                        <?php else: ?>
                          <span class="badge text-bg-secondary">Selesai / Nonaktif</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <a href="<?= base_url('ujian/reset_peserta/' . encrypt_id($u['id'])) ?>" onclick="return confirm('Reset seluruh pengerjaan peserta pada ujian ini? Seluruh jawaban peserta akan dihapus agar peserta bisa mengulangi ujian.')" class="btn btn-sm btn-outline-warning me-1" title="Reset Pengerjaan Peserta">
                          <i class="bi bi-arrow-repeat"></i> Reset Ujian
                        </a>
                        <?php if ($u['is_active']): ?>
                          <a href="<?= base_url('ujian/toggle_status/' . encrypt_id($u['id']) . '/0') ?>" class="btn btn-sm btn-outline-danger">Nonaktifkan</a>
                        <?php else: ?>
                          <a href="<?= base_url('ujian/toggle_status/' . encrypt_id($u['id']) . '/1') ?>" class="btn btn-sm btn-outline-success">Aktifkan</a>
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

<!-- Modal Tambah Sesi Ujian -->
<div class="modal fade" id="modalTambahUjian" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?= base_url('ujian/simpan_ujian') ?>" method="post">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-clock-fill me-2"></i> Jadwalkan Sesi Ujian CBT Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label fw-semibold">Pilih Paket Bank Soal *</label>
              <select name="bank_soal_id" class="form-select" required>
                <?php foreach ($bank_list as $b): ?>
                  <option value="<?= $b['id'] ?>"><?= $b['kode_soal'] ?> - <?= $b['judul'] ?> (<?= $b['jumlah_soal'] ?> Soal)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Judul Ujian *</label>
              <input type="text" name="judul_ujian" class="form-control" placeholder="Contoh: Ujian Tengah Semester (PTS) Matematika" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kelas Peserta *</label>
              <select name="kelas_id" class="form-select" required>
                <?php foreach ($kelas_list as $k): ?>
                  <option value="<?= $k['id'] ?>"><?= $k['nama_kelas'] ?> (<?= $k['tingkat'] ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Jenis Ujian</label>
              <select name="jenis_ujian" class="form-select">
                <option value="Harian">Ulangan Harian</option>
                <option value="PTS">PTS</option>
                <option value="PAS">PAS</option>
                <option value="Try Out">Try Out</option>
                <option value="Remidi">Remidi</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Waktu Mulai *</label>
              <input type="datetime-local" name="tanggal_mulai" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Waktu Selesai *</label>
              <input type="datetime-local" name="tanggal_selesai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+2 hours')) ?>" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Durasi Pengerjaan (Menit)</label>
              <input type="number" name="durasi" class="form-control" value="60" required />
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="is_shuffle" id="shuffleCheck" value="1">
                <label class="form-check-label fw-bold" for="shuffleCheck">Acak Urutan Soal (Shuffle Questions)</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold">Terbitkan Sesi Ujian</button>
        </div>
      </form>
    </div>
  </div>
</div>
