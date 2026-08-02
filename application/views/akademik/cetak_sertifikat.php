<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <title>Sertifikat PDF - <?= isset($sertifikat['nama_murid']) ? $sertifikat['nama_murid'] : 'Sertifikat' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>" />

    <!-- html2pdf Bundle CDN for Instant PDF Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
      @media print {
        .no-print { display: none !important; }
        body { background: white !important; padding: 0 !important; }
        .cert-container { border: 10px double #0d6efd !important; box-shadow: none !important; }
      }
      .cert-container {
        border: 12px double #0d6efd;
        background: #ffffff;
        position: relative;
        padding: 50px 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      }
      .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.04;
        width: 320px;
        pointer-events: none;
      }
    </style>
  </head>
  <body class="bg-light p-4">
    <!-- Top Action Buttons -->
    <div class="no-print mb-4 text-end container d-flex justify-content-between align-items-center">
      <a href="<?= base_url('akademik/sertifikat') ?>" class="btn btn-outline-secondary fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke List
      </a>
      <div class="d-flex gap-2">
        <button onclick="exportToPDF()" class="btn btn-danger btn-lg fw-bold shadow">
          <i class="bi bi-file-earmark-pdf-fill me-2"></i> Download File PDF
        </button>
        <button onclick="window.print()" class="btn btn-primary btn-lg fw-bold shadow">
          <i class="bi bi-printer-fill me-2"></i> Cetak / Save to PDF (Browser)
        </button>
      </div>
    </div>

    <div class="container" id="pdf-area">
      <div class="cert-container text-center">
        <!-- Watermark Logo -->
        <img src="<?= base_url(isset($school_info['logo']) && $school_info['logo'] ? $school_info['logo'] : 'dist/assets/img/AdminLTELogo.png') ?>" class="watermark" alt="Watermark" />

        <!-- Header Logo & Sekolah -->
        <div class="d-flex justify-content-center align-items-center mb-3 gap-3">
          <img src="<?= base_url(isset($school_info['logo']) && $school_info['logo'] ? $school_info['logo'] : 'dist/assets/img/AdminLTELogo.png') ?>" width="60" height="60" class="rounded-circle shadow-sm" alt="Logo" />
          <div class="text-start">
            <h3 class="fw-bold mb-0 text-primary"><?= isset($school_info['nama_sekolah']) ? strtoupper($school_info['nama_sekolah']) : 'ULTIMATE SCHOOL' ?></h3>
            <small class="text-secondary"><?= isset($school_info['alamat']) ? $school_info['alamat'] : 'Jl. Pendidikan No. 123, Jakarta' ?></small>
          </div>
        </div>

        <hr class="my-3 border-2 opacity-50" />

        <!-- Title -->
        <h1 class="fw-bold text-uppercase display-4 text-dark mb-1" style="font-family: serif; letter-spacing: 2px;">
          <?= isset($sertifikat['jenis']) ? strtoupper($sertifikat['jenis']) : 'SERTIFIKAT PENGHARGAAN' ?>
        </h1>
        <p class="text-muted fs-6 mb-3">Nomor Seri: <strong class="text-dark font-monospace"><?= isset($sertifikat['nomor_seri']) ? $sertifikat['nomor_seri'] : 'CERT-2026-001' ?></strong></p>

        <p class="fs-5 text-secondary mb-1">Diberikan secara resmi kepada:</p>
        <h2 class="display-5 fw-bold text-primary my-2 text-decoration-underline" style="font-family: serif;">
          <?= isset($sertifikat['nama_murid']) ? $sertifikat['nama_murid'] : 'Siswa Ultimate School' ?>
        </h2>
        <p class="fs-6 text-muted mb-4">NISN: <?= isset($sertifikat['nisn']) ? $sertifikat['nisn'] : '-' ?> | Kelas: <?= isset($sertifikat['nama_kelas']) ? $sertifikat['nama_kelas'] : '-' ?></p>

        <p class="fs-5 text-dark lead mx-auto mb-4" style="max-width: 750px;">
          <?= isset($sertifikat['deskripsi']) ? nl2br($sertifikat['deskripsi']) : 'Atas dedikasi, keikutsertaan, dan prestasi luar biasa yang telah dicapai dalam kegiatan akademik dan kesiswaan.' ?>
        </p>

        <!-- Signatures & QR Code -->
        <div class="row align-items-center mt-4 pt-2">
          <div class="col-4 text-start">
            <div class="p-3 bg-light rounded-3 border d-inline-block text-center">
              <i class="bi bi-qr-code-scan fs-2 text-primary mb-1"></i>
              <div class="small fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> VERIFIED QR</div>
              <small class="text-muted font-monospace"><?= isset($sertifikat['qr_code']) ? $sertifikat['qr_code'] : 'QR-12345' ?></small>
            </div>
          </div>

          <div class="col-4 text-center">
            <small class="text-muted">Tanggal Diterbitkan:</small>
            <p class="fw-bold fs-6 mb-0"><?= date('d F Y', strtotime(isset($sertifikat['tanggal_terbit']) ? $sertifikat['tanggal_terbit'] : date('Y-m-d'))) ?></p>
          </div>

          <div class="col-4 text-end">
            <p class="mb-4">Kepala Sekolah,</p>
            <h5 class="fw-bold text-decoration-underline mb-0"><?= isset($school_info['kepala_sekolah']) ? $school_info['kepala_sekolah'] : 'Dr. H. Ahmad Dahlan, M.Pd.' ?></h5>
            <small class="text-muted">NIP. 198501012010011001</small>
          </div>
        </div>
      </div>
    </div>

    <!-- PDF Export Script -->
    <script>
      function exportToPDF() {
        const element = document.getElementById('pdf-area');
        const studentName = '<?= isset($sertifikat['nama_murid']) ? str_replace(' ', '_', $sertifikat['nama_murid']) : 'Siswa' ?>';
        const opt = {
          margin:       [0.2, 0.2, 0.2, 0.2],
          filename:     'Sertifikat_' + studentName + '.pdf',
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { scale: 2, useCORS: true },
          jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
        };
        html2pdf().set(opt).from(element).save();
      }
    </script>
  </body>
</html>
