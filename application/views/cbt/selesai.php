<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Hasil Ujian CBT</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="<?= base_url('cbt') ?>">Ujian CBT</a></li><li class="breadcrumb-item active">Selesai</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-6 text-center py-4">
          <div class="card shadow-lg border-0 rounded-4 p-4">
            <div class="card-body">
              <div class="text-success fs-1 mb-3"><i class="bi bi-check-circle-fill"></i></div>
              <h2 class="fw-bold mb-2">Ujian Berhasil Diselesaikan!</h2>
              <p class="text-secondary mb-4">Terima kasih. Jawaban Anda telah tersimpan dan terhitung ke dalam sistem penilaian.</p>

              <?php if (isset($hasil['nilai_total'])): ?>
                <div class="p-4 bg-light rounded-4 border mb-4">
                  <h6 class="text-uppercase text-secondary fw-bold mb-1">Skor Akhir Ujian</h6>
                  <h1 class="display-3 fw-bold text-primary mb-2"><?= round($hasil['nilai_total']) ?></h1>
                  <?php if ($hasil['is_lulus']): ?>
                    <span class="badge text-bg-success fs-6 px-4 py-2 rounded-pill"><i class="bi bi-patch-check-fill me-1"></i> DIKATEGORIKAN LULUS (Skor ≥ 60)</span>
                  <?php else: ?>
                    <span class="badge text-bg-warning text-dark fs-6 px-4 py-2 rounded-pill"><i class="bi bi-exclamation-diamond-fill me-1"></i> BELUM LULUS (Skor < 60)</span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <div class="d-grid gap-2">
                <a href="<?= base_url('cbt') ?>" class="btn btn-primary btn-lg fw-bold"><i class="bi bi-list-check me-2"></i> Kembali ke Daftar Ujian</a>
                <a href="<?= base_url('kuislatihan') ?>" class="btn btn-outline-info fw-bold"><i class="bi bi-ui-checks me-2"></i> Coba Latihan Soal Mandiri</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
