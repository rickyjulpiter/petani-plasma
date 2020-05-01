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
            <h1>Data KUD</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Master / KUD</li>
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
  db.collection("kud").orderBy("kode")
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
        await db.collection("kud").doc(args.previousItem.keys)
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
              && (!filter.nama_koperasi || client.nama_koperasi.indexOf(filter.nama_koperasi) > -1)
              && (!filter.hubungan_mitra || client.hubungan_mitra.indexOf(filter.hubungan_mitra) > -1)
              && (!filter.hubungan_komunikasi || client.hubungan_komunikasi.indexOf(filter.hubungan_komunikasi) > -1)
              && (!filter.nama_ketua || client.nama_ketua.indexOf(filter.nama_ketua) > -1)
              && (!filter.no_kontak_ketua || client.no_kontak_ketua.indexOf(filter.no_kontak_ketua) > -1)
              && (!filter.nama_sekretaris || client.nama_sekretaris.indexOf(filter.nama_sekretaris) > -1)
              && (!filter.no_kontak_sekretaris || client.no_kontak_sekretaris.indexOf(filter.no_kontak_sekretaris) > -1)
              && (!filter.nama_bendahara || client.nama_bendahara.indexOf(filter.nama_bendahara) > -1)
              && (!filter.no_kontak_bendahara || client.no_kontak_bendahara.indexOf(filter.no_kontak_bendahara) > -1)
              && (!filter.keterangan || client.keterangan.indexOf(filter.keterangan) > -1)
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
        { name: "nama_koperasi", title: "Nama Koperasi", type: "text", width: 150, validate: "required" },
        { name: "hubungan_mitra", title: "Hubungan Mitra", type: "text", width: 150, validate: "required" },
        { name: "hubungan_komunikasi", title: "Hubungan Komunikasi", type: "text", width: 120, validate: "required" },
        { name: "nama_ketua", title: "Nama Ketua", type: "text", width: 120, validate: "required" },
        { name: "no_kontak_ketua", title: "No Kontak Ketua", type: "text", width: 120, validate: "required" },
        { name: "nama_sekretaris", title: "Nama Sekretaris", type: "text", width: 120, validate: "required" },
        { name: "no_kontak_sekretaris", title: "No Kontak Sekretaris", type: "text", width: 120, validate: "required" },
        { name: "nama_bendahara", title: "Nama Bendahara", type: "text", width: 120, validate: "required" },
        { name: "no_kontak_bendahara", title: "No Kontak Bendahara", type: "text", width: 120, validate: "required" },
        { name: "keterangan", title: "Keterangan", type: "text", width: 100 },
        { name: "kebun", title: "Kebun", type: "text", width: 100, editing: false, validate: "required" },
        { name: "master", title: "Show", type: "checkbox", width: 60 },
        { type: "control" , deleteButton: false}
      ]
    });
  }
</script>
</body>
</html>

