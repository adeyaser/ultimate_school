<?php
  $active_tab = ($this->input->get('tab') === 'matrix' || $this->input->get('role_id')) ? 'matrix' : ($this->input->get('tab') ? $this->input->get('tab') : 'role-tree');
?>
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0 fw-bold"><i class="bi bi-shield-lock-fill text-primary me-2"></i> Konfigurasi ACL & Tree Role Hierarchy</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">ACL Management</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= $this->session->flashdata('success') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- ACL Tabs Navigation -->
      <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-0 border-bottom">
          <ul class="nav nav-tabs card-header-tabs m-0 border-0" id="aclTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link <?= ($active_tab === 'role-tree') ? 'active fw-bold border-bottom border-3 border-primary' : '' ?> px-4 py-3 border-0" id="role-tree-tab" data-bs-toggle="tab" data-bs-target="#role-tree" type="button" role="tab">
                <i class="bi bi-diagram-3-fill me-2 text-primary"></i> 1. Pohon Role (Akar Pohon Tree)
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link <?= ($active_tab === 'menu-tree') ? 'active fw-bold border-bottom border-3 border-success' : '' ?> px-4 py-3 border-0" id="menu-tree-tab" data-bs-toggle="tab" data-bs-target="#menu-tree" type="button" role="tab">
                <i class="bi bi-folder-symlink-fill me-2 text-success"></i> 2. Pohon Menu System (ACL Menu)
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link <?= ($active_tab === 'matrix') ? 'active fw-bold border-bottom border-3 border-warning' : '' ?> px-4 py-3 border-0" id="matrix-tab" data-bs-toggle="tab" data-bs-target="#matrix" type="button" role="tab">
                <i class="bi bi-grid-3x3-gap-fill me-2 text-warning"></i> 3. Matriks Hak Akses (ACL Role Matrix)
              </button>
            </li>
          </ul>
        </div>

        <div class="card-body p-4">
          <div class="tab-content" id="aclTabsContent">

            <!-- TAB 1: POHON ROLE (AKAR POHON TREE) -->
            <div class="tab-pane fade <?= ($active_tab === 'role-tree') ? 'show active' : '' ?>" id="role-tree" role="tabpanel">
              <div class="alert alert-info border-0 rounded-3 mb-4">
                <i class="bi bi-info-circle-fill me-2"></i> Konfigurasi <strong>Akar Pohon Hierarki Role (Role Tree Hierarchy)</strong> menentukan pewarisan hak akses antar role di mana child role mewarisi atau dibatasi oleh parent role di atasnya.
              </div>

              <div class="row g-4">
                <div class="col-md-7">
                  <div class="card border rounded-4 p-3 bg-light shadow-sm">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-tree-fill me-2"></i> Visualisasi Akar Pohon Role</h5>
                    
                    <?php
                      function render_role_tree($roles) {
                          echo '<ul class="list-group list-group-flush ps-3 border-start border-3 border-primary">';
                          foreach ($roles as $r) {
                              echo '<li class="list-group-item bg-transparent border-0 py-2">';
                              echo '<div class="d-flex align-items-center gap-2">';
                              echo '<span class="badge text-bg-primary rounded-pill"><i class="bi bi-person-badge-fill me-1"></i> ' . htmlspecialchars($r['role_code']) . '</span>';
                              echo '<strong class="text-dark">' . htmlspecialchars($r['role_name']) . '</strong>';
                              if ($r['parent_role_name']) {
                                  echo '<small class="text-muted ms-2">(Induk: ' . htmlspecialchars($r['parent_role_name']) . ')</small>';
                              }
                              echo '</div>';
                              if (!empty($r['children'])) {
                                  render_role_tree($r['children']);
                              }
                              echo '</li>';
                          }
                          echo '</ul>';
                      }
                      render_role_tree($roles_tree);
                    ?>
                  </div>
                </div>

                <div class="col-md-5">
                  <div class="card border rounded-4 p-3">
                    <h5 class="fw-bold mb-3"><i class="bi bi-list-stars me-2"></i> Tabel Daftar Role Master</h5>
                    <div class="table-responsive">
                      <table class="table table-sm table-hover align-middle">
                        <thead class="table-dark">
                          <tr>
                            <th>Role Code</th>
                            <th>Nama Role</th>
                            <th>Parent Role</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($roles_list as $rl): ?>
                            <tr>
                              <td><code><?= $rl['role_code'] ?></code></td>
                              <td class="fw-bold"><?= $rl['role_name'] ?></td>
                              <td><span class="badge text-bg-secondary"><?= $rl['parent_role_name'] ? $rl['parent_role_name'] : 'Root (Akar Utama)' ?></span></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB 2: POHON MENU SYSTEM (ACL MENU TREE) -->
            <div class="tab-pane fade <?= ($active_tab === 'menu-tree') ? 'show active' : '' ?>" id="menu-tree" role="tabpanel">
              <div class="alert alert-success border-0 rounded-3 mb-4">
                <i class="bi bi-info-circle-fill me-2"></i> Struktur menu dibangun dalam pola <strong>Tree Menu (Induk & Anak Menu)</strong> untuk menghasilkan navigasi sidebar dinamis.
              </div>

              <div class="card border rounded-4 p-3 bg-light shadow-sm">
                <h5 class="fw-bold text-success mb-3"><i class="bi bi-folder-fill me-2"></i> Visualisasi Akar Pohon Menu Navigasi</h5>
                <?php
                  function render_menu_tree_view($menus) {
                      echo '<ul class="list-group list-group-flush ps-3 border-start border-3 border-success">';
                      foreach ($menus as $m) {
                          echo '<li class="list-group-item bg-transparent border-0 py-2">';
                          echo '<div class="d-flex align-items-center gap-2">';
                          echo '<i class="bi ' . ($m['icon'] ? $m['icon'] : 'bi-folder') . ' text-success fs-5"></i>';
                          echo '<strong class="text-dark">' . htmlspecialchars($m['title']) . '</strong>';
                          echo '<small class="text-muted"><code>' . htmlspecialchars($m['url']) . '</code></small>';
                          if ($m['section_header']) {
                              echo '<span class="badge text-bg-warning text-dark ms-2">' . htmlspecialchars($m['section_header']) . '</span>';
                          }
                          echo '</div>';
                          if (!empty($m['children'])) {
                              render_menu_tree_view($m['children']);
                          }
                          echo '</li>';
                      }
                      echo '</ul>';
                  }
                  render_menu_tree_view($menus_tree);
                ?>
              </div>
            </div>

            <!-- TAB 3: MATRIKS HAK AKSES ROLE (ACL MATRIX) -->
            <div class="tab-pane fade <?= ($active_tab === 'matrix') ? 'show active' : '' ?>" id="matrix" role="tabpanel">
              <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                  <h5 class="fw-bold text-warning mb-1"><i class="bi bi-grid-3x3-gap-fill me-2"></i> Matriks Otorisasi Hak Akses Per Role</h5>
                  <small class="text-muted">Konfigurasi izin standar menu untuk masing-masing role pengguna.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="toggleAllCheckboxes(true)"><i class="bi bi-check-all me-1"></i> Pilih Semua</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="toggleAllCheckboxes(false)"><i class="bi bi-x-circle me-1"></i> Hapus Semua</button>
                  <form action="<?= base_url('acl') ?>" method="get" class="d-flex align-items-center gap-2 m-0">
                    <input type="hidden" name="tab" value="matrix" />
                    <select name="role_id" class="form-select form-select-sm fw-bold border-warning text-dark" onchange="this.form.submit()">
                      <?php foreach ($roles_list as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($r['id'] == $selected_role_id) ? 'selected' : '' ?>><?= $r['role_name'] ?> (<?= $r['role_code'] ?>)</option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                </div>
              </div>

              <form action="<?= base_url('acl/simpan_matrix') ?>" method="post">
                <input type="hidden" name="role_id" value="<?= $selected_role_id ?>" />
                
                <div class="table-responsive border rounded-4 overflow-hidden mb-3">
                  <table class="table table-hover align-middle m-0">
                    <thead class="table-dark">
                      <tr>
                        <th style="width: 40px;">No</th>
                        <th>Fitur / Menu System</th>
                        <th class="text-center" style="width: 100px;">View</th>
                        <th class="text-center" style="width: 100px;">Create</th>
                        <th class="text-center" style="width: 100px;">Edit</th>
                        <th class="text-center" style="width: 100px;">Delete</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $no = 1; foreach ($menus_list as $m): 
                        $perm = isset($role_matrix[$m['id']]) ? $role_matrix[$m['id']] : array('can_view' => 1, 'can_create' => 1, 'can_edit' => 1, 'can_delete' => 1);
                      ?>
                        <tr>
                          <td><?= $no++ ?></td>
                          <td>
                            <?php if ($m['parent_id'] > 0): ?>
                              <span class="ms-3"><i class="bi bi-arrow-return-right me-1 text-muted"></i> <?= $m['title'] ?></span>
                            <?php else: ?>
                              <strong class="text-primary"><i class="bi <?= $m['icon'] ?> me-1"></i> <?= $m['title'] ?></strong>
                            <?php endif; ?>
                          </td>
                          <td class="text-center"><input type="checkbox" name="perms[<?= $m['id'] ?>][can_view]" value="1" <?= ($perm['can_view'] == 1) ? 'checked' : '' ?> class="form-check-input" /></td>
                          <td class="text-center"><input type="checkbox" name="perms[<?= $m['id'] ?>][can_create]" value="1" <?= ($perm['can_create'] == 1) ? 'checked' : '' ?> class="form-check-input" /></td>
                          <td class="text-center"><input type="checkbox" name="perms[<?= $m['id'] ?>][can_edit]" value="1" <?= ($perm['can_edit'] == 1) ? 'checked' : '' ?> class="form-check-input" /></td>
                          <td class="text-center"><input type="checkbox" name="perms[<?= $m['id'] ?>][can_delete]" value="1" <?= ($perm['can_delete'] == 1) ? 'checked' : '' ?> class="form-check-input" /></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <div class="text-end">
                  <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-save-fill me-1"></i> Simpan Matriks Hak Akses</button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<script>
function toggleAllCheckboxes(state) {
  document.querySelectorAll('#matrix input[type="checkbox"]').forEach(cb => cb.checked = state);
}
</script>
