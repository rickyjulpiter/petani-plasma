<!DOCTYPE html>
<html>
<?php include '../templates/header.php'; ?>
<style>
  #highlight, .highlight {
    background-color: #000000;
  }
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
            <h1>Laporan Produksi-Ku</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Laporan / Produksi-Ku</li>
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
                <div class="row">
                  <div class="col-md-6">
                    <label>Dari</label>
                    <label for="tanggalPickerDari"></label><input type="Text" class="form-control" placeholder="Tanggal kunjungan" id="tanggalPickerDari" autocomplete="off">
                  </div>
                  <div class="col-md-6">
                    <label>Sampai</label>
                    <label for="tanggalPickerSampai"></label><input type="Text" class="form-control" placeholder="Tanggal kunjungan" id="tanggalPickerSampai" autocomplete="off">
                  </div>
                </div>
                <span class="text-sm">*Note: Tanggal yang tidak ditandai berarti kosong</span>
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
      <div class="card" id="mapCard" hidden>
        <div class="card-body">
          <div id="mapid" style="height: 500px; z-index: 0"></div>
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
  var selectedDateSampai; // datePickerSampai
  var selectedDateDari // datePickerDari
  var data = [];
  var mapInit = true;
  var layerGroup = L.layerGroup();

  $("#tanggalPickerDari").datepicker({
    changeMonth: true,
    changeYear: true,
    beforeShowDay: highlightDays
  });
  $("#tanggalPickerDari").datepicker("option", "dateFormat", "M d, yy");
  $("#tanggalPickerSampai").datepicker({
    changeMonth: true,
    changeYear: true,
    beforeShowDay: highlightDays
  });
  $("#tanggalPickerSampai").datepicker("option", "dateFormat", "M d, yy");

  function highlightDays(date) {
    for (var i = 0; i < availableDates.length; i++) {
      if (availableDates[i] === date.getTime()) {
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
  })

  $('#kebunSelect').on('change', function() {
    selectedOptionKebun = this.value;
    $('#tanggalSpinner').removeAttr('hidden');
    $('#ktSpinner').removeAttr('hidden');
    $('#kudSpinner').removeAttr('hidden');
    db.collection("kud")
      .where("kebun", "==", selectedOptionKebun)
      .orderBy("kode")
      .get().then((querySnapshot) => {
      $('#kudSelect').empty();
      $('#ktSelect').empty();
      $("#tanggalPickerDari").datepicker("refresh");
      $("#tanggalPickerDari").datepicker("setDate", null);
      $("#tanggalPickerSampai").datepicker("refresh");
      $("#tanggalPickerSampai").datepicker("setDate", null);
      availableDates = [];
      if (data.length) {
        data = [];
        load();
      }
      if (!mapInit) {
        layerGroup.clearLayers();
      }
      index = [];
      if(!querySnapshot.size){
        optionList = '';
        $('#kudSpinner').attr('hidden', '');
        $('#ktSpinner').attr('hidden', '');
        $('#tanggalSpinner').attr('hidden', '');
        $('#kudSelect').append(optionList);
      }
      optionList = '';
      optionList += '<option value="" selected="selected" disabled></option>';
      optionList += '<option value="all">SEMUA</option>';
      querySnapshot.forEach((doc) => {
        if (!index.includes(doc.data().kode)) {
          index.push(doc.data().kode);
          optionList += '<option value="' + doc.data().kode + '">' + doc.data().nama_koperasi + '</option>';
        }
      });
      $('#kudSpinner').attr('hidden', '');
      $('#ktSpinner').attr('hidden', '');
      $('#tanggalSpinner').attr('hidden', '');
      $('#kudSelect').append(optionList);
    })
  })

  $('#kudSelect').on('change', function() {
    selectedOptionKud = this.value;
    $('#tanggalSpinner').removeAttr('hidden');
    $('#ktSpinner').removeAttr('hidden');

    if(selectedOptionKud === "all") {
      db.collection("kt")
        .where("kebun", "==", selectedOptionKebun)
        .orderBy("kode")
        .get().then((querySnapshot) => {
        fetchDataKt(querySnapshot);
      })
    } else {
      db.collection("kt")
        .where("kebun", "==", selectedOptionKebun)
        .where("kud", "==", selectedOptionKud)
        .orderBy("kode")
        .get().then((querySnapshot) => {
        fetchDataKt(querySnapshot);
      })
    }
  })

  function fetchDataKt(querySnapshot) {
    $('#ktSelect').empty();
    $("#tanggalPickerDari").datepicker("refresh");
    $("#tanggalPickerDari").datepicker("setDate", null);
    $("#tanggalPickerSampai").datepicker("refresh");
    $("#tanggalPickerSampai").datepicker("setDate", null);
    availableDates = [];
    if (data.length) {
      data = [];
      load();
    }
    if (!mapInit) {
      layerGroup.clearLayers();
    }
    index = [];
    if(!querySnapshot.size){
      optionList = '';
      $('#ktSpinner').attr('hidden', '');
      $('#tanggalSpinner').attr('hidden', '');
      $('#kudSelect').append(optionList);
    }
    optionList = '';
    optionList += '<option value="" selected="selected" disabled></option>';
    optionList += '<option value="all">SEMUA</option>';
    querySnapshot.forEach((doc) => {
      if (!index.includes(doc.data().kode)) {
        index.push(doc.data().kode);
        optionList += '<option value="' + doc.data().kode + '">' + doc.data().nama_kelompok_tani + '</option>';
      }
    });
    $('#ktSpinner').attr('hidden', '');
    $('#tanggalSpinner').attr('hidden', '');
    $('#ktSelect').append(optionList);
  }

  $('#ktSelect').on('change', function() {
    selectedOptionKt = this.value;
    $('#tanggalSpinner').removeAttr('hidden');
    db.collection("report")
      .where("kebun", "==", selectedOptionKebun)
      .where("kt", "==", selectedOptionKt)
      .get().then((querySnapshot) => {
      $("#tanggalPickerDari").datepicker("refresh");
      $("#tanggalPickerDari").datepicker("setDate", null);
      $("#tanggalPickerSampai").datepicker("refresh");
      $("#tanggalPickerSampai").datepicker("setDate", null);
      availableDates = [];
      if (data.length) {
        data = [];
        load();
      }
      if (!mapInit) {
        layerGroup.clearLayers();
      }
      querySnapshot.forEach((doc) => {
        availableDates.push(new Date(doc.data().updated_at_hasil_kerja.seconds * 1000).setHours(0, 0, 0, 0));
      });
      $('#tanggalSpinner').attr('hidden', '');
    })
  })

  $('#tanggalPickerDari').on('change', function() {
    selectedDateDari = $("#tanggalPickerDari").datepicker("getDate");
    $("#tanggalPickerSampai").datepicker("refresh");
    $("#tanggalPickerSampai").datepicker("setDate", null);
  })

  $('#tanggalPickerSampai').on('change', function() {
    data = [{keys: ""}]; //onSnapshot fix
    selectedDateSampai = $("#tanggalPickerSampai").datepicker("getDate");
    selectedDateSampai.setDate(selectedDateSampai.getDate() + 1);
    selectedDateSampai.setHours(0, 0, 0, 0);
    $("#spinner").removeAttr("hidden");

    if (mapInit) {
      $('#mapCard').removeAttr('hidden');
      var mymap = L.map('mapid', {
        center: [-0.7893, 113.9213],
        zoom: 5
      });
      L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox/satellite-streets-v11',
        tileSize: 512,
        zoomOffset: -1,
        accessToken: 'pk.eyJ1IjoidWx0cmFleHAiLCJhIjoiY2s5dTFvN3I0MWphdzNpcXBkbGxrbWM1diJ9.LjWgAXr8Ol3byKAK5pd-Lg'
      }).addTo(mymap);
      layerGroup.addTo(mymap);
      mapInit = false;
    }
    else {
      layerGroup.clearLayers();
    }

    if(selectedOptionKud === "all") {
      if (selectedOptionKt === "all") {
        db.collection("report")
          .where("kebun", "==", selectedOptionKebun)
          .where("updated_at_hasil_kerja", ">=", selectedDateDari).where("updated_at_hasil_kerja", "<", selectedDateSampai)
          .orderBy("updated_at_hasil_kerja")
          .onSnapshot((querySnapshot) => {
            fetchData(querySnapshot);
          })
      } else {
        db.collection("report")
          .where("kebun", "==", selectedOptionKebun)
          .where("kt", "==", selectedOptionKt)
          .where("updated_at_hasil_kerja", ">=", selectedDateDari).where("updated_at_hasil_kerja", "<", selectedDateSampai)
          .orderBy("updated_at_hasil_kerja")
          .onSnapshot((querySnapshot) => {
            fetchData(querySnapshot);
          })
      }

    } else if(selectedOptionKt === "all") {
      db.collection("report")
        .where("kebun", "==", selectedOptionKebun)
        .where("kud", "==", selectedOptionKud)
        .where("updated_at_hasil_kerja", ">=", selectedDateDari).where("updated_at_hasil_kerja", "<", selectedDateSampai)
        .orderBy("updated_at_hasil_kerja")
        .onSnapshot((querySnapshot) => {
          fetchData(querySnapshot);
        })
    } else {
      db.collection("report")
        .where("kebun", "==", selectedOptionKebun)
        .where("kud", "==", selectedOptionKud)
        .where("kt", "==", selectedOptionKt)
        .where("updated_at_hasil_kerja", ">=", selectedDateDari).where("updated_at_hasil_kerja", "<", selectedDateSampai)
        .orderBy("updated_at_hasil_kerja")
        .onSnapshot((querySnapshot) => {
          fetchData(querySnapshot);
        })
    }
  })

  function fetchData(querySnapshot) {
    var init = true;
    var contain;
    // onSnapshot listen to all document in a collection, so it did not filter the 'WHERE' arguments
    // if there is an update on db after the first db load. The result, table update out of the 'WHERE' range
    // The solution is add a contain var and check the if statement
    if (!querySnapshot.empty) {
      contain = data[0].keys === querySnapshot.docs[0].id;
    }
    if (contain || init) {
      data = [];
      var documentSize = querySnapshot.size;
      if(!querySnapshot.size){
        load();
      }
      querySnapshot.forEach((doc) => {
        var nama = "";
        //fetch nama pegawai
        db.collection("users").doc(doc.data().id_user).get().then((doc1) => {
          nama = doc1.data().nama_pegawai;

          const tempData = doc.data();
          tempData['keys'] = doc.id;
          tempData['tanggal'] = new Date(doc.data().updated_at_hasil_kerja.seconds * 1000).toLocaleString();
          tempData['nama_pegawai'] = nama;
          data.push(tempData);
          var marker = L.marker([doc.data().location_hasil_kerja.lat, doc.data().location_hasil_kerja.long]).addTo(layerGroup);
          marker.bindPopup(nama + "<br>" + doc.data().nama_petani + "</br>");

          if(data.length === documentSize) {
            load();
          }
        })
      });
    }
  }

  function load() {
    var mapInitTable = true;
    var refreshCount = 0;
    $("#spinner").attr("hidden", "");
    $("#jsGrid1").jsGrid({
      height: 600,
      width: "100%",

      filtering: true,
      editing: false,
      sorting: true,
      autoload: true,
      paging: true,
      pageSize: 10,

      onRefreshed: function(args) {
        console.log(mapInitTable);
        if (!mapInitTable && refreshCount >= 2) {
          layerGroup.clearLayers();
          var item = args.grid.data;
          item.forEach((data) => {
            var marker = L.marker([data.location_hasil_kerja.lat, data.location_hasil_kerja.long]).addTo(layerGroup);
            marker.bindPopup(data.nama_pegawai + "<br>" + data.nama_petani + "</br>");
          })
        }
        mapInitTable = false;
        refreshCount += 1;
      },

      controller: {
        loadData: function(filter) {
          return $.grep(data, function(client) {
            return (!filter.tanggal.toLowerCase() || client.tanggal.toLowerCase().indexOf(filter.tanggal.toLowerCase()) > -1)
              && (!filter.nama_pegawai.toLowerCase() || client.nama_pegawai.toLowerCase().indexOf(filter.nama_pegawai.toLowerCase()) > -1)
              && (!filter.kapling.toLowerCase() || client.kapling.toLowerCase().indexOf(filter.kapling.toLowerCase()) > -1)
              && (!filter.kondisi.toLowerCase() || client.kondisi.toLowerCase().indexOf(filter.kondisi.toLowerCase()) > -1)
              && (!filter.prioritas.toLowerCase() || client.prioritas.toLowerCase().indexOf(filter.prioritas.toLowerCase()) > -1)
              && (!filter.saran.toLowerCase() || client.saran.toLowerCase().indexOf(filter.saran.toLowerCase()) > -1);
          });
        },
      },

      data: data,

      fields: [
        { name: "tanggal", title: "Tanggal", type: "text", width: 85, editing: false },
        { name: "nama_pegawai", title: "Nama Pegawai", type: "text", width: 100, editing: false },
        { name: "kapling", title: "Kapling", type: "text", width: 100, editing: false },
        { name: "kondisi", title: "Kondisi Kapling saat kunjungan", type: "text", width: 170 },
        { name: "prioritas", title: "Type", type: "text", width: 55 },
        { name: "saran", title: "Saran", type: "text", width: 120 },
        { name: "url_pic_hasil_kerja", title: "Foto", type: "text", width: 85, sorting: false,
          itemTemplate: function (value, item) {
            if(value === null){
              return $("<div>").text("-");
            }
            else {
              return $("<a>").attr("href", value).attr("target", "_blank").text("Tampilkan");
            }
          }
        }
      ]
    });
  }
</script>
</body>
</html>


