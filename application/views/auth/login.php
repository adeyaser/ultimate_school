<!doctype html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Login System | Ultimate School</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <!-- AdminLTE v4 CSS -->
    <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>" />
    <!-- Cloudflare Turnstile CAPTCHA API -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  </head>
  <body class="login-page bg-body-secondary d-flex align-items-center justify-content-center min-vh-100">
    <div class="login-box" style="width: 400px;">
      <div class="login-logo text-center mb-4">
        <a href="<?= base_url() ?>" class="h2 text-decoration-none fw-bold text-primary">
          <i class="bi bi-mortarboard-fill me-2"></i><b>ULTIMATE</b> SCHOOL
        </a>
        <div class="text-muted fs-6">Sistem Informasi Manajemen Sekolah</div>
      </div>
      
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body login-card-body p-4">
          <p class="login-box-msg text-center fw-semibold text-secondary mb-4">Silakan masuk ke akun Anda</p>

          <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <?= $this->session->flashdata('error') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i>
              <?= $this->session->flashdata('success') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form action="<?= base_url('auth/login') ?>" method="post">
            <div class="input-group mb-3">
              <input type="text" name="username" class="form-control form-control-lg" placeholder="Username / Email" required autofocus />
              <div class="input-group-text"><span class="bi bi-person-fill"></span></div>
            </div>

            <div class="input-group mb-4">
              <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required />
              <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
            </div>

            <!-- Cloudflare Turnstile CAPTCHA Widget -->
            <?= render_turnstile() ?>

            <div class="row mb-3">
              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                  <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                </button>
              </div>
            </div>
          </form>

          <div class="text-center mt-3 pt-3 border-top">
            <a href="<?= base_url('home') ?>" class="text-decoration-none">
              <i class="bi bi-house-door me-1"></i> Kembali ke Company Profile
            </a>
          </div>
        </div>
      </div>
      
      <?php
        $login_school_name = !empty($school_info['nama_sekolah']) ? $school_info['nama_sekolah'] : 'Ultimate School';
      ?>
      <div class="text-center text-muted fs-7 mt-4">
        &copy; <?= date('Y') ?> <?= htmlspecialchars($login_school_name) ?> System. AdminLTE v4.1.0 Integrated.
      </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
