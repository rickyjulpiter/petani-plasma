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
        await db.collection("kapling").doc(args.previousItem.keys)
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
              && (!filter.nama_petani || client.nama_petani.indexOf(filter.nama_petani) > -1)
              && (!filter.no_kontak || client.no_kontak.indexOf(filter.no_kontak) > -1)
              && (!filter.kt || client.kt.indexOf(filter.kt) > -1)
              && (!filter.kud || client.kud.indexOf(filter.kud) > -1)
              && (!filter.kebun || client.kebun.indexOf(filter.kebun) > -1)
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
        { name: "nama_petani", title: "Nama Petani", type: "text", width: 150, validate: "required" },
        { name: "no_kontak", title: "No Kontak Petani", type: "text", width: 150 },
        { name: "kt", title: "KT", type: "text", width: 60, editing: false },
        { name: "kud", title: "KUD", type: "text", width: 60,  editing: false },
        { name: "kebun", title: "Kebun", type: "text", width: 60,  editing: false },
        { name: "master", title: "Show", type: "checkbox", width: 60 },
        { type: "control" , deleteButton: false}
      ]
    });
  }
</script>
</body>
</html>

