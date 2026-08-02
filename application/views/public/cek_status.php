<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cek Status Pendaftaran PPDB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/compro-custom.css') ?>" />
  </head>
  <body class="bg-body-tertiary d-flex flex-column min-vh-100">
    <div class="container py-5 my-auto">
      <div class="row justify-content-center">
        <div class="col-md-7">
          <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white text-center p-4">
              <h3 class="fw-bold mb-0"><i class="bi bi-search me-2"></i> Cek Status PPDB Online</h3>
            </div>
            <div class="card-body p-4">
              <form action="<?= base_url('home/cek_status') ?>" method="get" class="mb-4">
                <label class="form-label fw-bold">Masukkan Nomor Pendaftaran PPDB:</label>
                <div class="input-group input-group-lg">
                  <input type="text" name="no_pendaftaran" class="form-control" placeholder="Contoh: PPDB-20260802-1234" value="<?= isset($_GET['no_pendaftaran']) ? htmlspecialchars($_GET['no_pendaftaran']) : '' ?>" required />
                  <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-search me-1"></i> Cari</button>
                </div>
              </form>

              <?php if (isset($hasil) && $hasil): ?>
                <div class="border rounded-3 p-3 bg-light">
                  <h5 class="fw-bold text-primary mb-3">Hasil Pencarian:</h5>
                  <table class="table table-bordered bg-white">
                    <tr><th width="35%">No. Pendaftaran</th><td><span class="badge text-bg-dark fs-6"><?= $hasil['no_pendaftaran'] ?></span></td></tr>
                    <tr><th>Nama Lengkap</th><td class="fw-bold"><?= $hasil['nama_lengkap'] ?></td></tr>
                    <tr><th>Jurusan Dipilih</th><td><?= $hasil['jurusan_dipilih'] ?></td></tr>
                    <tr><th>Tanggal Daftar</th><td><?= date('d M Y', strtotime($hasil['created_at'])) ?></td></tr>
                    <tr>
                      <th>Status Pendaftaran</th>
                      <td>
                        <?php if ($hasil['status'] === 'Daftar'): ?>
                          <span class="badge text-bg-info fs-6">Menunggu Verifikasi</span>
                        <?php elseif ($hasil['status'] === 'Lulus' || $hasil['status'] === 'Daftar Ulang'): ?>
                          <span class="badge text-bg-success fs-6">Diterima / Lulus</span>
                        <?php else: ?>
                          <span class="badge text-bg-danger fs-6"><?= $hasil['status'] ?></span>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php if ($hasil['catatan']): ?>
                      <tr><th>Catatan Panitia</th><td><?= nl2br($hasil['catatan']) ?></td></tr>
                    <?php endif; ?>
                  </table>
                </div>
              <?php elseif (isset($_GET['no_pendaftaran'])): ?>
                <div class="alert alert-warning text-center">
                  <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> Nomor Pendaftaran tidak ditemukan. Pastikan nomor yang dimasukkan benar.
                </div>
              <?php endif; ?>

              <div class="text-center mt-4">
                <a href="<?= base_url('home') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
