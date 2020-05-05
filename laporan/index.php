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
              <div class="form-group">
                <label>KUD</label>
                <i class="fas fa-circle-notch fa-spin" id="kudSpinner" hidden></i>
                <label for="kudSelect"></label><select class="form-control select2bs4" id="kudSelect" style="width: 100%;" data-placeholder="KUD">
                  <option selected="selected" value="" disabled></option>
                </select>
              </div>
              <!-- /.form-group -->
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-4">
              <div class="form-group">
                <label>KT</label>
                <i class="fas fa-circle-notch fa-spin" id="ktSpinner" hidden></i>
                <label for="ktSelect"></label><select class="form-control select2bs4" id="ktSelect" style="width: 100%;" data-placeholder="KT">
                  <option selected="selected" value="" disabled></option>
                </select>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label>Tanggal kunjungan</label>
                <i class="fas fa-circle-notch fa-spin" id="tanggalSpinner" hidden></i>
                <label for="tanggalPicker"></label><input type="Text" class="form-control" placeholder="Tanggal kunjungan" id="tanggalPicker">
                <span class="text-sm">*Note: Tanggal yang tidak ditandai berarti kosong</span>
              </div>
              <!-- /.form-group -->
            </div>
          </div>
          <div id="jsGrid1"></div>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
      <div class="card">
        <div class="card-body">
          <div id="mapid" style="height: 500px;"></div>
        </div>
        <!-- /.card-body-->
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

  var db = firebase.firestore();
  var optionList;
  var index;
  var selectedOptionKebun;
  var selectedOptionKud;
  var selectedOptionKt;
  var availableDates = [];
  var selectedDate;
  var data = [];

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

  db.collection("kebun")
    .orderBy("kode")
    .get().then((querySnapshot) => {
    optionList = '';
    optionList += '<option value="" selected="selected" disabled></option>';
    querySnapshot.forEach((doc) => {
      optionList += '<option value="' + doc.data().kode + '">' + doc.data().kode + '</option>';
    });
    $('#kebunSelect').append(optionList);
    mymap.invalidateSize();
  })

  $('#kebunSelect').on('change', function() {
    console.log("kebun onChange " + this.value);
    selectedOptionKebun = this.value;
    $('#tanggalSpinner').removeAttr('hidden');
    $('#ktSpinner').removeAttr('hidden');
    $('#kudSpinner').removeAttr('hidden');
    db.collection("report")
      .where("kebun", "==", selectedOptionKebun)
      .orderBy("kud")
      .get().then((querySnapshot) => {
        $('#kudSelect').empty();
        $('#ktSelect').empty();
        $("#tanggalPicker").datepicker("refresh");
        $("#tanggalPicker").datepicker("setDate", null);
        availableDates = [];
        if (data.length) {
          data = [];
          load();
        }
        optionList = '';
        optionList += '<option value="" selected="selected" disabled></option>';
        index = [];
        querySnapshot.forEach((doc) => {
          if (!index.includes(doc.data().kud)) {
            index.push(doc.data().kud);
            optionList += '<option value="' + doc.data().kud + '">' + doc.data().kud + '</option>';
          }
        });
        $('#kudSpinner').attr('hidden', '');
        $('#ktSpinner').attr('hidden', '');
        $('#tanggalSpinner').attr('hidden', '');
        $('#kudSelect').append(optionList);
      })
  })

  $('#kudSelect').on('change', function() {
    console.log("kud onChange " + this.value);
    selectedOptionKud = this.value;
    $('#tanggalSpinner').removeAttr('hidden');
    $('#ktSpinner').removeAttr('hidden');
    db.collection("report")
      .where("kebun", "==", selectedOptionKebun)
      .where("kud", "==", selectedOptionKud)
      .orderBy("kt")
      .get().then((querySnapshot) => {
        $('#ktSelect').empty();
        $("#tanggalPicker").datepicker("refresh");
        $("#tanggalPicker").datepicker("setDate", null);
        availableDates = [];
        if (data.length) {
          data = [];
          load();
        }
        optionList = '';
        optionList += '<option value="" selected="selected" disabled></option>';
        index = [];
        querySnapshot.forEach((doc) => {
          if (!index.includes(doc.data().kt)) {
            index.push(doc.data().kt);
            optionList += '<option value="' + doc.data().kt + '">' + doc.data().kt + '</option>';
          }
        });
        $('#ktSpinner').attr('hidden', '');
        $('#tanggalSpinner').attr('hidden', '');
        $('#ktSelect').append(optionList);
      })
  })

  $('#ktSelect').on('change', function() {
    console.log("kt onChange " + this.value);
    selectedOptionKt = this.value;
    $('#tanggalSpinner').removeAttr('hidden');
    db.collection("report")
      .where("kebun", "==", selectedOptionKebun)
      .where("kud", "==", selectedOptionKud)
      .where("kt", "==", selectedOptionKt)
      .get().then((querySnapshot) => {
        $("#tanggalPicker").datepicker("refresh");
        $("#tanggalPicker").datepicker("setDate", null);
        availableDates = [];
        if (data.length) {
          data = [];
          load();
        }
        querySnapshot.forEach((doc) => {
          console.log(doc.data().tanggal);
          availableDates.push((doc.data().tanggal.seconds * 1000));
          console.log(availableDates);
        });
        $('#tanggalSpinner').attr('hidden', '');
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
      .onSnapshot((querySnapshot) => {
        data = [];
        querySnapshot.forEach((doc) => {
          const tempData = doc.data();
          tempData['keys'] = doc.id;
          data.push(tempData);
          console.log(doc.data());
        });
        load();
      })
  })

  function load() {
    console.log(data);
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
        await db.collection("report").doc(args.previousItem.keys)
          .update(args.item)
          .then(function () {
            console.log('Data update berhasil');
          }).catch(function (error) {
            console.log("Error updating document: ", error);
            alert('Data bermasalah');
          });

      },

      controller: {
        loadData: function(filter) {
          return $.grep(data, function(client) {
            return (!filter.kapling.toLowerCase() || client.kapling.toLowerCase().indexOf(filter.kapling.toLowerCase()) > -1)
              && (!filter.kondisi.toLowerCase() || client.kondisi.toLowerCase().indexOf(filter.kondisi.toLowerCase()) > -1)
              && (!filter.tipe.toLowerCase() || client.tipe.toLowerCase().indexOf(filter.tipe.toLowerCase()) > -1)
              && (!filter.saran.toLowerCase() || client.saran.toLowerCase().indexOf(filter.saran.toLowerCase()) > -1)
              && (!filter.pendapat.toLowerCase() || client.pendapat.toLowerCase().indexOf(filter.pendapat.toLowerCase()) > -1)
              && (!filter.nama_petani.toLowerCase() || client.nama_petani.toLowerCase().indexOf(filter.nama_petani.toLowerCase()) > -1)
              && (!filter.no_kontak.toLowerCase() || client.no_kontak.toLowerCase().indexOf(filter.no_kontak.toLowerCase()) > -1);
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
        { name: "kode", title: "Kode", type: "text", width: 100, editing: false, visible: false },
        { name: "kud", title: "Kode", type: "text", width: 100, editing: false, visible: false },
        { name: "kt", title: "Kode", type: "text", width: 100, editing: false, visible: false },
        { name: "tanggal", title: "Kode", type: "text", width: 100, editing: false, visible: false },
        { name: "kapling", title: "Kapling", type: "text", width: 100 },
        { name: "kondisi", title: "Kondisi Kapling saat kunjungan", type: "text", width: 170 },
        { name: "tipe", title: "Type", type: "text", width: 40, validate: "required" },
        { name: "saran", title: "Saran", type: "text", width: 120 },
        { name: "pendapat", title: "Pendapat petani", type: "text", width: 120 },
        { name: "nama_petani", title: "Nama Petani", type: "text", width: 100 },
        { name: "no_kontak", title: "No Kontak", type: "text", width: 100 },
        { type: "control", deleteButton: false}
      ]
    });
  }

  var mymap = L.map('mapid', {
    center: [-0.7893, 113.9213],
    zoom: 5
  });

  L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/streets-v11',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: 'pk.eyJ1IjoidWx0cmFleHAiLCJhIjoiY2s5dTFvN3I0MWphdzNpcXBkbGxrbWM1diJ9.LjWgAXr8Ol3byKAK5pd-Lg'
  }).addTo(mymap);

  var marker = L.marker([-6.2088, 106.8456]).addTo(mymap);
  marker.bindPopup("<b>Hello world!</b><br>I am a popup.");
  var marker = L.marker([3.5952, 98.6722]).addTo(mymap);
  marker.bindPopup("<b>Hello world2!</b><br>I am a popup.");



</script>
</body>
</html>

