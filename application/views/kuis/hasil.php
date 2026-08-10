<!-- MathJax for rendering Mathematical LaTeX equations -->
<script>
MathJax = {
  tex: {
    inlineMath: [['$', '$'], ['\\(', '\\)']],
    displayMath: [['$$', '$$'], ['\\[', '\\]']]
  },
  svg: { fontCache: 'global' }
};
</script>
<script type="text/javascript" id="MathJax-script" async
  src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
</script>

<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Hasil & Pembahasan Kuis</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="<?= base_url('kuislatihan') ?>">Latihan Soal</a></li><li class="breadcrumb-item active">Hasil Kuis</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="row justify-content-center mb-4">
        <div class="col-md-8">
          <div class="card shadow-sm border-0 rounded-4 bg-primary text-white text-center p-4">
            <div class="card-body">
              <h5 class="text-uppercase opacity-90 mb-1">Skor Kuis Latihan Mandiri</h5>
              <h1 class="display-3 fw-bold my-2"><?= $kuis['nilai'] ?></h1>
              <div class="d-flex justify-content-center gap-4 mt-3">
                <span class="badge text-bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i> Benar: <?= $kuis['jawaban_benar'] ?></span>
                <span class="badge text-bg-danger fs-6 px-3 py-2"><i class="bi bi-x-circle me-1"></i> Salah: <?= $kuis['jawaban_salah'] ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Question Answer Breakdown -->
      <div class="row justify-content-center">
        <div class="col-md-8">
          <h4 class="fw-bold mb-3"><i class="bi bi-journal-check me-2 text-primary"></i> Pembahasan Soal</h4>

          <?php foreach ($soal_list as $index => $s): ?>
            <?php 
              $user_ans_item = isset($user_ans[$s['id']]) ? $user_ans[$s['id']] : null;
              $is_correct = ($user_ans_item && $user_ans_item['is_benar'] == 1);
            ?>
            <div class="card shadow-sm border-0 rounded-4 mb-3 border-start border-5 <?= $is_correct ? 'border-success' : 'border-danger' ?>">
              <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Soal #<?= $index + 1 ?></span>
                <?php if ($is_correct): ?>
                  <span class="badge text-bg-success"><i class="bi bi-check-lg me-1"></i> Benar</span>
                <?php else: ?>
                  <span class="badge text-bg-danger"><i class="bi bi-x-lg me-1"></i> Salah</span>
                <?php endif; ?>
              </div>
              <div class="card-body">
                <p class="fw-semibold mb-3 fs-5"><?= nl2br($s['pertanyaan']) ?></p>
                <div class="p-3 bg-light rounded-3 mb-2">
                  <p class="mb-1"><strong>Jawaban Anda:</strong> <span class="<?= $is_correct ? 'text-success fw-bold' : 'text-danger fw-bold' ?>"><?= isset($user_ans_item['jawaban']) ? strtoupper($user_ans_item['jawaban']) : '-' ?></span></p>
                  <p class="mb-0 text-success fw-bold"><strong>Kunci Jawaban Benar:</strong> <?= strtoupper($s['kunci_jawaban']) ?></p>
                </div>
                <?php if ($s['pembahasan']): ?>
                  <div class="alert alert-info py-2 px-3 mb-0 small">
                    <strong><i class="bi bi-info-circle-fill me-1"></i> Pembahasan:</strong> <?= nl2br($s['pembahasan']) ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>

          <div class="text-center my-4">
            <a href="<?= base_url('kuislatihan') ?>" class="btn btn-primary btn-lg fw-bold"><i class="bi bi-arrow-left me-1"></i> Kembali ke Latihan Soal</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
