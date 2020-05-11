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
            <h1 class="m-0 text-dark">Dashboard</h1>
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
          <div class="card-header">
            <h3>Laporan</h3>
          </div>
          <div class="card-body">
            <!-- Laporan -->
            <div class="row">
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-gradient-cyan">
                  <div class="inner">
                    <h3 id="laporan"><i class="fas fa-circle-notch fa-spin" id="spinner"></i></h3>

                    <p>Laporan <b>Hasil Kerja</b> <br>Hari Ini</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-document"></i>
                  </div>
                  <a href="../laporan" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-gradient-teal">
                  <div class="inner">
                    <h3 id="laporanPembinaan"><i class="fas fa-circle-notch fa-spin" id="spinner"></i></h3>

                    <p>Laporan <b>Pembinaan</b> <br> Hari ini</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-document"></i>
                  </div>
                  <a href="../laporanPembinaan" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3>Master</h3>
          </div>
          <div class="card-body">
            <!-- Master -->
            <div class="row">
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                  <div class="inner">
                    <h3 id="kebun"><i class="fas fa-circle-notch fa-spin" id="spinner"></i></h3>

                    <p>Kebun</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-key"></i>
                  </div>
                  <a href="../master/kebun.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3 id="kud"><i class="fas fa-circle-notch fa-spin" id="spinner"></i></h3>

                    <p>KUD</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-key"></i>
                  </div>
                  <a href="../master/kud.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                  <div class="inner">
                    <h3 id="kt"><i class="fas fa-circle-notch fa-spin" id="spinner"></i></h3>

                    <p>KT</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-key"></i>
                  </div>
                  <a href="../master/kt.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                    <h3 id="kapling"><i class="fas fa-circle-notch fa-spin" id="spinner"></i></h3>

                    <p>Kapling</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-key"></i>
                  </div>
                  <a href="../master/kapling.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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

  var today = new Date();
  today.setHours(0, 0, 0, 0);
  console.log(today);
  var db = firebase.firestore();
  db.collection('kebun').get().then((kebunData) => $("#kebun").html(kebunData.size));
  db.collection('kud').get().then((kudData) => $("#kud").html(kudData.size));
  db.collection('kt').get().then((ktData) => $("#kt").html(ktData.size));
  db.collection('kapling').get().then((kaplingData) => $("#kapling").html(kaplingData.size));
  db.collection('report').where("updated_at_hasil_kerja", ">=", today)
    .get().then((hasilKerjaData) => $("#laporan").html(hasilKerjaData.size));
  db.collection('report').where("updated_at_pembinaan_petani", ">=", today)
    .get().then((pembinaanData) => $("#laporanPembinaan").html(pembinaanData.size));
</script>

</body>
</html>
