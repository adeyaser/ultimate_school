<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Pembayaran SPP & TU Sekolah</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Pembayaran SPP</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle-fill me-2"></i><?= $this->session->flashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <div class="card shadow-sm mb-4">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
          <h3 class="card-title fw-bold"><i class="bi bi-cash-stack me-2 text-danger"></i> Tagihan & Transaksi Keuangan Siswa</h3>
          <?php if (in_array($this->role, array('super_admin', 'admin'))): ?>
            <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalBuatTagihan"><i class="bi bi-plus-lg me-1"></i> Buat Tagihan SPP Baru</button>
          <?php endif; ?>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
              <thead class="table-light">
                <tr>
                  <th>Nama Siswa</th>
                  <th>Jenis Tagihan</th>
                  <th>Bulan</th>
                  <th>Nominal Tagihan</th>
                  <th>Terbayar</th>
                  <th>Sisa</th>
                  <th>Status</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($tagihan_list)): ?>
                  <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data tagihan pembayaran.</td></tr>
                <?php else: ?>
                  <?php foreach ($tagihan_list as $t): ?>
                    <tr>
                      <td class="fw-bold"><?= $t['nama_murid'] ?> <br><small class="text-muted"><?= $t['nama_kelas'] ?></small></td>
                      <td><span class="badge text-bg-primary"><?= $t['jenis'] ?></span></td>
                      <td><?= $t['bulan'] ? $t['bulan'] : '-' ?></td>
                      <td>Rp <?= number_format($t['nominal'], 0, ',', '.') ?></td>
                      <td class="text-success fw-bold">Rp <?= number_format($t['terbayar'], 0, ',', '.') ?></td>
                      <td class="text-danger fw-bold">Rp <?= number_format($t['sisa'], 0, ',', '.') ?></td>
                      <td>
                        <?php if ($t['status'] === 'Lunas'): ?>
                          <span class="badge text-bg-success">LUNAS</span>
                        <?php else: ?>
                          <span class="badge text-bg-danger">BELUM LUNAS</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <a href="<?= base_url('pembayaran/kuitansi/' . $t['id']) ?>" target="_blank" class="btn btn-sm btn-outline-dark fw-bold me-1"><i class="bi bi-printer-fill me-1"></i> Kuitansi</a>
                        <?php if ($t['status'] !== 'Lunas' && in_array($this->role, array('super_admin', 'admin'))): ?>
                          <button class="btn btn-sm btn-success fw-bold" onclick="openBayarModal(<?= $t['id'] ?>, '<?= $t['nama_murid'] ?>', <?= $t['sisa'] ?>)"><i class="bi bi-cash me-1"></i> Bayar</button>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Modal Buat Tagihan -->
<div class="modal fade" id="modalBuatTagihan" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('pembayaran/buat_tagihan') ?>" method="post">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i> Buat Tagihan SPP / Keuangan</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label fw-semibold">Pilih Siswa *</label>
              <select name="murid_id" class="form-select" required>
                <?php foreach ($murid_list as $m): ?>
                  <option value="<?= $m['id'] ?>"><?= $m['full_name'] ?> (NISN: <?= $m['nisn'] ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Jenis Tagihan *</label>
              <select name="jenis" class="form-select" required>
                <option value="SPP">SPP Bulanan</option>
                <option value="Daftar Ulang">Daftar Ulang</option>
                <option value="Ujian">Ujian</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div class="col-md-6"><label class="form-label fw-semibold">Bulan (Untuk SPP)</label><input type="text" name="bulan" class="form-control" placeholder="Agustus 2026" /></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Nominal (Rp) *</label><input type="number" name="nominal" class="form-control" placeholder="350000" required /></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Jatuh Tempo</label><input type="date" name="tanggal_jatuh_tempo" class="form-control" /></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger fw-bold">Terbitkan Tagihan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Input Pembayaran -->
<div class="modal fade" id="modalBayar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('pembayaran/bayar') ?>" method="post">
        <input type="hidden" name="pembayaran_id" id="bayar_id" />
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-cash-coin me-2"></i> Input Pembayaran Kasir</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="mb-3">Siswa: <strong id="bayar_nama"></strong></p>
          <div class="mb-3">
            <label class="form-label fw-bold">Jumlah Bayar (Rp) *</label>
            <input type="number" name="jumlah_bayar" id="bayar_sisa" class="form-control form-control-lg fw-bold text-success" required />
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Metode Pembayaran</label>
            <select name="metode" class="form-select">
              <option value="Tunai">Tunai / Kasir TU</option>
              <option value="Transfer Bank">Transfer Bank</option>
              <option value="E-Wallet">E-Wallet (QRIS)</option>
              <option value="Virtual Account">Virtual Account</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan / Catatan</label>
            <input type="text" name="keterangan" class="form-control" placeholder="Pembayaran lunas via kasir" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success fw-bold">Simpan Pembayaran & Cetak</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function openBayarModal(id, nama, sisa) {
    document.getElementById('bayar_id').value = id;
    document.getElementById('bayar_nama').innerText = nama;
    document.getElementById('bayar_sisa').value = sisa;
    new bootstrap.Modal(document.getElementById('modalBayar')).show();
  }
</script>
