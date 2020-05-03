<!DOCTYPE html>
<html>
<?php include '../templates/header.php'; ?>
<style>
  #highlight, .highlight {
    background-color: #000000;
  }
</style>
<body class="hold-transition sidebar-mini">
<div id="spinnerContent">
  <i class="fas fa-circle-notch fa-spin"></i>
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
            <h1>Laporan Kunjungan Staff</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Laporan</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Kebun</label>
            <select class="form-control select2bs4" id="kebunSelect" style="width: 100%;" data-placeholder="Kebun">
              <option selected="selected" value="" disabled></option>
              <option value="KLB">KLB</option>
              <option value="KLM">KLM</option>
              <option value="KLO">KLO</option>
              <option value="KLP">KLP</option>
              <option value="KLT">KLT</option>
              <option value="KLU">KLU</option>
            </select>
          </div>
          <!-- /.form-group -->
          <div class="form-group">
            <label>KUD</label>
            <select class="form-control select2bs4"id="kudSelect" style="width: 100%;" data-placeholder="KUD">
              <option selected="selected" value="" disabled></option>
            </select>
          </div>
          <!-- /.form-group -->
          <div class="form-group">
            <label>KT</label>
            <select class="form-control select2bs4" id="ktSelect" style="width: 100%;" data-placeholder="KT">
              <option selected="selected" value="" disabled></option>
            </select>
          </div>
          <!-- /.form-group -->
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal kunjungan</label>
            <input type="Text" class="form-control" placeholder="Tanggal kunjungan" id="tanggalPicker">
            <span class="text-sm">*Note: Tanggal yang tidak ditandai berarti kosong</span>
          </div>
          <!-- /.form-group -->
          <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control" rows="4" placeholder="Keterangan"></textarea>
          </div>
          <!-- /.form-group -->
        </div>
      </div>
      <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
<!--          <div id="jsGrid1"></div>-->
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
</script>

<!-- onChange Script -->
<script>
  var db = firebase.firestore();
  var optionList;
  var index;
  var selectedOptionKebun;
  var selectedOptionKud;
  var selectedOptionKt;
  var availableDates = [];
  var selectedDate;

  $("#tanggalPicker").datepicker({
    changeMonth: true,
    changeYear: true,
    beforeShowDay: highlightDays
  });
  $("#tanggalPicker").datepicker("option", "dateFormat", "M d, yy");

  function highlightDays(date) {
    for (var i = 0; i < availableDates.length; i++) {
      if (availableDates[i] == date.getTime()) {
        return [true, 'highlight'];
      }
    }
    return [true, ''];
  }

  $('#kebunSelect').on('change', function() {
    console.log("kebun onChange " + this.value);
    selectedOptionKebun = this.value;
    db.collection("report")
      .where("kebun", "==", selectedOptionKebun)
      .get().then((querySnapshot) => {
        $('#kudSelect').empty();
        $('#ktSelect').empty();
        optionList = '';
        optionList += '<option value="" selected="selected" disabled></option>';
        index = [];
        querySnapshot.forEach((doc) => {
          if (!index.includes(doc.data().kud)) {
            index.push(doc.data().kud);
            optionList += '<option value="' + doc.data().kud + '">' + doc.data().kud + '</option>';
          }
        });
        $('#kudSelect').append(optionList);
      })
  })

  $('#kudSelect').on('change', function() {
    console.log("kud onChange " + this.value);
    selectedOptionKud = this.value;
    db.collection("report")
      .where("kebun", "==", selectedOptionKebun)
      .where("kud", "==", selectedOptionKud)
      .get().then((querySnapshot) => {
        $('#ktSelect').empty();
        optionList = '';
        optionList += '<option value="" selected="selected" disabled></option>';
        index = [];
        querySnapshot.forEach((doc) => {
          if (!index.includes(doc.data().kt)) {
            index.push(doc.data().kt);
            optionList += '<option value="' + doc.data().kt + '">' + doc.data().kt + '</option>';
          }
        });
        $('#ktSelect').append(optionList);
      })
  })

  $('#ktSelect').on('change', function() {
    console.log("kt onChange " + this.value);
    selectedOptionKt = this.value;
    db.collection("report")
      .where("kebun", "==", selectedOptionKebun)
      .where("kud", "==", selectedOptionKud)
      .where("kt", "==", selectedOptionKt)
      .get().then((querySnapshot) => {
        availableDates = [];
        querySnapshot.forEach((doc) => {
          console.log(doc.data().tanggal);
          availableDates.push((doc.data().tanggal.seconds * 1000));
          console.log(availableDates);
        });
      })
  })

  $('#tanggalPicker').on('change', function() {
    selectedDate = $("#tanggalPicker").datepicker("getDate");
    console.log("tanggal onChange " + selectedDate);
    db.collection("report")
      .where("kebun", "==", selectedOptionKebun)
      .where("kud", "==", selectedOptionKud)
      .where("kt", "==", selectedOptionKt)
      .where("tanggal", "==", selectedDate)
      .get().then((querySnapshot) => {
      querySnapshot.forEach((doc) => {
        console.log(doc.data());
      });
    })
  })

  // var data = [];
  // var id = [];
  // db.collection("kebun").orderBy("kode")
  //   .onSnapshot((querySnapshot) => {
  //     data = [];
  //     querySnapshot.forEach((doc) => {
  //       tempData = doc.data();
  //       tempId = doc.id;
  //       tempData['keys'] = tempId;
  //       data.push(tempData);
  //     });
  //     load();
  //   });

  function load() {
    $("#spinner").remove();
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
        delete args.item['keys'];
        await db.collection("kebun").doc(args.previousItem.keys)
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
            return (!filter.kode.toLowerCase() || client.kode.toLowerCase().indexOf(filter.kode.toLowerCase()) > -1)
              && (!filter.nama.toLowerCase() || client.nama.toLowerCase().indexOf(filter.nama.toLowerCase()) > -1)
              && (!filter.manager.toLowerCase() || client.manager.toLowerCase().indexOf(filter.manager.toLowerCase()) > -1)
              && (!filter.kelompok.toLowerCase() || client.kelompok.toLowerCase().indexOf(filter.kelompok.toLowerCase()) > -1)
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
        { name: "kode", title: "Kode", type: "text", width: 100, editing: false },
        { name: "nama", title: "Nama", type: "text", width: 300, editing: false },
        { name: "manager", title: "Manager", type: "text", width: 300, validate: "required" },
        { name: "kelompok", title: "Group", type: "text", width: 100 },
        { name: "master", title: "Show", type: "checkbox", width: 60 },
        { type: "control" , deleteButton: false }
      ]
    });
  }
</script>
</body>
</html>

