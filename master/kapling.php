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
            <div class="col-md-4">
              <div class="form-group">
                <label>KT</label>
                <i class="fas fa-circle-notch fa-spin" id="ktSpinner" hidden></i>
                <label for="ktSelect"></label><select class="form-control select2bs4" id="ktSelect" style="width: 100%;" data-placeholder="KT">
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
  var selectedOptionKt;
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
    $('#ktSpinner').removeAttr('hidden');
    $('#kudSpinner').removeAttr('hidden');
    db.collection("kud")
      .where("kebun", "==", selectedOptionKebun)
      .orderBy("kode")
      .get().then((querySnapshot) => {
      $('#kudSelect').empty();
      $('#ktSelect').empty();
      if (data.length) {
        data = [];
        load();
      }
      index = [];
      if(!querySnapshot.size){
        optionList = '';
        $('#kudSpinner').attr('hidden', '');
        $('#ktSpinner').attr('hidden', '');
        $('#kudSelect').append(optionList);
      }
      optionList = '';
      optionList += '<option value="" selected="selected" disabled></option>';
      querySnapshot.forEach((doc) => {
        if (!index.includes(doc.data().kode)) {
          index.push(doc.data().kode);
          optionList += '<option value="' + doc.data().kode + '">' + doc.data().nama_koperasi + ' - ' + doc.data().kode + '</option>';
        }
      });
      $('#kudSpinner').attr('hidden', '');
      $('#ktSpinner').attr('hidden', '');
      $('#kudSelect').append(optionList);
    })
  })

  $('#kudSelect').on('change', function() {
    selectedOptionKud = this.value;
    $('#ktSpinner').removeAttr('hidden');
    db.collection("kt")
      .where("kebun", "==", selectedOptionKebun)
      .where("kud", "==", selectedOptionKud)
      .orderBy("kode")
      .get().then((querySnapshot) => {
      $('#ktSelect').empty();
      if (data.length) {
        data = [];
        load();
      }
      index = [];
      if(!querySnapshot.size){
        optionList = '';
        $('#ktSpinner').attr('hidden', '');
        $('#kudSelect').append(optionList);
      }
      optionList = '';
      optionList += '<option value="" selected="selected" disabled></option>';
      querySnapshot.forEach((doc) => {
        if (!index.includes(doc.data().kode)) {
          index.push(doc.data().kode);
          optionList += '<option value="' + doc.data().kode + '">' + doc.data().nama_kelompok_tani + ' - ' + doc.data().kode + '</option>';
        }
      });
      $('#ktSpinner').attr('hidden', '');
      $('#ktSelect').append(optionList);
    })
  })

  $('#ktSelect').on('change', function() {
    var init = true;
    var contain;
    data = [{keys: ""}]; //onSnapshot fix
    selectedOptionKt = this.value;
    $('#spinner').removeAttr('hidden');

    db.collection("kapling")
      .where("kebun", "==", selectedOptionKebun)
      .where("kud", "==", selectedOptionKud)
      .where("kt", "==", selectedOptionKt)
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
            data.push(tempData);
          });
          console.log(data);
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
        var isEmpty;
        args.item.kebun = selectedOptionKebun;
        args.item.kud = selectedOptionKud;
        args.item.kt = selectedOptionKt;
        if(args.item.kode.length === 1) {
          args.item.kode = "00000" + args.item.kode;
        } else if (args.item.kode.length === 2) {
          args.item.kode = "0000" + args.item.kode;
        } else if (args.item.kode.length === 3) {
          args.item.kode = "000" + args.item.kode;
        } else if (args.item.kode.length === 4) {
          args.item.kode = "00" + args.item.kode;
        } else if (args.item.kode.length === 5) {
          args.item.kode = "0" + args.item.kode;
        }
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
            alert(error);
          });
        if(isEmpty){
          args.item.master = true;
          await db.collection("kapling")
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
              && (!filter.nama_petani.toLowerCase() || client.nama_petani.toLowerCase().indexOf(filter.nama_petani.toLowerCase()) > -1)
              && (!filter.no_kontak.toLowerCase() || client.no_kontak.toLowerCase().indexOf(filter.no_kontak.toLowerCase()) > -1)
              && (!filter.kebun.toLowerCase() || client.kebun.toLowerCase().indexOf(filter.kebun.toLowerCase()) > -1)
              && (!filter.kud.toLowerCase() || client.kud.toLowerCase().indexOf(filter.kud.toLowerCase()) > -1)
              && (!filter.kt.toLowerCase() || client.kt.toLowerCase().indexOf(filter.kt.toLowerCase()) > -1)
              && (filter.master === undefined || client.master === filter.master);
          });
        },
      },

      data: data,

      fields: [
        { name: "kode", title: "Kode", type: "text", width: 60, editing: false, validate: "required" },
        { name: "nama_petani", title: "Nama Petani", type: "text", width: 150, validate: "required" },
        { name: "no_kontak", title: "No Kontak Petani", type: "text", width: 150, validate: "required" },
        { name: "kebun", title: "Kebun", type: "text", width: 150, inserting: false },
        { name: "kud", title: "Kode KUD", type: "text", width: 150, inserting: false },
        { name: "kt", title: "Kode KT", type: "text", width: 150, inserting: false },
        { name: "master", title: "Show", type: "checkbox", width: 60 },
        { type: "control", deleteButton: false}
      ]
    });
  }
</script>
</body>
</html>

