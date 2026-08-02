<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Administrasi Data Murid</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">Data Murid</li></ol></div>
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
          <h3 class="card-title fw-bold"><i class="bi bi-people-fill me-2 text-primary"></i> Data Seluruh Siswa Aktif</h3>
          <a href="<?= base_url('murid/tambah') ?>" class="btn btn-primary fw-bold"><i class="bi bi-plus-lg me-1"></i> Tambah Siswa Baru</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
              <thead class="table-light">
                <tr>
                  <th>NISN / NIS</th>
                  <th>Nama Lengkap</th>
                  <th>JK</th>
                  <th>Kelas</th>
                  <th>No. Telepon</th>
                  <th>Status</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($murid_list)): ?>
                  <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data siswa terdaftar.</td></tr>
                <?php else: ?>
                  <?php foreach ($murid_list as $m): ?>
                    <tr>
                      <td><span class="badge text-bg-dark"><?= $m['nisn'] ?></span> / <small><?= $m['nis'] ?></small></td>
                      <td class="fw-bold d-flex align-items-center gap-2">
                        <?php 
                          $m_photo = (!empty($m['photo']) && file_exists(FCPATH . $m['photo'])) ? base_url($m['photo']) : base_url('dist/assets/img/avatar.png');
                        ?>
                        <img src="<?= $m_photo ?>" width="34" height="34" class="rounded-circle shadow-sm border" style="object-fit: cover;" alt="Foto" />
                        <span><?= $m['full_name'] ?></span>
                      </td>
                      <td><?= $m['gender'] ?></td>
                      <td><span class="badge text-bg-info"><?= $m['nama_kelas'] ?></span></td>
                      <td><?= $m['phone'] ?></td>
                      <td><span class="badge text-bg-success"><?= $m['status_murid'] ?></span></td>
                      <td class="text-end">
                        <a href="<?= base_url('murid/detail/' . $m['id']) ?>" class="btn btn-sm btn-outline-primary fw-bold"><i class="bi bi-eye-fill me-1"></i> Detail & Dokumen</a>
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
