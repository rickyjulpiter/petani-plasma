<!DOCTYPE html>
<html>
<?php include '../templates/header.php'; ?>
<style>
  .center-screen {
    margin: 0;
    position: absolute;
    top: 50%;
    left: 50%;
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
  }
</style>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="col-12" style="height: 100vh" id="spinnerContent">
    <div class="center-screen">
      <div class="spinner-grow text-dark" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
  </div>
  <div class="wrapper" id="content" hidden>

    <!-- Navbar -->
    <?php include '../templates/navbar.php'; ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include '../templates/menu.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0 text-dark">Laporan Tahunan</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-body">
              <!-- Laporan -->
              <div class="row">
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-gradient-cyan">
                    <div class="inner">
                      <h3 id="laporan"><i class="fas fa-circle-notch fa-spin" id="spinner"></i></h3>

                      <p>Laporan <b>Lapangan-Ku</b> <br>Hari Ini</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-document"></i>
                    </div>
                    <a href="../laporanLapangan" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-gradient-success">
                    <div class="inner">
                      <h3 id="laporanPembinaan"><i class="fas fa-circle-notch fa-spin" id="spinner"></i></h3>

                      <p>Laporan <b>Pembinaan-Ku</b> <br> Hari ini</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-document"></i>
                    </div>
                    <a href="../laporanPembinaan" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-gradient-warning">
                    <div class="inner">
                      <h3 id="laporanProduksi"><i class="fas fa-circle-notch fa-spin" id="spinner"></i></h3>

                      <p>Laporan <b>Produksi-Ku</b> <br> Hari ini</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-document"></i>
                    </div>
                    <a href="../produksiKu" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-gradient-danger">
                    <div class="inner">
                      <h3 id="laporanKerja"><i class="fas fa-circle-notch fa-spin" id="spinner"></i></h3>

                      <p>Laporan <b>Kerja-Ku</b> <br> Hari ini</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-document"></i>
                    </div>
                    <a href="../laporanHasilKerja" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <!-- ./col -->
              </div>
            </div>
          </div>
          <!-- /.row -->
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
      <!-- /.content-header -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Footer -->
    <?php include '../templates/footer.php'; ?>
    <!-- /.footer -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <?php include '../templates/script.php'; ?>

  <!-- Authentication -->
  <script type="text/javascript">
    window.onload = function() {
      initApp();
    };

    var thisYear = new Date();
    thisYear.setDate(1);
    thisYear.setMonth(0);
    thisYear.setHours(0, 0, 0, 0);
    var db = firebase.firestore();
    db.collection('report').where("updated_at_hasil_kerja", ">=", thisYear)
      .get().then((hasilKerjaData) => $("#laporan").html(hasilKerjaData.size));
    db.collection('report').where("updated_at_pembinaan_petani", ">=", thisYear)
      .get().then((pembinaanData) => $("#laporanPembinaan").html(pembinaanData.size));
    db.collection('produksiku').where("updated", ">=", thisYear)
      .get().then((produksiData) => $("#laporanProduksi").html(produksiData.size));
    db.collection('report').where("updated_at", ">=", thisYear)
      .get().then((produksiData) => $("#laporanKerja").html(produksiData.size));
  </script>

</body>

</html>