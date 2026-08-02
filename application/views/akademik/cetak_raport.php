<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <title>Raport PDF - <?= $murid['full_name'] ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>" />

    <!-- html2pdf Bundle CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
      @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
      }
    </style>
  </head>
  <body class="bg-white p-5">
    <div class="no-print mb-4 text-end d-flex justify-content-between align-items-center">
      <a href="<?= base_url('akademik/raport/' . $murid['id']) ?>" class="btn btn-outline-secondary fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
      <div class="d-flex gap-2">
        <button onclick="exportRaportPDF()" class="btn btn-danger btn-lg fw-bold shadow">
          <i class="bi bi-file-earmark-pdf-fill me-2"></i> Download Raport PDF
        </button>
        <button onclick="window.print()" class="btn btn-primary btn-lg fw-bold shadow">
          <i class="bi bi-printer-fill me-2"></i> Cetak / Save to PDF (Browser)
        </button>
      </div>
    </div>

    <div id="raport-pdf-area">
      <!-- Kop Sekolah -->
      <div class="text-center pb-3 mb-4 border-bottom border-3 border-dark">
        <h2 class="fw-bold mb-1"><?= isset($school_info['nama_sekolah']) ? strtoupper($school_info['nama_sekolah']) : 'ULTIMATE SCHOOL' ?></h2>
        <p class="mb-0">Alamat: <?= isset($school_info['alamat']) ? $school_info['alamat'] : 'Jl. Pendidikan No. 123' ?> | NPSN: <?= isset($school_info['npsn']) ? $school_info['npsn'] : '12345678' ?></p>
        <h4 class="fw-bold text-uppercase mt-3 text-decoration-underline">LAPORAN HASIL BELAJAR (RAPORT)</h4>
      </div>

      <table class="table table-borderless mb-4 fs-6">
        <tr><th width="20%">Nama Siswa</th><td width="30%">: <strong><?= $murid['full_name'] ?></strong></td><th width="20%">Kelas</th><td width="30%">: <?= $murid['nama_kelas'] ?></td></tr>
        <tr><th>NISN / NIS</th><td>: <?= $murid['nisn'] ?> / <?= $murid['nis'] ?></td><th>Tahun Ajaran</th><td>: <?= isset($raport['nama_tahun_ajaran']) ? $raport['nama_tahun_ajaran'] : '2026/2027' ?></td></tr>
      </table>

      <table class="table table-bordered align-middle mb-2" style="font-size: 13px;">
        <thead class="table-light text-center">
          <tr>
            <th rowspan="2" width="4%" style="vertical-align: middle;">No</th>
            <th rowspan="2" style="vertical-align: middle;">Mata Pelajaran</th>
            <th rowspan="2" width="6%" style="vertical-align: middle;">KKM</th>
            <th colspan="4" class="text-center">Komponen Penilaian</th>
            <th rowspan="2" width="8%" style="vertical-align: middle;">Nilai Akhir</th>
            <th rowspan="2" width="7%" style="vertical-align: middle;">Predikat</th>
            <th rowspan="2" width="10%" style="vertical-align: middle;">Ketuntasan</th>
          </tr>
          <tr>
            <th width="8%">Rata² UH<br><small>(20%)</small></th>
            <th width="8%">Rata² Tugas<br><small>(20%)</small></th>
            <th width="7%">UTS<br><small>(30%)</small></th>
            <th width="7%">UKK<br><small>(30%)</small></th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $total_uh = 0; $total_tugas = 0; $total_uts = 0; $total_ukk = 0; $total_akhir = 0;
            $count = count($nilai_list);
          ?>
          <?php foreach ($nilai_list as $idx => $n): 
            $uh    = isset($n['nilai_harian']) ? (float)$n['nilai_harian'] : 0;
            $tugas = isset($n['nilai_tugas'])  ? (float)$n['nilai_tugas']  : 0;
            $uts   = isset($n['nilai_pts'])    ? (float)$n['nilai_pts']    : 0;
            $ukk   = isset($n['nilai_pas'])    ? (float)$n['nilai_pas']    : 0;
            $akhir = isset($n['nilai_akhir'])  ? (float)$n['nilai_akhir']  : 0;
            $total_uh += $uh; $total_tugas += $tugas; $total_uts += $uts; $total_ukk += $ukk; $total_akhir += $akhir;
          ?>
            <tr>
              <td class="text-center"><?= $idx + 1 ?></td>
              <td class="fw-bold"><?= $n['nama_mapel'] ?></td>
              <td class="text-center"><?= $n['kkm'] ?></td>
              <td class="text-center"><?= $uh > 0 ? number_format($uh, 1) : '-' ?></td>
              <td class="text-center"><?= $tugas > 0 ? number_format($tugas, 1) : '-' ?></td>
              <td class="text-center"><?= $uts > 0 ? number_format($uts, 1) : '-' ?></td>
              <td class="text-center"><?= $ukk > 0 ? number_format($ukk, 1) : '-' ?></td>
              <td class="text-center font-monospace fw-bold fs-5"><?= number_format($akhir, 1) ?></td>
              <td class="text-center fw-bold"><?= $n['predikat'] ?></td>
              <td class="text-center fw-bold text-<?= $n['is_tuntas'] ? 'success' : 'danger' ?>"><?= $n['is_tuntas'] ? 'TUNTAS' : 'BELUM TUNTAS' ?></td>
            </tr>
          <?php endforeach; ?>
          <!-- Rata-rata keseluruhan -->
          <tr class="fw-bold" style="background-color: #fff3cd;">
            <td colspan="3" class="text-end">Rata-Rata:</td>
            <td class="text-center"><?= $count > 0 ? number_format($total_uh / $count, 1) : '-' ?></td>
            <td class="text-center"><?= $count > 0 ? number_format($total_tugas / $count, 1) : '-' ?></td>
            <td class="text-center"><?= $count > 0 ? number_format($total_uts / $count, 1) : '-' ?></td>
            <td class="text-center"><?= $count > 0 ? number_format($total_ukk / $count, 1) : '-' ?></td>
            <td class="text-center fs-5"><?= $count > 0 ? number_format($total_akhir / $count, 1) : '-' ?></td>
            <td colspan="2"></td>
          </tr>
        </tbody>
      </table>

      <p class="small text-muted mb-4"><strong>Rumus:</strong> (Rata² UH × 20%) + (Rata² Tugas × 20%) + (UTS × 30%) + (UKK × 30%)</p>

      <div class="row mt-5">
        <div class="col-6 text-center">
          <p class="mb-5">Wali Kelas,</p>
          <p class="fw-bold text-decoration-underline mb-0">Wali Kelas <?= $murid['nama_kelas'] ?></p>
        </div>
        <div class="col-6 text-center">
          <p class="mb-5">Kepala Sekolah,</p>
          <p class="fw-bold text-decoration-underline mb-0"><?= isset($school_info['kepala_sekolah']) ? $school_info['kepala_sekolah'] : 'Kepala Sekolah' ?></p>
        </div>
      </div>
    </div>

    <!-- PDF Export Script -->
    <script>
      function exportRaportPDF() {
        const element = document.getElementById('raport-pdf-area');
        const studentName = '<?= str_replace(' ', '_', $murid['full_name']) ?>';
        const opt = {
          margin:       [0.3, 0.3, 0.3, 0.3],
          filename:     'Raport_' + studentName + '.pdf',
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { scale: 2, useCORS: true },
          jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
      }
    </script>
  </body>
</html>
