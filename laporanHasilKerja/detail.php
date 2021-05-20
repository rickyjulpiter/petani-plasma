<!DOCTYPE html>
<html>
<?php include '../templates/header.php'; ?>
<style>
    #highlight,
    .highlight {
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
                            <h1>Detail Laporan Kerja</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Laporan / Kerja-Ku</li>
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
                        <div id="spinner" style="text-align:center;">
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
        var selectedOptionStaff;
        var selectedOptionKebun;
        var selectedOptionKud;
        var selectedOptionKt;
        var availableDates = [];
        var selectedDateSampai; // datePickerSampai
        var selectedDateDari // datePickerDari
        var tableData = [];
        var mapInit = true;
        var layerGroup = L.layerGroup();

        reportId = localStorage.getItem("report_id");

        fetchDataAndSubLaporan(db);

        async function fetchDataAndSubLaporan(db) {
            db.collection("report").doc(reportId).get().then(async (querySnapshot) => {
                var createdAt = new Date(querySnapshot.data().created_at.seconds * 1000).toLocaleString();
                tableData.push({
                    'created_at': createdAt,
                    'url_picture': querySnapshot.data()['url_picture'],
                    'location': querySnapshot.data()['location'],
                });
                var marker = L.marker([querySnapshot.data().location.lat, querySnapshot.data().location.long]).addTo(layerGroup);
                marker.bindPopup(createdAt);
                await db.collection("report").doc(reportId).collection("sub-laporan-kerja").get().then((querySnapshot) => {
                    querySnapshot.forEach((doc) => {
                        var createdAt = new Date(doc.data().created_at.seconds * 1000).toLocaleString();
                        tableData.push({
                            'created_at': createdAt,
                            'url_picture': doc.data()['url_picture'],
                            'location': doc.data()['location'],
                        });
                        var marker = L.marker([doc.data().location.lat, doc.data().location.long]).addTo(layerGroup);
                        marker.bindPopup(createdAt);
                    });
                });
                load();
                drawMap();
            });
        }

        function drawMap() {
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
        }

        function load() {
            var mapInitTable = true;
            var refreshCount = 0;
            $("#spinner").attr("hidden", "");

            $("#jsGrid1").jsGrid({
                height: 350,
                width: "100%",

                filtering: true,
                editing: false,
                sorting: true,
                autoload: true,
                paging: true,
                pageSize: 5,

                onRefreshed: function(args) {
                    if (!mapInitTable && refreshCount >= 2) {
                        layerGroup.clearLayers();
                        var item = args.grid.data;
                        item.forEach((data) => {
                            var marker = L.marker([data.location.lat, data.location.long]).addTo(layerGroup);
                            marker.bindPopup(data.nama_pegawai + "<br>" + data.nama_petani + "</br>");
                        })
                    }
                    mapInitTable = false;
                    refreshCount += 1;
                },

                data: tableData,

                fields: [{
                        name: "created_at",
                        title: "Tanggal",
                        type: "text",
                        width: 85,
                        editing: false,
                    },
                    {
                        name: "url_picture",
                        title: "Gambar",
                        type: "text",
                        width: 100,
                        editing: false,
                        itemTemplate: function(value, item) {
                            if (value === null) {
                                return $("<div>").text("-");
                            } else {
                                return $("<a>").attr("href", value).attr("target", "_blank").text("Tampilkan");
                            }
                        }
                        // sort: false,
                    },
                ]
            });
        }
    </script>
</body>

</html>