<!DOCTYPE html>
<html>
<?php include '../templates/header.php'; ?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php include '../templates/navbar.php'; ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include '../templates/menu.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Data Kebun</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Master / Kebun</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
          <div id="jsGrid1"></div>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include '../templates/footer.php'; ?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?php include '../templates/script.php'; ?>
<!-- page script -->
<script>
  // $(function() {
  //
  //   $("#jsGrid").jsGrid({
  //     height: "100%",
  //     width: "100%",
  //
  //     filtering: true,
  //     editing: true,
  //     sorting: true,
  //     paging: true,
  //     autoload: true,
  //
  //     pageSize: 15,
  //     pageButtonCount: 5,
  //
  //     deleteConfirm: "Do you really want to delete the client?",
  //
  //     controller: db,
  //
  //     fields: [
  //       { name: "Kode", type: "text", width: 150 },
  //       { name: "Nama", type: "number", width: 50 },
  //       { name: "Manager", type: "text", width: 200 },
  //       { name: "Group", type: "select", items: db.countries, valueField: "Id", textField: "Name" },
  //       { name: "Married", type: "checkbox", title: "Is Married", sorting: false },
  //       { type: "control" }
  //     ]
  //   });
  // });

  var db = firebase.firestore();
  var data = [];
  db.collection("kebun")
    .onSnapshot((querySnapshot) => {
      data = [];
      querySnapshot.forEach((doc) => {
        temp = doc.data();
        data.push(temp);
      });
      load();
    });

  function load() {
    console.log(data);
    $("#jsGrid1").jsGrid({
      height: "100%",
      width: "100%",

      filtering: true,
      editing: true,
      sorting: true,
      autoload: true,

      controller: {
        loadData: function(filter) {
          return $.grep(data, function(client) {
            return (!filter.kode || client.kode.indexOf(filter.kode) > -1)
              && (!filter.nama || client.nama.indexOf(filter.nama) > -1)
              && (!filter.manager || client.manager.indexOf(filter.manager) > -1)
              && (!filter.show || client.show.indexOf(filter.show) > -1)
          });
        },
      },

      data: data,

      fields: [
        { name: "kode", type: "text", width: 50 },
        { name: "nama", type: "text", width: 150 },
        { name: "manager", type: "text", width: 200 },
        { name: "show", type: "text", width: 200 }
      ]
    });
  }
</script>
</body>
</html>

