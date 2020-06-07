<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="../dashboard/index.php" class="brand-link">
    <span class="brand-text font-weight-light">Petani Plasma</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="info">
        <a href="#" class="d-block">Administrator</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="../dashboard" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>
              Laporan
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="../laporan" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Staff Kerja</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../laporanPembinaan" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Pembinaan </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../produksiKu" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Produksi-Ku </p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-users-cog"></i>
            <p>
              Master
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="../master/kebun.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Kebun</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../master/kud.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>KUD</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../master/kt.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>KT</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../master/kapling.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Kapling</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" onclick="logout()">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>
              Logout
            </p>
          </a>
        </li>
        <!--        <li class="nav-item">-->
        <!--          <a href="#" class="nav-link" onclick="admin()">-->
        <!--            <i class="nav-icon fas fa-sign-out-alt"></i>-->
        <!--            <p>-->
        <!--              do admin-->
        <!--            </p>-->
        <!--          </a>-->
        <!--        </li>-->
        <!--        <li class="nav-item">-->
        <!--          <a href="#" class="nav-link" onclick="copy()">-->
        <!--            <i class="nav-icon fas fa-sign-out-alt"></i>-->
        <!--            <p>-->
        <!--              Copy Data-->
        <!--            </p>-->
        <!--          </a>-->
        <!--        </li>-->
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
