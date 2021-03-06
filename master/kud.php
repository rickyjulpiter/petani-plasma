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
          <div class="col-md-4">
            <div class="form-group">
              <label>Kebun</label>
              <label for="kebunSelect"></label><select class="form-control select2bs4" id="kebunSelect" style="width: 100%;" data-placeholder="Kebun">
                <option selected="selected" value="" disabled></option>
              </select>
            </div>
            <!-- /.form-group -->
          </div>
          <div id="spinner" style="text-align:center;" hidden>
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
  //Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })

  var tempData, tempId;
  var optionList;
  var selectedOptionKebun;
  var db = firebase.firestore();
  var data = [];
  var id = [];

  db.collection("kebun")
    .orderBy("kode")
    .get().then((querySnapshot) => {
    optionList = '';
    optionList += '<option value="" selected="selected" disabled></option>';
    querySnapshot.forEach((doc) => {
      optionList += '<option value="' + doc.data().kode + '">' + doc.data().kode + '</option>';
    });
    $('#kebunSelect').append(optionList);
  })

  $('#kebunSelect').on('change', function() {
    var init = true;
    var contain;
    data = [{keys: ""}]; //onSnapshot fix
    selectedOptionKebun = this.value;
    $('#spinner').removeAttr('hidden');
    db.collection("kud")
      .where("kebun", "==", selectedOptionKebun)
      .orderBy("kode")
      .onSnapshot((querySnapshot) => {
        // onSnapshot listen to all document in a collection, so it did not filter the 'WHERE' arguments
        // if there is an update on db after the first db load. The result, table update out of the 'WHERE' range
        // The solution is add a contain var and check the if statement

        if (!querySnapshot.empty) {
          contain = data[0].keys === querySnapshot.docs[0].id;
        }
        if (contain || init) {
          data = [];
          querySnapshot.forEach((doc) => {
            tempData = doc.data();
            tempId = doc.id;
            tempData['keys'] = tempId;

            if(tempData['hubungan_mitra'] === '001'){
              tempData['hubungan_mitra'] = 'GREEN'
            }
            else if(tempData['hubungan_mitra'] === '002'){
              tempData['hubungan_mitra'] = 'YELLOW'
            }
            else if(tempData['hubungan_mitra'] === '003'){
              tempData['hubungan_mitra'] = 'RED'
            }
            else {
              tempData['hubungan_mitra'] = 'ERROR'
            }

            if(tempData['hubungan_komunikasi'] === '001'){
              tempData['hubungan_komunikasi'] = 'BAGUS'
            }
            else if(tempData['hubungan_komunikasi'] === '002'){
              tempData['hubungan_komunikasi'] = 'SEDANG'
            }
            else if(tempData['hubungan_komunikasi'] === '003'){
              tempData['hubungan_komunikasi'] = 'BURUK'
            }
            else {
              tempData['hubungan_komunikasi'] = 'ERROR'
            }

            data.push(tempData);
          });
          load();
        }
      });
  })

  function load() {
    $("#spinner").attr("hidden", "");
    $("#jsGrid1").jsGrid({
      height: 600,
      width: "100%",

      filtering: true,
      editing: true,
      sorting: true,
      autoload: true,
      inserting: true,
      paging: true,
      pageSize: 10,

      onItemUpdating: async function(args) {
        var err = false;
        args.cancel = true; //cancel first cause if not cancel, the table will update first before database confirm it
        if(args.item['hubungan_mitra'] !== 'GREEN' && args.item['hubungan_mitra'] !== 'YELLOW' && args.item['hubungan_mitra'] !== 'RED') {
          alert('Hubungan mitra diisi dengan GREEN / YELLOW / RED')
          err = true;
        }
        if(args.item['hubungan_komunikasi'] !== 'BAGUS' && args.item['hubungan_komunikasi'] !== 'SEDANG' && args.item['hubungan_komunikasi'] !== 'BURUK') {
          alert('Hubungan komunikasi diisi dengan BAGUS / SEDANG / BURUK')
          err = true;
        }
        delete args.item['keys'];
        if(!err) {
          switch (args.item['hubungan_mitra']) {
            case "GREEN" :
              args.item['hubungan_mitra'] = '001';
              break;
            case "YELLOW" :
              args.item['hubungan_mitra'] = '002';
              break;
            case "RED" :
              args.item['hubungan_mitra'] = '003';
              break;
          }
          switch (args.item['hubungan_komunikasi']) {
            case "BAGUS" :
              args.item['hubungan_komunikasi'] = '001';
              break;
            case "SEDANG" :
              args.item['hubungan_komunikasi'] = '002';
              break;
            case "BURUK" :
              args.item['hubungan_komunikasi'] = '003';
              break;
          }
          await db.collection("kud").doc(args.previousItem.keys)
            .update(args.item)
            .then(function () {
            }).catch(function (error) {
              alert('Data bermasalah');
            });
        }
      },

      onItemInserting: async function(args) {
        var err = false;
        var isEmpty;
        args.item.kebun = selectedOptionKebun;
        if(args.item.kode.length === 1) {
          args.item.kode = "00" + args.item.kode;
        } else if (args.item.kode.length === 2) {
          args.item.kode = "0" + args.item.kode;
        }
        args.cancel = true; //cancel first cause if not cancel, the table will update first before database confirm it
        if(args.item['hubungan_mitra'] !== 'GREEN' && args.item['hubungan_mitra'] !== 'YELLOW' && args.item['hubungan_mitra'] !== 'RED') {
          alert('Hubungan mitra diisi dengan GREEN / YELLOW / RED')
          err = true;
        }
        if(args.item['hubungan_komunikasi'] !== 'BAGUS' && args.item['hubungan_komunikasi'] !== 'SEDANG' && args.item['hubungan_komunikasi'] !== 'BURUK') {
          alert('Hubungan komunikasi diisi dengan BAGUS / SEDANG / BURUK')
          err = true;
        }
        delete args.item['keys'];
        await db.collection("kud")
          .where("kode", "==", args.item.kode)
          .where("kebun", "==", args.item.kebun)
          .get()
          .then(function (querySnapshot) {
            isEmpty = querySnapshot.empty;
          })
          .catch(function (error) {
            alert(error);
          });
        if(isEmpty && !err){
          switch (args.item['hubungan_mitra']) {
            case "GREEN" :
              args.item['hubungan_mitra'] = '001';
              break;
            case "YELLOW" :
              args.item['hubungan_mitra'] = '002';
              break;
            case "RED" :
              args.item['hubungan_mitra'] = '003';
              break;
          }
          switch (args.item['hubungan_komunikasi']) {
            case "BAGUS" :
              args.item['hubungan_komunikasi'] = '001';
              break;
            case "SEDANG" :
              args.item['hubungan_komunikasi'] = '002';
              break;
            case "BURUK" :
              args.item['hubungan_komunikasi'] = '003';
              break;
          }
          args.item.master = true;
          await db.collection("kud")
            .add(args.item)
            .then(function () {
            })
            .catch(function (error) {
              alert(error);
            });
        } else {
          alert("Kode sudah terdaftar");
        }
      },

      controller: {
        loadData: function(filter) {
          return $.grep(data, function(client) {
            return (!filter.kode.toLowerCase() || client.kode.toLowerCase().indexOf(filter.kode.toLowerCase()) > -1)
              && (!filter.nama_koperasi.toLowerCase() || client.nama_koperasi.toLowerCase().indexOf(filter.nama_koperasi.toLowerCase()) > -1)
              && (!filter.hubungan_mitra.toLowerCase() || client.hubungan_mitra.toLowerCase().indexOf(filter.hubungan_mitra.toLowerCase()) > -1)
              && (!filter.hubungan_komunikasi.toLowerCase() || client.hubungan_komunikasi.toLowerCase().indexOf(filter.hubungan_komunikasi.toLowerCase()) > -1)
              && (!filter.nama_ketua.toLowerCase() || client.nama_ketua.toLowerCase().indexOf(filter.nama_ketua.toLowerCase()) > -1)
              && (!filter.no_kontak_ketua.toLowerCase() || client.no_kontak_ketua.toLowerCase().indexOf(filter.no_kontak_ketua.toLowerCase()) > -1)
              && (!filter.nama_sekretaris.toLowerCase() || client.nama_sekretaris.toLowerCase().indexOf(filter.nama_sekretaris.toLowerCase()) > -1)
              && (!filter.no_kontak_sekretaris.toLowerCase() || client.no_kontak_sekretaris.toLowerCase().indexOf(filter.no_kontak_sekretaris.toLowerCase()) > -1)
              && (!filter.nama_bendahara.toLowerCase() || client.nama_bendahara.toLowerCase().indexOf(filter.nama_bendahara.toLowerCase()) > -1)
              && (!filter.no_kontak_bendahara.toLowerCase() || client.no_kontak_bendahara.toLowerCase().indexOf(filter.no_kontak_bendahara.toLowerCase()) > -1)
              && (!filter.keterangan.toLowerCase() || client.keterangan.toLowerCase().indexOf(filter.keterangan.toLowerCase()) > -1)
              && (filter.master === undefined || client.master === filter.master);
          });
        },
      },

      data: data,

      fields: [
        { name: "kode", title: "Kode", type: "text", width: 60, editing: false, validate: "required" },
        { name: "nama_koperasi", title: "Nama Koperasi", type: "text", width: 150, validate: "required" },
        { name: "hubungan_mitra", title: "Hubungan Mitra", type: "text", width: 120, validate: "required" },
        { name: "hubungan_komunikasi", title: "Hubungan Komunikasi", type: "text", width: 120, validate: "required" },
        { name: "nama_ketua", title: "Nama Ketua", type: "text", width: 120, validate: "required" },
        { name: "no_kontak_ketua", title: "No Kontak Ketua", type: "text", width: 120, validate: "required" },
        { name: "nama_sekretaris", title: "Nama Sekretaris", type: "text", width: 120, validate: "required" },
        { name: "no_kontak_sekretaris", title: "No Kontak Sekretaris", type: "text", width: 120, validate: "required" },
        { name: "nama_bendahara", title: "Nama Bendahara", type: "text", width: 120, validate: "required" },
        { name: "no_kontak_bendahara", title: "No Kontak Bendahara", type: "text", width: 120, validate: "required" },
        { name: "keterangan", title: "Keterangan", type: "text", width: 100 },
        { name: "master", title: "Show", type: "checkbox", width: 60 },
        { type: "control", deleteButton: false}
      ]
    });
  }
</script>
</body>
</html>

