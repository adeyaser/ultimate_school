<?php
  $user_role = isset($user_data['role']) ? $user_data['role'] : 'super_admin';
  $is_admin = in_array($user_role, array('super_admin', 'admin'));
?>
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0 fw-bold"><i class="bi bi-people-fill text-primary me-2"></i> Manajemen Pengguna System</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Manajemen Users</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-3" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= $this->session->flashdata('success') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-3" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $this->session->flashdata('error') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Filter Selection Card -->
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-3">
          <form action="<?= base_url('users') ?>" method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label fw-bold text-primary mb-1"><i class="bi bi-person-badge-fill me-1"></i> Filter Hak Akses (Role)</label>
              <select name="role" class="form-select fw-bold border-primary py-2" onchange="this.form.submit()">
                <option value="">-- Semua Role --</option>
                <option value="super_admin" <?= ($selected_role === 'super_admin') ? 'selected' : '' ?>>Super Admin</option>
                <option value="admin" <?= ($selected_role === 'admin') ? 'selected' : '' ?>>Admin Sekolah</option>
                <option value="kepala_sekolah" <?= ($selected_role === 'kepala_sekolah') ? 'selected' : '' ?>>Kepala Sekolah</option>
                <option value="guru" <?= ($selected_role === 'guru') ? 'selected' : '' ?>>Guru Pengampu</option>
                <option value="wali_kelas" <?= ($selected_role === 'wali_kelas') ? 'selected' : '' ?>>Wali Kelas</option>
                <option value="murid" <?= ($selected_role === 'murid') ? 'selected' : '' ?>>Siswa / Murid</option>
                <option value="orang_tua" <?= ($selected_role === 'orang_tua') ? 'selected' : '' ?>>Orang Tua / Wali</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold text-primary mb-1"><i class="bi bi-mortarboard-fill me-1"></i> Filter Mode Jenjang</label>
              <select name="jenjang" class="form-select fw-bold border-primary py-2" onchange="this.form.submit()">
                <option value="ALL" <?= ($selected_jenjang === 'ALL') ? 'selected' : '' ?>>Semua Jenjang (SD, SMP, SMA)</option>
                <option value="SD" <?= ($selected_jenjang === 'SD') ? 'selected' : '' ?>>Mode SD (Sekolah Dasar)</option>
                <option value="SMP" <?= ($selected_jenjang === 'SMP') ? 'selected' : '' ?>>Mode SMP (Menengah Pertama)</option>
                <option value="SMA" <?= ($selected_jenjang === 'SMA') ? 'selected' : '' ?>>Mode SMA (Menengah Atas)</option>
              </select>
            </div>

            <div class="col-md-4 text-end">
              <?php if ($is_admin): ?>
                <button type="button" class="btn btn-success fw-bold px-4 py-2 w-100" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                  <i class="bi bi-person-plus-fill me-1"></i> Tambah Akun Pengguna
                </button>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>

      <!-- Users Table Card -->
      <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
          <h5 class="fw-bold text-primary mb-0"><i class="bi bi-person-lines-fill me-2"></i> Daftar Seluruh Akun Pengguna</h5>
          <span class="badge text-bg-secondary px-3 py-2 fs-6">Total: <?= count($users_list) ?> Users</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive p-3">
            <table id="tableUsers" class="table table-hover table-striped align-middle w-100 m-0">
              <thead class="table-dark">
                <tr>
                  <th style="width: 40px;">No</th>
                  <th>Nama Lengkap & Username</th>
                  <th>Email & Kontak</th>
                  <th class="text-center">Role / Akses</th>
                  <th class="text-center">Jenjang Mode</th>
                  <th class="text-center">Status</th>
                  <th style="width: 140px;" class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; foreach ($users_list as $u): ?>
                  <?php
                    $role_badge = ($u['role'] === 'super_admin') ? 'text-bg-danger' : (($u['role'] === 'admin') ? 'text-bg-warning text-dark' : (($u['role'] === 'guru' || $u['role'] === 'wali_kelas') ? 'text-bg-primary' : 'text-bg-info text-white'));
                    $status_badge = ($u['status'] === 'active') ? 'text-bg-success' : 'text-bg-secondary';
                    $j_badge = ($u['jenjang'] === 'SD') ? 'text-bg-success' : (($u['jenjang'] === 'SMP') ? 'text-bg-info text-white' : (($u['jenjang'] === 'SMA') ? 'text-bg-warning text-dark' : 'text-bg-secondary'));
                  ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td>
                      <strong class="text-dark fs-6 d-block"><?= htmlspecialchars($u['full_name']) ?></strong>
                      <small class="text-muted"><i class="bi bi-person me-1"></i> Username: <code><?= htmlspecialchars($u['username']) ?></code></small>
                    </td>
                    <td>
                      <small class="d-block text-dark"><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($u['email'] ? $u['email'] : '-') ?></small>
                      <small class="text-muted"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($u['phone'] ? $u['phone'] : '-') ?></small>
                    </td>
                    <td class="text-center"><span class="badge <?= $role_badge ?> px-3 py-1 fs-6"><?= strtoupper(str_replace('_', ' ', $u['role'])) ?></span></td>
                    <td class="text-center"><span class="badge <?= $j_badge ?> px-3 py-1 fs-6"><?= $u['jenjang'] ?></span></td>
                    <td class="text-center"><span class="badge <?= $status_badge ?> px-3 py-1 fs-6"><?= strtoupper($u['status']) ?></span></td>
                    <td class="text-center">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalEditUser_<?= $u['id'] ?>" title="Edit User">
                          <i class="bi bi-pencil-square"></i>
                        </button>
                        <?php if ($is_admin && $u['id'] != $user_data['id']): ?>
                          <a href="<?= base_url('users/hapus/' . $u['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?')" title="Hapus User">
                            <i class="bi bi-trash-fill"></i>
                          </a>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>

                  <!-- Modal Edit User -->
                  <div class="modal fade" id="modalEditUser_<?= $u['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <form action="<?= base_url('users/simpan') ?>" method="post">
                          <input type="hidden" name="id" value="<?= $u['id'] ?>" />
                          <div class="modal-header bg-warning text-dark p-3">
                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Akun Pengguna</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body p-4">
                            <div class="row g-3">
                              <div class="col-md-6">
                                <label class="form-label fw-bold">Username *</label>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($u['username']) ?>" required />
                              </div>
                              <div class="col-md-6">
                                <label class="form-label fw-bold">Password Baru (Kosongkan jika tidak diubah)</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" />
                              </div>
                              <div class="col-md-12">
                                <label class="form-label fw-bold">Nama Lengkap *</label>
                                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($u['full_name']) ?>" required />
                              </div>
                              <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email']) ?>" />
                              </div>
                              <div class="col-md-6">
                                <label class="form-label fw-bold">Nomor Telepon / WA</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($u['phone']) ?>" />
                              </div>
                              <div class="col-md-6">
                                <label class="form-label fw-bold">Hak Akses (Role) *</label>
                                <select name="role" class="form-select" required>
                                  <option value="super_admin" <?= ($u['role'] === 'super_admin') ? 'selected' : '' ?>>Super Admin</option>
                                  <option value="admin" <?= ($u['role'] === 'admin') ? 'selected' : '' ?>>Admin Sekolah</option>
                                  <option value="kepala_sekolah" <?= ($u['role'] === 'kepala_sekolah') ? 'selected' : '' ?>>Kepala Sekolah</option>
                                  <option value="guru" <?= ($u['role'] === 'guru') ? 'selected' : '' ?>>Guru</option>
                                  <option value="wali_kelas" <?= ($u['role'] === 'wali_kelas') ? 'selected' : '' ?>>Wali Kelas</option>
                                  <option value="murid" <?= ($u['role'] === 'murid') ? 'selected' : '' ?>>Murid</option>
                                  <option value="orang_tua" <?= ($u['role'] === 'orang_tua') ? 'selected' : '' ?>>Orang Tua</option>
                                </select>
                              </div>
                              <div class="col-md-6">
                                <label class="form-label fw-bold">Mode Jenjang Access *</label>
                                <select name="jenjang" class="form-select" required>
                                  <option value="ALL" <?= ($u['jenjang'] === 'ALL') ? 'selected' : '' ?>>ALL (Semua Mode)</option>
                                  <option value="SD" <?= ($u['jenjang'] === 'SD') ? 'selected' : '' ?>>SD (Sekolah Dasar)</option>
                                  <option value="SMP" <?= ($u['jenjang'] === 'SMP') ? 'selected' : '' ?>>SMP (Menengah Pertama)</option>
                                  <option value="SMA" <?= ($u['jenjang'] === 'SMA') ? 'selected' : '' ?>>SMA (Menengah Atas)</option>
                                </select>
                              </div>
                              <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Kelamin</label>
                                <select name="gender" class="form-select">
                                  <option value="L" <?= ($u['gender'] === 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                  <option value="P" <?= ($u['gender'] === 'P') ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                              </div>
                              <div class="col-md-6">
                                <label class="form-label fw-bold">Status Akun</label>
                                <select name="status" class="form-select">
                                  <option value="active" <?= ($u['status'] === 'active') ? 'selected' : '' ?>>Active (Aktif)</option>
                                  <option value="inactive" <?= ($u['status'] === 'inactive') ? 'selected' : '' ?>>Inactive (Non-Aktif)</option>
                                  <option value="suspended" <?= ($u['status'] === 'suspended') ? 'selected' : '' ?>>Suspended</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer bg-light p-3">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning fw-bold px-4"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<!-- Modal Tambah User -->
<?php if ($is_admin): ?>
<div class="modal fade" id="modalTambahUser" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <form action="<?= base_url('users/simpan') ?>" method="post">
        <div class="modal-header bg-success text-white p-3">
          <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i> Tambah Akun Pengguna Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Username *</label>
              <input type="text" name="username" class="form-control" placeholder="username_login" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Password *</label>
              <input type="password" name="password" class="form-control" placeholder="••••••••" required />
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Nama Lengkap *</label>
              <input type="text" name="full_name" class="form-control" placeholder="Nama Lengkap Beserta Gelar" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Email</label>
              <input type="email" name="email" class="form-control" placeholder="email@sekolah.sch.id" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Nomor Telepon / WA</label>
              <input type="text" name="phone" class="form-control" placeholder="0812xxxxxxx" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Hak Akses (Role) *</label>
              <select name="role" class="form-select" required>
                <option value="admin">Admin Sekolah</option>
                <option value="guru">Guru Pengampu</option>
                <option value="wali_kelas">Wali Kelas</option>
                <option value="kepala_sekolah">Kepala Sekolah</option>
                <option value="murid">Murid</option>
                <option value="orang_tua">Orang Tua</option>
                <option value="super_admin">Super Admin</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Mode Jenjang Access *</label>
              <select name="jenjang" class="form-select" required>
                <option value="ALL">ALL (Semua Mode)</option>
                <option value="SD">SD (Sekolah Dasar)</option>
                <option value="SMP" selected>SMP (Menengah Pertama)</option>
                <option value="SMA">SMA (Menengah Atas)</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Jenis Kelamin</label>
              <select name="gender" class="form-select">
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Status Akun</label>
              <select name="status" class="form-select">
                <option value="active">Active (Aktif)</option>
                <option value="inactive">Inactive (Non-Aktif)</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success fw-bold px-4"><i class="bi bi-check-lg me-1"></i> Buat Akun User</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    if ($('#tableUsers').length) {
      $('#tableUsers').DataTable({
        language: {
          search: "Cari User:",
          lengthMenu: "Tampilkan _MENU_ data per halaman",
          info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ user",
          infoEmpty: "Tidak ada data user",
          infoFiltered: "(disaring dari _MAX_ total user)",
          zeroRecords: "Data user tidak ditemukan",
          paginate: {
            first: "Awal",
            last: "Akhir",
            next: "Selanjutnya",
            previous: "Sebelumnya"
          }
        },
        columnDefs: [
          { targets: [0, 6], orderable: false }
        ]
      });
    }
  }
});
</script>
