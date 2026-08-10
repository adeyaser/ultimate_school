<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Konfirmasi Kertas Ujian</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="<?= base_url('cbt') ?>">Ujian CBT</a></li><li class="breadcrumb-item active">Token</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white p-4 rounded-top-4 text-center">
              <h4 class="fw-bold mb-1"><i class="bi bi-shield-lock-fill me-2"></i> Konfirmasi Token Ujian</h4>
              <p class="mb-0 opacity-90"><?= $ujian['judul_ujian'] ?></p>
            </div>
            <div class="card-body p-4 p-md-5">

              <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-3">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $this->session->flashdata('error') ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <div class="p-3 bg-light rounded-3 mb-4 border">
                <table class="table table-borderless table-sm mb-0">
                  <tr><th width="40%">Mata Pelajaran</th><td>: <?= $ujian['nama_mapel'] ?></td></tr>
                  <tr><th>Pengawas / Guru</th><td>: <?= $ujian['nama_guru'] ?></td></tr>
                  <tr><th>Durasi Pengerjaan</th><td>: <strong><?= $ujian['durasi'] ?> Menit</strong></td></tr>
                  <tr><th>Jumlah Soal</th><td>: <strong><?= $ujian['jumlah_soal'] ?> Soal</strong></td></tr>
                </table>
              </div>

              <form action="<?= base_url('cbt/mulai_ujian') ?>" method="post">
                <input type="hidden" name="ujian_id" value="<?= encrypt_id($ujian['id']) ?>" />
                <div class="mb-4">
                  <label class="form-label fw-bold text-center d-block fs-5">Masukkan Token Ujian (6 Karakter):</label>
                  <input type="text" name="token" class="form-control form-control-lg text-center fw-bold fs-3 text-primary text-uppercase" placeholder="AAAAAA" maxlength="6" required autofocus />
                  <small class="form-text text-muted text-center d-block mt-2">Minta token ujian kepada pengawas ujian di ruangan.</small>
                </div>

                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary btn-lg fw-bold shadow">
                    <i class="bi bi-play-circle-fill me-2"></i> Mulai Kerjakan Ujian
                  </button>
                  <a href="<?= base_url('cbt') ?>" class="btn btn-outline-secondary">Batal</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
