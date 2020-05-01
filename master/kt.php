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
            <h1>Data KT</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Master / KT</li>
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
  var db = firebase.firestore();
  var data = [];
  var id = [];
  db.collection("kt").orderBy("kode")
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
    $("#jsGrid1").jsGrid({
      height: "100%",
      width: "100%",

      filtering: true,
      editing: true,
      sorting: true,
      autoload: true,
      //inserting: true,

      onItemUpdating: async function(args) {
        args.cancel = true; //cancel first cause if not cancel, the table will update first before database confirm it
        await db.collection("kt").doc(args.previousItem.keys)
          .update(args.item)
          .then(function () {
            console.log('Data Update ' + args.item.kode + ' Success');
          }).catch(function (error) {
            console.log("Error updating document: ", error);
            alert('Data bermasalah');
          });

      },

      controller: {
        loadData: function(filter) {
          return $.grep(data, function(client) {
            return (!filter.kode || client.kode.indexOf(filter.kode) > -1)
              && (!filter.nama_kelompok_tani || client.nama_kelompok_tani.indexOf(filter.nama_kelompok_tani) > -1)
              && (!filter.kud || client.kud.indexOf(filter.kud) > -1)
              && (!filter.kebun || client.kebun.indexOf(filter.kebun) > -1)
              && (!filter.kemitraan || client.kemitraan.indexOf(filter.kemitraan) > -1)
              && (!filter.hubungan_komunikasi || client.hubungan_komunikasi.indexOf(filter.hubungan_komunikasi) > -1)
              && (!filter.nama_ketua || client.nama_ketua.indexOf(filter.nama_ketua) > -1)
              && (filter.master === undefined || client.master === filter.master);
          });
        },

        insertItem: function(insertingClient) {
          data.push(insertingClient);
          console.log(data);
        },

        updateItem: function(updatingClient) {
          console.log('Updated');
        },
      },

      data: data,

      fields: [
        { name: "kode", title: "Kode", type: "text", width: 60, editing: false },
        { name: "nama_kelompok_tani", title: "Nama Kelompok Tani", type: "text", width: 150, validate: "required" },
        { name: "kud", title: "KUD", type: "text", width: 60, editing: false },
        { name: "kebun", title: "Kebun", type: "text", width: 60, editing: false },
        { name: "kemitraan", title: "Kemitraan", type: "text", width: 170, validate: "required" },
        { name: "hubungan_komunikasi", title: "Hubungan Komunikasi", type: "text", width: 130, validate: "required" },
        { name: "nama_ketua", title: "Nama Ketua", type: "text", width: 130, validate: "required" },
        { name: "master", title: "Show", type: "checkbox", width: 60 },
        { type: "control" , deleteButton: false}
      ]
    });
  }
</script>
</body>
</html>

