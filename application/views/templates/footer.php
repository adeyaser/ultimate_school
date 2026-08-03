      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">
          AdminLTE v4.1.0 Integrated
        </div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <?php
          $footer_name = !empty($school_info['nama_sekolah']) ? $school_info['nama_sekolah'] : 'Ultimate School';
        ?>
        <strong>
          Copyright &copy; <?= date('Y') ?>&nbsp;
          <a href="#" class="text-decoration-none"><?= htmlspecialchars($footer_name) ?> System</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->

    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <script src="<?= base_url('dist/js/adminlte.min.js') ?>"></script>
    <!--end::Required Plugin(AdminLTE)-->

    <!-- jQuery and DataTables BS5 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.7/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.7/js/dataTables.bootstrap5.min.js" crossorigin="anonymous"></script>

    <!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <!--begin::Third Party Plugin(ApexCharts)-->
    <script
      src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(ApexCharts)-->

    <!--begin::Theme Controller Script-->
    <script>
      (() => {
        'use strict';
        const getStoredTheme = () => localStorage.getItem('lte-theme');
        const setStoredTheme = (theme) => localStorage.setItem('lte-theme', theme);

        const getPreferredTheme = () => {
          const storedTheme = getStoredTheme();
          if (storedTheme) {
            return storedTheme;
          }
          return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };

        const setTheme = (theme) => {
          if (theme === 'auto') {
            document.documentElement.setAttribute('data-bs-theme', window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
          } else {
            document.documentElement.setAttribute('data-bs-theme', theme);
          }
        };

        setTheme(getPreferredTheme());

        const showActiveTheme = (theme, focus = false) => {
          const themeSwitcher = document.querySelector('#bd-theme');
          if (!themeSwitcher) {
            return;
          }

          const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`);
          document.querySelectorAll('[data-bs-theme-value]').forEach((element) => {
            element.classList.remove('active');
            element.setAttribute('aria-pressed', 'false');
          });

          if (btnToActive) {
            btnToActive.classList.add('active');
            btnToActive.setAttribute('aria-pressed', 'true');
          }
        };

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
          const storedTheme = getStoredTheme();
          if (storedTheme !== 'light' && storedTheme !== 'dark') {
            setTheme(getPreferredTheme());
          }
        });

        window.addEventListener('DOMContentLoaded', () => {
          showActiveTheme(getPreferredTheme());

          document.querySelectorAll('[data-bs-theme-value]').forEach((toggle) => {
            toggle.addEventListener('click', () => {
              const theme = toggle.getAttribute('data-bs-theme-value');
              setStoredTheme(theme);
              setTheme(theme);
              showActiveTheme(theme, true);
            });
          });
        });
      })();
    </script>
    <!--end::Theme Controller Script-->
  </body>
</html>
