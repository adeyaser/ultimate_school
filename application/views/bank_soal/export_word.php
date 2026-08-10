<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="utf-8">
<title><?= isset($bank_soal['judul']) ? $bank_soal['judul'] : 'Naskah Soal Ujian' ?></title>
<style>
  body {
    font-family: 'Times New Roman', Times, serif;
    font-size: 12pt;
    line-height: 1.4;
    color: #000000;
  }
  .header-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }
  .header-table td {
    padding: 4px;
    font-size: 11pt;
  }
  .kop-sekolah {
    text-align: center;
    border-bottom: 3px double #000;
    padding-bottom: 10px;
    margin-bottom: 15px;
  }
  .kop-sekolah h2 {
    margin: 0;
    font-size: 16pt;
    text-transform: uppercase;
  }
  .kop-sekolah h3 {
    margin: 3px 0;
    font-size: 14pt;
    text-transform: uppercase;
  }
  .section-title {
    font-weight: bold;
    font-size: 12pt;
    margin-top: 15px;
    margin-bottom: 10px;
    text-decoration: underline;
  }
  .soal-item {
    margin-bottom: 15px;
  }
  .pertanyaan {
    margin-bottom: 6px;
    text-align: justify;
  }
  .pilihan-table {
    width: 100%;
    margin-left: 20px;
    margin-bottom: 10px;
  }
  .pilihan-table td {
    vertical-align: top;
    padding: 2px 5px;
  }
  .page-break {
    page-break-before: always;
  }
  .kunci-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }
  .kunci-table th, .kunci-table td {
    border: 1px solid #000;
    padding: 6px;
    text-align: left;
  }
  .kunci-table th {
    background-color: #f2f2f2;
  }
</style>
</head>
<body>

  <!-- Kop Naskah Ujian -->
  <div class="kop-sekolah">
    <h2><?= isset($school_info['nama_sekolah']) ? strtoupper($school_info['nama_sekolah']) : 'ULTIMATE SCHOOL' ?></h2>
    <h3>NASKAH SOAL UJIAN ONLINE / CBT</h3>
    <p style="margin: 0; font-size: 10pt;"><?= isset($school_info['alamat']) ? $school_info['alamat'] : '' ?></p>
  </div>

  <table class="header-table">
    <tr>
      <td width="18%"><strong>Mata Pelajaran</strong></td>
      <td width="2%">:</td>
      <td width="30%"><?= isset($bank_soal['nama_mapel']) ? $bank_soal['nama_mapel'] : '-' ?></td>
      <td width="18%"><strong>Kode Soal</strong></td>
      <td width="2%">:</td>
      <td width="30%"><?= isset($bank_soal['kode_soal']) ? $bank_soal['kode_soal'] : '-' ?></td>
    </tr>
    <tr>
      <td><strong>Kelas / Jenjang</strong></td>
      <td>:</td>
      <td><?= isset($bank_soal['nama_kelas']) ? $bank_soal['nama_kelas'] : '-' ?> (<?= isset($bank_soal['jenjang']) ? $bank_soal['jenjang'] : '' ?>)</td>
      <td><strong>Jumlah Soal</strong></td>
      <td>:</td>
      <td><?= isset($bank_soal['jumlah_soal']) ? $bank_soal['jumlah_soal'] : count($soal_list) ?> Butir Soal</td>
    </tr>
    <tr>
      <td><strong>Topik / Judul</strong></td>
      <td>:</td>
      <td><?= isset($bank_soal['judul']) ? $bank_soal['judul'] : '-' ?></td>
      <td><strong>Waktu Pengerjaan</strong></td>
      <td>:</td>
      <td><?= isset($bank_soal['durasi']) ? $bank_soal['durasi'] : '60' ?> Menit</td>
    </tr>
  </table>

  <hr style="border: 1px solid #000;" />

  <p><strong>PETUNJUK UMUM:</strong></p>
  <ol style="margin-top: 0; padding-left: 20px;">
    <li>Bacalah setiap butir pertanyaan dengan teliti sebelum memberikan jawaban.</li>
    <li>Pilihlah salah satu jawaban yang Anda anggap paling benar untuk soal pilihan ganda.</li>
  </ol>

  <!-- Butir Soal -->
  <div class="section-title">DAFTAR SOAL UJIAN</div>

  <?php if (!empty($soal_list)): ?>
    <?php foreach ($soal_list as $index => $s): ?>
      <div class="soal-item">
        <div class="pertanyaan">
          <strong><?= $index + 1 ?>.</strong> <?= nl2br($s['pertanyaan']) ?>
        </div>

        <?php if ($s['jenis'] === 'Pilihan Ganda'): ?>
          <table class="pilihan-table">
            <?php if (!empty($s['pilihan_a'])): ?>
              <tr><td width="3%">A.</td><td><?= $s['pilihan_a'] ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($s['pilihan_b'])): ?>
              <tr><td>B.</td><td><?= $s['pilihan_b'] ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($s['pilihan_c'])): ?>
              <tr><td>C.</td><td><?= $s['pilihan_c'] ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($s['pilihan_d'])): ?>
              <tr><td>D.</td><td><?= $s['pilihan_d'] ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($s['pilihan_e'])): ?>
              <tr><td>E.</td><td><?= $s['pilihan_e'] ?></td></tr>
            <?php endif; ?>
          </table>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Belum ada butir soal pada paket ini.</p>
  <?php endif; ?>

  <!-- Halaman Kunci Jawaban & Pembahasan -->
  <div class="page-break"></div>

  <div class="section-title">LEMBAR KUNCI JAWABAN & PEMBAHASAN</div>
  <p><strong>Paket Soal:</strong> <?= isset($bank_soal['judul']) ? $bank_soal['judul'] : '' ?> (<?= isset($bank_soal['kode_soal']) ? $bank_soal['kode_soal'] : '' ?>)</p>

  <table class="kunci-table">
    <thead>
      <tr>
        <th width="8%">No</th>
        <th width="15%">Jenis Soal</th>
        <th width="15%">Kunci Jawaban</th>
        <th width="12%">Bobot</th>
        <th>Pembahasan / Penjelasan Ringkas</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($soal_list as $index => $s): ?>
        <tr>
          <td style="text-align: center;"><?= $index + 1 ?></td>
          <td><?= $s['jenis'] ?></td>
          <td style="font-weight: bold; text-align: center;"><?= strtoupper($s['kunci_jawaban']) ?></td>
          <td style="text-align: center;"><?= $s['bobot'] ?></td>
          <td><?= nl2br($s['pembahasan']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>
</html>
