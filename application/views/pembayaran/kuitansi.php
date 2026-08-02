<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <title>Kuitansi Pembayaran - <?= $tagihan['nama_murid'] ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>" />

    <!-- html2pdf Bundle CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
      @page {
        size: A4 portrait;
        margin: 12mm;
      }
      @media print {
        .no-print { display: none !important; }
        body { background: white !important; padding: 0 !important; margin: 0 !important; }
        #kuitansi-pdf-area { padding: 0 !important; width: 100% !important; max-width: 100% !important; background: transparent !important; }
        .kuitansi-box { border: 4px double #212529 !important; box-shadow: none !important; width: 100% !important; max-width: 100% !important; }
      }
      #kuitansi-pdf-area {
        background: #ffffff;
        padding: 20px;
        max-width: 820px;
        margin: 0 auto;
        box-sizing: border-box;
      }
      .kuitansi-box {
        width: 100%;
        border: 4px double #212529;
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        box-sizing: border-box;
      }
    </style>
  </head>
  <body class="bg-light p-3 p-md-5">

    <!-- Top Action Buttons -->
    <div class="no-print mb-4 text-end d-flex justify-content-between align-items-center mx-auto" style="max-width: 820px;">
      <a href="<?= base_url('pembayaran/spp') ?>" class="btn btn-outline-secondary fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
      <div class="d-flex gap-2">
        <button onclick="exportKuitansiPDF()" class="btn btn-danger btn-lg fw-bold shadow">
          <i class="bi bi-file-earmark-pdf-fill me-2"></i> Download Kuitansi PDF
        </button>
        <button onclick="window.print()" class="btn btn-primary btn-lg fw-bold shadow">
          <i class="bi bi-printer-fill me-2"></i> Cetak Kuitansi
        </button>
      </div>
    </div>

    <!-- Printable Receipt Area with Outer Canvas Buffer Padding -->
    <div id="kuitansi-pdf-area">
      <div class="kuitansi-box">
        <!-- Header Receipt -->
        <div class="d-flex justify-content-between align-items-center border-bottom border-3 border-dark pb-3 mb-4">
          <div class="d-flex align-items-center gap-3">
            <img src="<?= base_url(isset($school_info['logo']) && $school_info['logo'] ? $school_info['logo'] : 'dist/assets/img/AdminLTELogo.png') ?>" width="55" height="55" class="rounded-circle shadow-sm" alt="Logo" />
            <div>
              <h3 class="fw-bold mb-0 text-primary" style="letter-spacing: 0.5px;"><?= isset($school_info['nama_sekolah']) ? strtoupper($school_info['nama_sekolah']) : 'ULTIMATE SCHOOL' ?></h3>
              <small class="text-secondary"><?= isset($school_info['alamat']) ? $school_info['alamat'] : 'Jl. Pendidikan No. 123, Jakarta' ?></small>
            </div>
          </div>
          <div class="text-end">
            <span class="badge text-bg-dark fs-6 mb-1">BUKTI RESMI</span>
            <h4 class="fw-bold text-uppercase mb-0 text-dark">KUITANSI PEMBAYARAN</h4>
            <small class="text-muted font-monospace">No. Trx: KUI-<?= str_pad($tagihan['id'], 6, '0', STR_PAD_LEFT) ?></small>
          </div>
        </div>

        <!-- Receipt Content Table -->
        <table class="table table-borderless fs-6 mb-4 align-middle">
          <tr>
            <th width="28%" class="text-secondary">Telah Diterima Dari</th>
            <td width="2%">:</td>
            <td><strong class="fs-5 text-dark"><?= $tagihan['nama_murid'] ?></strong> <span class="badge text-bg-primary ms-2"><?= $tagihan['nama_kelas'] ?></span></td>
          </tr>
          <tr>
            <th class="text-secondary">Jenis Pembayaran</th>
            <td>:</td>
            <td class="fw-semibold text-dark"><?= $tagihan['jenis'] ?> <?= $tagihan['bulan'] ? '('.$tagihan['bulan'].')' : '' ?></td>
          </tr>
          <tr>
            <th class="text-secondary">Tanggal Pembayaran</th>
            <td>:</td>
            <td><?= date('d F Y', strtotime(isset($tagihan['created_at']) ? $tagihan['created_at'] : date('Y-m-d'))) ?></td>
          </tr>
          <tr>
            <th class="text-secondary">Uang Sejumlah</th>
            <td>:</td>
            <td>
              <div class="p-2 px-3 bg-success-subtle border border-success text-success rounded-3 d-inline-block fw-bold fs-3">
                Rp <?= number_format($tagihan['terbayar'], 0, ',', '.') ?>
              </div>
            </td>
          </tr>
          <tr>
            <th class="text-secondary">Status Transaksi</th>
            <td>:</td>
            <td>
              <span class="badge text-bg-<?= ($tagihan['status'] === 'Lunas') ? 'success' : 'danger' ?> fs-6 px-3 py-2">
                <i class="bi bi-check-circle-fill me-1"></i> <?= strtoupper($tagihan['status']) ?>
              </span>
            </td>
          </tr>
        </table>

        <!-- Signatures & Official Stamp Footer -->
        <div class="row mt-5 pt-3 border-top">
          <div class="col-6 text-center">
            <p class="mb-5 text-secondary">Siswa / Pembayar,</p>
            <h6 class="fw-bold text-decoration-underline mb-0 text-dark"><?= $tagihan['nama_murid'] ?></h6>
            <small class="text-muted">Tanda Tangan Pembayar</small>
          </div>
          <div class="col-6 text-center">
            <p class="mb-5 text-secondary">Petugas Kasir TU,</p>
            <h6 class="fw-bold text-decoration-underline mb-0 text-dark">Bendahara TU Sekolah</h6>
            <small class="text-muted">Cap & Tanda Tangan Resmi</small>
          </div>
        </div>
      </div>
    </div>

    <!-- PDF Export Script -->
    <script>
      function exportKuitansiPDF() {
        const element = document.getElementById('kuitansi-pdf-area');
        const studentName = '<?= str_replace(' ', '_', $tagihan['nama_murid']) ?>';
        const opt = {
          margin:       [0.2, 0.2, 0.2, 0.2],
          filename:     'Kuitansi_' + studentName + '.pdf',
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { scale: 2, useCORS: true, scrollX: 0, scrollY: 0 },
          jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
      }
    </script>
  </body>
</html>
