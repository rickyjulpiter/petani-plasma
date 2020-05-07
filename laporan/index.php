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
              <li class="breadcrumb-item active">Laporan / Kerja Staff</li>
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
      <div class="card" id="mapCard" hidden>
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
  var mapInit = true;
  var layerGroup = L.layerGroup();

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
        if (!mapInit) {
          layerGroup.clearLayers();
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
        if (!mapInit) {
          layerGroup.clearLayers();
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
        if (!mapInit) {
          layerGroup.clearLayers();
        }
        querySnapshot.forEach((doc) => {
          console.log(doc.data().create_at.seconds);
          availableDates.push(new Date(doc.data().create_at.seconds * 1000).setHours(0, 0, 0, 0));
          console.log(availableDates);
        });
        $('#tanggalSpinner').attr('hidden', '');
      })
  })

  $('#tanggalPicker').on('change', function() {
    selectedDate = $("#tanggalPicker").datepicker("getDate");
    var selectedDateTomorrow = new Date();
    selectedDateTomorrow.setDate(selectedDate.getDate() + 1);

    if (mapInit) {
      $('#mapCard').removeAttr('hidden');
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
      layerGroup.addTo(mymap);
      mapInit = false;
    }
    else {
      layerGroup.clearLayers();
    }

    db.collection("report")
      .where("kebun", "==", selectedOptionKebun)
      .where("kud", "==", selectedOptionKud)
      .where("kt", "==", selectedOptionKt)
      .where("create_at", ">=", selectedDate).where("create_at", "<=", selectedDateTomorrow)
      .orderBy("create_at")
      .onSnapshot((querySnapshot) => {
        data = [];
        querySnapshot.forEach((doc) => {
          const tempData = doc.data();
          tempData['keys'] = doc.id;
          data.push(tempData);
          var circle = L.circle([doc.data().location_hasil_kerja.lat, doc.data().location_hasil_kerja.long], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: 100
          }).addTo(layerGroup);
          circle.bindPopup("<b>Kapling</b><br>" + doc.data().kapling + "</br>");
          // var marker = L.marker([doc.data().location_hasil_kerja.lat, doc.data().location_hasil_kerja.long]).addTo(layerGroup);
          // marker.bindPopup("<b>Kapling</b><br>" + doc.data().kapling + "</br>");
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
              && (!filter.prioritas.toLowerCase() || client.prioritas.toLowerCase().indexOf(filter.prioritas.toLowerCase()) > -1)
              && (!filter.saran.toLowerCase() || client.saran.toLowerCase().indexOf(filter.saran.toLowerCase()) > -1);
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
        { name: "kapling", title: "Kapling", type: "text", width: 100 },
        { name: "kondisi", title: "Kondisi Kapling saat kunjungan", type: "text", width: 170 },
        { name: "prioritas", title: "Type", type: "text", width: 40, validate: "required" },
        { name: "saran", title: "Saran", type: "text", width: 120 },
        { type: "control", deleteButton: false}
      ]
    });
  }
</script>
</body>
</html>

