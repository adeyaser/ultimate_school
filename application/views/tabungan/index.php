<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Kelola Tabungan Murid Per Kelas</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Tabungan</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle-fill me-2"></i><?= $this->session->flashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
          <form action="<?= base_url('tabungan') ?>" method="get" class="row g-3 align-items-end">
            <div class="col-md-8">
              <label class="form-label fw-bold">Pilih Kelas Target:</label>
              <select name="kelas_id" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas_list as $k): ?>
                  <option value="<?= $k['id'] ?>" <?= ($selected_kelas == $k['id']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <button type="submit" class="btn btn-success fw-bold w-100"><i class="bi bi-wallet2 me-1"></i> Tampilkan Tabungan Kelas</button>
            </div>
          </form>
        </div>
      </div>

      <?php if ($selected_kelas): ?>
        <div class="card shadow-sm mb-4">
          <div class="card-header border-0 bg-white">
            <h4 class="card-title fw-bold text-success mb-0"><i class="bi bi-piggy-bank-fill me-2"></i> Rekap Tabungan Siswa (Wali Kelas -> Administrasi TU)</h4>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                  <tr>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Saldo Akhir</th>
                    <th class="text-end">Aksi Transaksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($tabungan_list)): ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data siswa di kelas ini.</td></tr>
                  <?php else: ?>
                    <?php foreach ($tabungan_list as $t): ?>
                      <tr>
                        <td><span class="badge text-bg-dark"><?= $t['nisn'] ?></span></td>
                        <td class="fw-bold"><?= $t['full_name'] ?></td>
                        <td><strong class="text-success fs-5">Rp <?= number_format($t['saldo_akhir'] ? $t['saldo_akhir'] : 0, 0, ',', '.') ?></strong></td>
                        <td class="text-end">
                          <a href="<?= base_url('tabungan/transaksi/' . $t['murid_id']) ?>" class="btn btn-sm btn-outline-success fw-bold"><i class="bi bi-cash-coin me-1"></i> Setor / Tarik Tabungan</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>
