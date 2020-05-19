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
<body class="hold-transition sidebar-mini">
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
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Data Kapling</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Master / Kapling</li>
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
          <div id="spinner" style="text-align:center;">
            <span>Data Loading </span><i class="fas fa-circle-notch fa-spin"></i>
          </div>
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

<!-- page script -->
<?php include '../templates/script.php'; ?>
<script type="text/javascript">
  window.onload = function() {
    initApp();
  };
</script>
<script>
  var db = firebase.firestore();
  var data = [];
  var id = [];
  db.collection("kapling").orderBy("kode")
    .onSnapshot((querySnapshot) => {
      data = [];
      querySnapshot.forEach((doc) => {
        tempData = doc.data();
        tempId = doc.id;
        tempData['keys'] = tempId;
        data.push(tempData);
      });
      load();
    });

  function load() {
    $("#spinner").remove();
    $("#jsGrid1").jsGrid({
      height: "100%",
      width: "100%",

      filtering: true,
      editing: true,
      sorting: true,
      autoload: true,
      inserting: true,
      paging: true,

      onItemUpdating: async function(args) {
        args.cancel = true; //cancel first cause if not cancel, the table will update first before database confirm it
        delete args.item['keys'];
        await db.collection("kapling").doc(args.previousItem.keys)
          .update(args.item)
          .then(function () {
          }).catch(function (error) {
            alert('Data bermasalah');
          });
      },

      onItemInserting: async function(args) {
        args.cancel = true; //cancel first cause if not cancel, the table will update first before database confirm it
        delete args.item['keys'];
        await db.collection("kapling")
          .where("kode", "==", args.item.kode)
          .where("kt", "==", args.item.kt)
          .where("kud", "==", args.item.kud)
          .where("kebun", "==", args.item.kebun)
          .get()
          .then(function (querySnapshot) {
            isEmpty = querySnapshot.empty;
          })
          .catch(function (error) {
          });
        if(isEmpty){
          await db.collection("kapling")
            .add(args.item)
            .then(function () {
            })
            .catch(function (error) {
            });
        } else {
          alert("Kode sudah terdaftar");
        }
      },

      controller: {
        loadData: function(filter) {
          return $.grep(data, function(client) {
            return (!filter.kode.toLowerCase() || client.kode.toLowerCase().indexOf(filter.kode.toLowerCase()) > -1)
              && (!filter.nama_petani.toLowerCase() || client.nama_petani.toLowerCase().indexOf(filter.nama_petani.toLowerCase()) > -1)
              && (!filter.no_kontak.toLowerCase() || client.no_kontak.toLowerCase().indexOf(filter.no_kontak.toLowerCase()) > -1)
              && (!filter.kt.toLowerCase() || client.kt.toLowerCase().indexOf(filter.kt.toLowerCase()) > -1)
              && (!filter.kud.toLowerCase() || client.kud.toLowerCase().indexOf(filter.kud.toLowerCase()) > -1)
              && (!filter.kebun.toLowerCase() || client.kebun.toLowerCase().indexOf(filter.kebun.toLowerCase()) > -1)
              && (filter.master === undefined || client.master === filter.master);
          });
        },
      },

      data: data,

      fields: [
        { name: "kode", title: "Kode", type: "text", width: 60, editing: false, validate: "required" },
        { name: "nama_petani", title: "Nama Petani", type: "text", width: 150, validate: "required" },
        { name: "no_kontak", title: "No Kontak Petani", type: "text", width: 150, validate: "required" },
        { name: "kt", title: "KT", type: "text", width: 60, editing: false, validate: "required" },
        { name: "kud", title: "KUD", type: "text", width: 60,  editing: false, validate: "required" },
        { name: "kebun", title: "Kebun", type: "text", width: 60,  editing: false, validate: "required" },
        { name: "master", title: "Show", type: "checkbox", width: 60 },
        { type: "control", deleteButton: false}
      ]
    });
  }
</script>
</body>
</html>

