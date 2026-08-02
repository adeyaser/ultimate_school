<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0 fw-bold">Tambah Data Siswa Baru</h3></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="<?= base_url('murid') ?>">Data Murid</a></li><li class="breadcrumb-item active">Tambah</li></ol></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white p-3">
          <h4 class="fw-bold mb-0"><i class="bi bi-person-plus-fill me-2"></i> Formulir Master Siswa & Orang Tua</h4>
        </div>
        <div class="card-body p-4">
          <form action="<?= base_url('murid/simpan') ?>" method="post">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge me-2"></i> Akun & Biodata Utama</h5>
            <div class="row g-3 mb-4">
              <div class="col-md-4"><label class="form-label fw-semibold">NISN *</label><input type="text" name="nisn" class="form-control" placeholder="0081239011" required /></div>
              <div class="col-md-4"><label class="form-label fw-semibold">NIS Sekolah *</label><input type="text" name="nis" class="form-control" placeholder="2026001" required /></div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Kelas *</label>
                <select name="kelas_id" class="form-select" required>
                  <?php foreach ($kelas_list as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= $k['nama_kelas'] ?> (<?= $k['tingkat'] ?>)</option>
                  <?php endforeach; ?>
                </select>
              </div>
              <input type="hidden" name="tahun_ajaran_id" value="<?= isset($ta_active['id']) ? $ta_active['id'] : 1 ?>" />
              
              <div class="col-md-6"><label class="form-label fw-semibold">Nama Lengkap *</label><input type="text" name="full_name" class="form-control" required /></div>
              <div class="col-md-3"><label class="form-label fw-semibold">Username Login *</label><input type="text" name="username" class="form-control" placeholder="siswa01" required /></div>
              <div class="col-md-3"><label class="form-label fw-semibold">Password Login</label><input type="password" name="password" class="form-control" placeholder="Standard: 12345678" /></div>
              
              <div class="col-md-4"><label class="form-label fw-semibold">Tempat Lahir *</label><input type="text" name="tempat_lahir" class="form-control" required /></div>
              <div class="col-md-4"><label class="form-label fw-semibold">Tanggal Lahir *</label><input type="date" name="tanggal_lahir" class="form-control" required /></div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Jenis Kelamin *</label>
                <select name="gender" class="form-select" required>
                  <option value="L">Laki-laki</option>
                  <option value="P">Perempuan</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Agama</label>
                <select name="agama" class="form-select">
                  <option value="Islam">Islam</option>
                  <option value="Kristen">Kristen</option>
                  <option value="Katolik">Katolik</option>
                  <option value="Hindu">Hindu</option>
                  <option value="Buddha">Buddha</option>
                  <option value="Konghucu">Konghucu</option>
                </select>
              </div>
              <div class="col-md-4"><label class="form-label fw-semibold">Nomor HP/WA Siswa</label><input type="text" name="phone" class="form-control" /></div>
              <div class="col-md-4"><label class="form-label fw-semibold">Email Siswa</label><input type="email" name="email" class="form-control" /></div>
              <div class="col-md-12"><label class="form-label fw-semibold">Alamat Rumah Lengkap</label><textarea name="alamat_tinggal" class="form-control" rows="2"></textarea></div>
            </div>

            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-people me-2"></i> Data Orang Tua (Ayah & Ibu)</h5>
            <div class="row g-3 mb-4">
              <div class="col-md-4"><label class="form-label fw-semibold">Nama Ayah Kandung</label><input type="text" name="ayah_nama" class="form-control" /></div>
              <div class="col-md-4"><label class="form-label fw-semibold">Pekerjaan Ayah</label><input type="text" name="ayah_pekerjaan" class="form-control" /></div>
              <div class="col-md-4"><label class="form-label fw-semibold">No HP Ayah</label><input type="text" name="ayah_telepon" class="form-control" /></div>
              
              <div class="col-md-4"><label class="form-label fw-semibold">Nama Ibu Kandung</label><input type="text" name="ibu_nama" class="form-control" /></div>
              <div class="col-md-4"><label class="form-label fw-semibold">Pekerjaan Ibu</label><input type="text" name="ibu_pekerjaan" class="form-control" /></div>
              <div class="col-md-4"><label class="form-label fw-semibold">No HP Ibu</label><input type="text" name="ibu_telepon" class="form-control" /></div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="<?= base_url('murid') ?>" class="btn btn-secondary">Batal</a>
              <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Data Siswa</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>
