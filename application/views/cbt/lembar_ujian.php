<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?> - Safe Exam Lock Engine</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>" />
    <style>
      /* Strict Exam Lockdown CSS Styles */
      body {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        overflow-x: hidden;
      }
      .soal-grid-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        max-height: 380px;
        overflow-y: auto;
        padding-right: 4px;
      }
      .soal-map-btn {
        width: 100%;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        border-radius: 10px;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
        border: 2px solid #cbd5e1;
        background-color: #ffffff;
        color: #334155;
      }
      .soal-map-btn:hover {
        border-color: #0284c7;
        color: #0284c7;
        transform: translateY(-2px);
      }
      .soal-map-btn.answered {
        background-color: #10b981 !important;
        border-color: #059669 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3);
      }
      
      /* Fullscreen Lockdown Overlay */
      #kioskLockOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.96);
        backdrop-filter: blur(12px);
        z-index: 99999;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        text-align: center;
        padding: 2rem;
      }
      .pulse-lock {
        animation: pulseSiren 1.5s infinite;
      }
      @keyframes pulseSiren {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
      }
    </style>
  </head>
  <body class="bg-body-tertiary">

    <!-- Fullscreen Lockdown Screen Overlay -->
    <div id="kioskLockOverlay">
      <div class="card bg-dark border-danger border-3 shadow-2xl p-5 rounded-5 text-center" style="max-width: 600px;">
        <div class="display-1 text-danger mb-3 pulse-lock"><i class="bi bi-shield-lock-fill"></i></div>
        <h2 class="fw-bold text-white mb-2">MODE UJIAN TERKUNCI</h2>
        <p class="text-secondary fs-5 mb-4">
          Lembar Ujian ini wajib dikerjakan dalam <strong>Mode Layar Penuh (Fullscreen Lockdown)</strong>. 
          Anda tidak diperkenankan keluar dari layar ini atau membuka aplikasi lain!
        </p>
        <button onclick="enterKioskMode()" class="btn btn-danger btn-lg fw-bold px-5 py-3 rounded-pill fs-4 shadow-lg">
          <i class="bi bi-fullscreen me-2"></i> Kunci & Masuk Layar Penuh
        </button>
        <small class="text-muted mt-3 d-block"><i class="bi bi-exclamation-triangle me-1"></i> Berpindah halaman lebih dari 3 kali akan membatalkan ujian secara otomatis.</small>
      </div>
    </div>

    <!-- Sticky Exam Top Header -->
    <nav class="navbar navbar-expand bg-primary text-white shadow sticky-top py-3">
      <div class="container-fluid px-4">
        <span class="navbar-brand fw-bold text-white fs-4 mb-0">
          <i class="bi bi-laptop-fill me-2"></i><?= $ujian['judul_ujian'] ?>
        </span>
        <div class="ms-auto d-flex align-items-center">
          <!-- Safe Mode Badge -->
          <div class="badge bg-danger text-white px-3 py-2 fs-6 me-3 d-none d-md-flex align-items-center shadow-sm">
            <i class="bi bi-shield-fill-check me-2"></i>
            <span>MODE AMAN | Pelanggaran: <strong id="violationCount" class="text-warning fs-5">0</strong>/3</span>
          </div>

          <!-- Timer -->
          <div class="bg-warning text-dark fw-bold px-3 py-2 rounded-3 fs-5 me-3 shadow-sm">
            <i class="bi bi-clock-fill me-2"></i>Sisa Waktu: <span id="timerDisplay">00:00:00</span>
          </div>

          <button type="button" class="btn btn-light btn-lg fw-bold text-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSelesai">
            <i class="bi bi-check-all me-1"></i> Selesai Ujian
          </button>
        </div>
      </div>
    </nav>

    <div class="container-fluid py-4 px-4">
      <!-- Cheating Alert Banner -->
      <div id="cheatWarningBanner" class="alert alert-danger p-3 rounded-4 mb-4 shadow border-0 d-none">
        <h5 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> PERINGATAN KECURANGAN UJIAN!</h5>
        <span>Sistem mendeteksi Anda keluar dari mode ujian / berpindah aplikasi. Maksimal 3 kali pelanggaran sebelum ujian dihentikan otomatis!</span>
      </div>

      <div class="row g-4">
        <!-- Left: Question Sheet (Google Forms Style) -->
        <div class="col-lg-8">
          <form id="formUjian">
            <?php foreach ($soal_list as $index => $s): ?>
              <?php $selected_ans = isset($jawaban_map[$s['id']]) ? $jawaban_map[$s['id']] : ''; ?>
              <div class="card shadow-sm border-0 rounded-4 mb-4" id="soal-block-<?= $s['id'] ?>">
                <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                  <span class="badge text-bg-primary fs-6">Soal No. <?= $index + 1 ?> dari <?= count($soal_list) ?></span>
                  <span class="badge text-bg-secondary">Bobot: <?= $s['bobot'] ?> Poin</span>
                </div>
                <div class="card-body p-4">
                  <h5 class="fw-semibold mb-4 text-dark fs-5"><?= nl2br($s['pertanyaan']) ?></h5>

                  <?php if ($s['jenis'] === 'Pilihan Ganda'): ?>
                    <div class="d-flex flex-column gap-3">
                      <?php foreach (array('A' => $s['pilihan_a'], 'B' => $s['pilihan_b'], 'C' => $s['pilihan_c'], 'D' => $s['pilihan_d'], 'E' => $s['pilihan_e']) as $opt_key => $opt_val): ?>
                        <?php if ($opt_val): ?>
                          <label class="p-3 border rounded-3 d-flex align-items-center cursor-pointer option-label <?= (strtoupper($selected_ans) === $opt_key) ? 'border-primary bg-primary-subtle fw-bold' : '' ?>">
                            <input
                              type="radio"
                              name="jawaban_<?= $s['id'] ?>"
                              value="<?= $opt_key ?>"
                              class="form-check-input me-3 opt-radio"
                              data-peserta-id="<?= encrypt_id($peserta['id']) ?>"
                              data-soal-id="<?= $s['id'] ?>"
                              data-index="<?= $index + 1 ?>"
                              <?= (strtoupper($selected_ans) === $opt_key) ? 'checked' : '' ?>
                            />
                            <span><strong><?= $opt_key ?>.</strong> <?= $opt_val ?></span>
                          </label>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <textarea
                      class="form-control form-control-lg essay-input"
                      rows="4"
                      placeholder="Tuliskan jawaban essay Anda di sini..."
                      data-peserta-id="<?= encrypt_id($peserta['id']) ?>"
                      data-soal-id="<?= $s['id'] ?>"
                      data-index="<?= $index + 1 ?>"
                    ><?= htmlspecialchars($selected_ans) ?></textarea>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </form>
        </div>

        <!-- Right: Question Map & Auto-Save Indicator -->
        <div class="col-lg-4">
          <div class="card shadow-sm border-0 rounded-4 sticky-top" style="top: 95px;">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
              <h6 class="fw-bold text-primary mb-0">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i> Peta Navigasi Soal
              </h6>
              <span class="badge text-bg-primary rounded-pill px-3 py-1 fw-bold">
                <span id="answeredCount"><?= count($jawaban_map) ?></span> / <?= count($soal_list) ?> Terjawab
              </span>
            </div>
            <div class="card-body p-3">
              <!-- Scrollable Clean 5-Column Grid -->
              <div class="soal-grid-container mb-3">
                <?php foreach ($soal_list as $index => $s): ?>
                  <?php $is_ans = isset($jawaban_map[$s['id']]) && !empty($jawaban_map[$s['id']]); ?>
                  <a
                    href="#soal-block-<?= $s['id'] ?>"
                    class="soal-map-btn <?= $is_ans ? 'answered' : '' ?>"
                    id="map-btn-<?= $index + 1 ?>"
                  >
                    <?= $index + 1 ?>
                  </a>
                <?php endforeach; ?>
              </div>

              <!-- Indicator Legend -->
              <div class="p-3 bg-light rounded-3 border">
                <div class="d-flex align-items-center justify-content-around small fw-semibold">
                  <div class="d-flex align-items-center">
                    <span class="d-inline-block rounded-2 bg-success me-2" style="width: 14px; height: 14px;"></span>
                    <span class="text-dark">Sudah Dijawab</span>
                  </div>
                  <div class="d-flex align-items-center">
                    <span class="d-inline-block rounded-2 bg-white border border-secondary me-2" style="width: 14px; height: 14px;"></span>
                    <span class="text-muted">Belum Dijawab</span>
                  </div>
                </div>
              </div>

              <div id="saveToast" class="alert alert-success border-success py-2 px-3 mt-3 mb-0 small text-center d-none rounded-3 fw-bold">
                <i class="bi bi-cloud-check-fill me-1"></i> Jawaban tersimpan otomatis.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Selesai Ujian -->
    <div class="modal fade" id="modalSelesai" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header bg-primary text-white p-3">
            <h5 class="modal-title fw-bold"><i class="bi bi-patch-question-fill me-2"></i> Konfirmasi Selesai Ujian</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4 text-center">
            <i class="bi bi-check-circle-fill text-success display-1 mb-3"></i>
            <h4 class="fw-bold mb-2">Apakah Anda Yakin Ingin Menyelesaikan Ujian?</h4>
            <p class="text-secondary fs-6 mb-0">
              Setelah dikumpulkan, lembar jawaban Anda akan otomatis dinilai dan Anda tidak dapat mengubah jawaban lagi.
            </p>
          </div>
          <div class="modal-footer bg-light p-3 justify-content-center gap-2">
            <button type="button" class="btn btn-outline-secondary px-4 fw-bold rounded-pill" data-bs-dismiss="modal">
              Batal & Lanjut Mengerjakan
            </button>
            <button type="button" onclick="confirmFinishExam()" class="btn btn-danger btn-lg px-4 fw-bold rounded-pill shadow">
              <i class="bi bi-send-check-fill me-1"></i> Ya, Kumpulkan Jawaban Now
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- jQuery & Lock Engine JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      let violations = 0;
      const maxViolations = 3;
      const pesertaId = '<?= encrypt_id($peserta['id']) ?>';
      let isKioskActive = false;
      let isSubmitting = false;

      // 1. Enter Fullscreen Mode
      function enterKioskMode() {
        if (document.documentElement.requestFullscreen) {
          document.documentElement.requestFullscreen().then(() => {
            $('#kioskLockOverlay').addClass('d-none');
            isKioskActive = true;
          }).catch(err => {
            $('#kioskLockOverlay').addClass('d-none');
            isKioskActive = true;
          });
        } else {
          $('#kioskLockOverlay').addClass('d-none');
          isKioskActive = true;
        }
      }

      // 2. Fullscreen Change & Violation Handler
      document.addEventListener('fullscreenchange', function () {
        if (isSubmitting) return;
        if (!document.fullscreenElement && isKioskActive) {
          $('#kioskLockOverlay').removeClass('d-none');
          triggerViolation('Keluar dari Mode Layar Penuh');
        }
      });

      function triggerViolation(reason) {
        if (isSubmitting) return;
        violations++;
        $('#violationCount').text(violations);
        $('#cheatWarningBanner').removeClass('d-none');

        if (violations >= maxViolations) {
          alert('🚫 UJIAN TERKUNCI: ANDA TELAH DILAPORKAN KARENA KELUAR HALAMAN 3 KALI!\n\nUjian Anda otomatis dihentikan dan dikumpulkan.');
          isSubmitting = true;
          window.onbeforeunload = null;
          window.location.href = '<?= base_url("cbt/selesai/") ?>' + pesertaId;
        }
      }

      document.addEventListener('visibilitychange', function() {
        if (isSubmitting) return;
        if (document.hidden && isKioskActive) {
          $('#kioskLockOverlay').removeClass('d-none');
          triggerViolation('Berpindah Tab / Halaman');
        }
      });

      window.addEventListener('blur', function() {
        if (isSubmitting) return;
        if (isKioskActive) {
          $('#kioskLockOverlay').removeClass('d-none');
          triggerViolation('Fokus Jendela Hilang');
        }
      });

      // Confirm Submit Action Fix
      function confirmFinishExam() {
        isSubmitting = true;
        window.onbeforeunload = null;
        window.location.href = '<?= base_url("cbt/selesai/") ?>' + pesertaId;
      }

      // 3. Disable Right Click, Copy, Cut, Paste & Selection
      document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
      });

      document.addEventListener('copy', function(e) { e.preventDefault(); });
      document.addEventListener('paste', function(e) { e.preventDefault(); });
      document.addEventListener('cut', function(e) { e.preventDefault(); });

      // 4. Block Key Combinations (F12, Alt+Tab, Ctrl+W, Ctrl+T, etc.)
      document.addEventListener('keydown', function(e) {
        if (isSubmitting) return true;
        if (
          e.keyCode === 123 || // F12
          (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) ||
          (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 83 || e.keyCode === 80 || e.keyCode === 87 || e.keyCode === 84 || e.keyCode === 78)) ||
          (e.altKey && (e.keyCode === 9 || e.keyCode === 115))
        ) {
          e.preventDefault();
          return false;
        }
      });

      // 5. Prevent Back History & Page Refresh
      history.pushState(null, null, location.href);
      window.onpopstate = function () {
        if (!isSubmitting) {
          history.pushState(null, null, location.href);
        }
      };

      window.onbeforeunload = function () {
        if (!isSubmitting) {
          return "Ujian sedang berlangsung dalam Mode Terkunci! Yakin ingin meninggalkan halaman?";
        }
      };

      // 6. Auto-save Answer Handling
      $(document.body).on('change', '.opt-radio', function () {
        const pId = $(this).data('peserta-id');
        const sId = $(this).data('soal-id');
        const idx = $(this).data('index');
        const val = $(this).val();

        saveAnswer(pId, sId, val, idx);
      });

      $(document.body).on('blur', '.essay-input', function () {
        const pId = $(this).data('peserta-id');
        const sId = $(this).data('soal-id');
        const idx = $(this).data('index');
        const val = $(this).val();

        saveAnswer(pId, sId, val, idx);
      });

      function saveAnswer(pId, sId, val, idx) {
        $.post('<?= base_url("cbt/simpan_jawaban_ajax") ?>', {
          peserta_id: pId,
          soal_id: sId,
          jawaban: val
        }, function (res) {
          if (val && val.trim() !== '') {
            $('#map-btn-' + idx).addClass('answered');
          } else {
            $('#map-btn-' + idx).removeClass('answered');
          }
          $('#answeredCount').text($('.soal-map-btn.answered').length);
          $('#saveToast').removeClass('d-none');
          setTimeout(() => $('#saveToast').addClass('d-none'), 2000);
        }, 'json');
      }

      // 7. Countdown Timer Script
      let totalSeconds = <?= $ujian['durasi'] * 60 ?>;
      const timerInterval = setInterval(function () {
        if (totalSeconds <= 0) {
          clearInterval(timerInterval);
          isSubmitting = true;
          window.onbeforeunload = null;
          alert('Waktu ujian telah habis! Jawaban Anda otomatis dikumpulkan.');
          window.location.href = '<?= base_url("cbt/selesai/" . $peserta["id"]) ?>';
        } else {
          totalSeconds--;
          const hrs = Math.floor(totalSeconds / 3600);
          const mins = Math.floor((totalSeconds % 3600) / 60);
          const secs = totalSeconds % 60;
          $('#timerDisplay').text(
            (hrs < 10 ? '0' + hrs : hrs) + ':' +
            (mins < 10 ? '0' + mins : mins) + ':' +
            (secs < 10 ? '0' + secs : secs)
          );
        }
      }, 1000);
    </script>
  </body>
</html>
