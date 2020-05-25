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
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Kebun</label>
                <label for="kebunSelect"></label><select class="form-control select2bs4" id="kebunSelect" style="width: 100%;" data-placeholder="Kebun">
                  <option selected="selected" value="" disabled></option>
                </select>
              </div>
              <!-- /.form-group -->
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-4">
              <div class="form-group">
                <label>KUD</label>
                <i class="fas fa-circle-notch fa-spin" id="kudSpinner" hidden></i>
                <label for="kudSelect"></label><select class="form-control select2bs4" id="kudSelect" style="width: 100%;" data-placeholder="KUD">
                  <option selected="selected" value="" disabled></option>
                </select>
              </div>
              <!-- /.form-group -->
            </div>
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
  var selectedOptionKud;
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
    selectedOptionKebun = this.value;
    $('#kudSpinner').removeAttr('hidden');
    db.collection("kud")
      .where("kebun", "==", selectedOptionKebun)
      .orderBy("kode")
      .get().then((querySnapshot) => {
      $('#kudSelect').empty();
      availableDates = [];
      if (data.length) {
        data = [];
        load();
      }
      index = [];
      if(!querySnapshot.size){
        optionList = '';
        $('#kudSpinner').attr('hidden', '');
        $('#kudSelect').append(optionList);
      }
      optionList = '';
      optionList += '<option value="" selected="selected" disabled></option>';
      querySnapshot.forEach((doc) => {
        if (!index.includes(doc.data().kode)) {
          index.push(doc.data().kode);
          optionList += '<option value="' + doc.data().kode + '">' + doc.data().nama_koperasi + '</option>';
        }
      });
      $('#kudSpinner').attr('hidden', '');
      $('#kudSelect').append(optionList);
    })
  })

  $('#kudSelect').on('change', function() {
    selectedOptionKud = this.value;
    $('#spinner').removeAttr('hidden');
    db.collection("kt")
      .where("kebun", "==", selectedOptionKebun)
      .where("kud", "==", selectedOptionKud)
      .orderBy("kode")
      .onSnapshot((querySnapshot) => {
        data = [];
        querySnapshot.forEach((doc) => {
          tempData = doc.data();
          tempId = doc.id;
          tempData['keys'] = tempId;

          if(tempData['kemitraan'] === '001'){
            tempData['kemitraan'] = 'GREEN'
          }
          else if(tempData['kemitraan'] === '002'){
            tempData['kemitraan'] = 'YELLOW'
          }
          else if(tempData['kemitraan'] === '003'){
            tempData['kemitraan'] = 'RED'
          }
          else {
            tempData['kemitraan'] = 'ERROR'
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
      });
  })

  function load() {
    $("#spinner").attr("hidden", "");
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
        var err = false;
        args.cancel = true; //cancel first cause if not cancel, the table will update first before database confirm it
        if(args.item['kemitraan'] !== 'GREEN' && args.item['kemitraan'] !== 'YELLOW' && args.item['kemitraan'] !== 'RED') {
          alert('Kemitraan diisi dengan GREEN / YELLOW / RED')
          err = true;
        }
        if(args.item['hubungan_komunikasi'] !== 'BAGUS' && args.item['hubungan_komunikasi'] !== 'SEDANG' && args.item['hubungan_komunikasi'] !== 'BURUK') {
          alert('Hubungan komunikasi diisi dengan BAGUS / SEDANG / BURUK')
          err = true;
        }
        delete args.item['keys'];
        if(!err) {
          switch (args.item['kemitraan']) {
            case "GREEN" :
              args.item['kemitraan'] = '001';
              break;
            case "YELLOW" :
              args.item['kemitraan'] = '002';
              break;
            case "RED" :
              args.item['kemitraan'] = '003';
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
          await db.collection("kt").doc(args.previousItem.keys)
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
        args.item.kud = selectedOptionKud;
        args.cancel = true; //cancel first cause if not cancel, the table will update first before database confirm it
        if(args.item['kemitraan'] !== 'GREEN' && args.item['kemitraan'] !== 'YELLOW' && args.item['kemitraan'] !== 'RED') {
          alert('Kemitraan diisi dengan GREEN / YELLOW / RED')
          err = true;
        }
        if(args.item['hubungan_komunikasi'] !== 'BAGUS' && args.item['hubungan_komunikasi'] !== 'SEDANG' && args.item['hubungan_komunikasi'] !== 'BURUK') {
          alert('Hubungan komunikasi diisi dengan BAGUS / SEDANG / BURUK')
          err = true;
        }
        delete args.item['keys'];
        await db.collection("kt")
          .where("kode", "==", args.item.kode)
          .where("kud", "==", args.item.kud)
          .where("kebun", "==", args.item.kebun)
          .get()
          .then(function (querySnapshot) {
            isEmpty = querySnapshot.empty;
          })
          .catch(function (error) {
            alert(error);
          });
        if(isEmpty && !err){
          if(args.item.kode.length === 1) {
            args.item.kode = "00" + args.item.kode;
          } else if (args.item.kode.length === 2) {
            args.item.kode = "0" + args.item.kode;
          }

          switch (args.item['kemitraan']) {
            case "GREEN" :
              args.item['kemitraan'] = '001';
              break;
            case "YELLOW" :
              args.item['kemitraan'] = '002';
              break;
            case "RED" :
              args.item['kemitraan'] = '003';
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
          await db.collection("kt")
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
              && (!filter.nama_kelompok_tani.toLowerCase() || client.nama_kelompok_tani.toLowerCase().indexOf(filter.nama_kelompok_tani.toLowerCase()) > -1)
              && (!filter.kemitraan.toLowerCase() || client.kemitraan.toLowerCase().indexOf(filter.kemitraan.toLowerCase()) > -1)
              && (!filter.hubungan_komunikasi.toLowerCase() || client.hubungan_komunikasi.toLowerCase().indexOf(filter.hubungan_komunikasi.toLowerCase()) > -1)
              && (!filter.nama_ketua.toLowerCase() || client.nama_ketua.toLowerCase().indexOf(filter.nama_ketua.toLowerCase()) > -1)
              && (filter.master === undefined || client.master === filter.master);
          });
        },
      },

      data: data,

      fields: [
        { name: "kode", title: "Kode", type: "text", width: 60, editing: false, validate: "required" },
        { name: "nama_kelompok_tani", title: "Nama Kelompok Tani", type: "text", width: 150, validate: "required" },
        { name: "kemitraan", title: "Kemitraan", type: "text", width: 130, validate: "required" },
        { name: "hubungan_komunikasi", title: "Hubungan Komunikasi", type: "text", width: 130, validate: "required" },
        { name: "nama_ketua", title: "Nama Ketua", type: "text", width: 130, validate: "required" },
        { name: "master", title: "Show", type: "checkbox", width: 60 },
        { type: "control", deleteButton: false}
      ]
    });
  }
</script>
</body>
</html>

