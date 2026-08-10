<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Daftar Ujian Online Saya (CBT)</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Ujian CBT</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="row g-4">
        <?php if (empty($ujian_list)): ?>
          <div class="col-12">
            <div class="card p-5 text-center text-muted shadow-sm">
              <i class="bi bi-calendar-x fs-1 text-warning mb-2"></i>
              <h4>Belum Ada Ujian Aktif Saat Ini</h4>
              <p>Tidak ada sesi ujian yang dijadwalkan untuk kelas Anda.</p>
            </div>
          </div>
        <?php else: ?>
          <?php foreach ($ujian_list as $u): ?>
            <div class="col-md-6 col-lg-4">
              <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4 p-3">
                  <span class="badge text-bg-warning float-end"><?= $u['jenis_ujian'] ?></span>
                  <h5 class="fw-bold mb-0 text-truncate"><?= $u['judul_ujian'] ?></h5>
                  <small class="opacity-90"><?= $u['nama_mapel'] ?></small>
                </div>
                <div class="card-body">
                  <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between px-0">
                      <span><i class="bi bi-alarm me-2 text-warning"></i> Durasi:</span>
                      <strong class="text-primary"><?= $u['durasi'] ?> Menit</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                      <span><i class="bi bi-file-earmark-text me-2 text-info"></i> Jumlah Soal:</span>
                      <strong><?= $u['jumlah_soal'] ?> Soal</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                      <span><i class="bi bi-clock me-2 text-secondary"></i> Selesai Sesi:</span>
                      <small><?= date('d M H:i', strtotime($u['tanggal_selesai'])) ?></small>
                    </li>
                  </ul>

                  <?php if ($u['status_peserta'] === 'Selesai'): ?>
                    <div class="alert alert-success text-center mb-2 fw-bold py-2">
                      <i class="bi bi-check-circle-fill me-1"></i> Selesai | Nilai: <?= round($u['nilai_total']) ?>
                    </div>
                    <a href="<?= base_url('cbt/ulangi_ujian/' . $u['id']) ?>" onclick="return confirm('Ulangi pengerjaan ujian ini? Jawaban pengerjaan sebelumnya akan di-reset.')" class="btn btn-outline-warning btn-sm w-100 fw-bold">
                      <i class="bi bi-arrow-repeat me-1"></i> Ulangi Pengerjaan Ujian (Remidi)
                    </a>
                  <?php else: ?>
                    <a href="<?= base_url('cbt/konfirmasi/' . $u['id']) ?>" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                      <i class="bi bi-box-arrow-in-right me-2"></i> Ikuti Ujian Ini
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>
