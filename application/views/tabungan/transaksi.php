<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Transaksi Tabungan Siswa</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="<?= base_url('tabungan') ?>">Tabungan</a></li><li class="breadcrumb-item active">Transaksi</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle-fill me-2"></i><?= $this->session->flashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- Form Transaksi -->
        <div class="col-md-5">
          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-success text-white p-3">
              <h5 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2"></i> Setoran / Penarikan Tabungan</h5>
            </div>
            <div class="card-body p-4">
              <div class="p-3 bg-light rounded-3 mb-3 border">
                <p class="mb-1"><strong>Siswa:</strong> <?= $murid['full_name'] ?></p>
                <p class="mb-0"><strong>Saldo Saat Ini:</strong> <span class="text-success fw-bold fs-4">Rp <?= number_format($tabungan['saldo_akhir'], 0, ',', '.') ?></span></p>
              </div>

              <form action="<?= base_url('tabungan/simpan_transaksi') ?>" method="post">
                <input type="hidden" name="tabungan_id" value="<?= $tabungan['id'] ?>" />
                <input type="hidden" name="murid_id" value="<?= $murid['id'] ?>" />

                <div class="mb-3">
                  <label class="form-label fw-bold">Jenis Transaksi *</label>
                  <select name="jenis" class="form-select form-select-lg" required>
                    <option value="Setoran">Setoran Tabungan (+)</option>
                    <option value="Penarikan">Penarikan Tabungan (-)</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Nominal (Rp) *</label>
                  <input type="number" name="nominal" class="form-control form-control-lg" placeholder="50000" min="1000" required />
                </div>
                <div class="mb-4">
                  <label class="form-label fw-bold">Keterangan / Catatan</label>
                  <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Tabungan mingguan" />
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">
                  <i class="bi bi-check-lg me-1"></i> Simpan Transaksi Tabungan
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Riwayat Transaksi -->
        <div class="col-md-7">
          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white p-3 fw-bold text-primary border-bottom">
              <i class="bi bi-clock-history me-2"></i> Riwayat Transaksi Tabungan
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                  <thead class="table-light">
                    <tr>
                      <th>Tanggal</th>
                      <th>Jenis</th>
                      <th>Nominal</th>
                      <th>Saldo Sesudah</th>
                      <th>Pencatat</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($riwayat)): ?>
                      <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td></tr>
                    <?php else: ?>
                      <?php foreach ($riwayat as $r): ?>
                        <tr>
                          <td><small><?= date('d M Y', strtotime($r['tanggal'])) ?></small></td>
                          <td>
                            <?php if ($r['jenis'] === 'Setoran'): ?>
                              <span class="badge text-bg-success">+ Setoran</span>
                            <?php else: ?>
                              <span class="badge text-bg-danger">- Penarikan</span>
                            <?php endif; ?>
                          </td>
                          <td class="fw-bold">Rp <?= number_format($r['nominal'], 0, ',', '.') ?></td>
                          <td class="text-primary fw-bold">Rp <?= number_format($r['saldo_sesudah'], 0, ',', '.') ?></td>
                          <td><small><?= $r['nama_input'] ?></small></td>
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
    </div>
  </div>
</main>
