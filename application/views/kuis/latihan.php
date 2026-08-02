<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>" />
  </head>
  <body class="bg-body-tertiary">

    <!-- Top Banner Header (Google Forms Style) -->
    <div class="py-4 bg-success text-white shadow-sm mb-4">
      <div class="container">
        <h2 class="fw-bold mb-1"><i class="bi bi-ui-checks me-2"></i> Kuis Latihan Mandiri</h2>
        <p class="mb-0 fs-5 opacity-90">Mata Pelajaran: <?= $kuis['nama_mapel'] ?> | Jumlah: <?= count($soal_list) ?> Soal</p>
      </div>
    </div>

    <div class="container py-3">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form action="<?= base_url('kuislatihan/submit') ?>" method="post">
            <input type="hidden" name="kuis_id" value="<?= $kuis['id'] ?>" />

            <?php foreach ($soal_list as $index => $s): ?>
              <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between">
                  <span class="badge text-bg-success fs-6">Pertanyaan #<?= $index + 1 ?></span>
                  <span class="badge text-bg-secondary"><?= $s['tingkat_kesulitan'] ?></span>
                </div>
                <div class="card-body p-4">
                  <h5 class="fw-semibold mb-4 text-dark fs-5"><?= nl2br($s['pertanyaan']) ?></h5>

                  <div class="d-flex flex-column gap-3">
                    <?php foreach (array('A' => $s['pilihan_a'], 'B' => $s['pilihan_b'], 'C' => $s['pilihan_c'], 'D' => $s['pilihan_d'], 'E' => $s['pilihan_e']) as $opt_key => $opt_val): ?>
                      <?php if ($opt_val): ?>
                        <label class="p-3 border rounded-3 d-flex align-items-center cursor-pointer option-label">
                          <input type="radio" name="jawaban[<?= $s['id'] ?>]" value="<?= $opt_key ?>" class="form-check-input me-3" required />
                          <span><strong><?= $opt_key ?>.</strong> <?= $opt_val ?></span>
                        </label>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>

            <div class="d-grid gap-2 mb-5">
              <button type="submit" class="btn btn-success btn-lg fw-bold shadow">
                <i class="bi bi-send-check-fill me-2"></i> Selesai & Kirim Jawaban
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
