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
            <h1>News</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">/ News/li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="card">
        <!-- /.card-header -->
        <div class="card-header">
          <button onclick="modalDefault()" class="btn btn-block btn-info btn-sm col-2" data-toggle="modal"
                  data-target="#modal-default">Tambah News
          </button>
        </div>
        <div class="card-body">
          <div id="spinner" style="text-align:center;">
            <span>Data Loading </span><i class="fas fa-circle-notch fa-spin"></i>
          </div>
          <table id="table" class="table table-bordered table-striped" hidden>
            <thead>
            <tr>
              <th>Tanggal</th>
              <th>Judul</th>
              <th>Isi</th>
              <th>Foto</th>
              <th>Video</th>
              <th>Master</th>
              <th>Control</th>
            </tr>
            </thead>
            <tbody id="table-tbody"></tbody>
          </table>
        </div>
        <!-- /.card-body -->

        <div class="modal fade" id="modal-default">
          <div class="modal-dialog">
            <div class="modal-content">
              <form action="" method="post" enctype="multipart/form-data" onsubmit="return false">
                <div class="modal-header">
                  <h4 class="modal-title" id="modalTitle"></h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label for="title">Judul</label><input type="text" class="form-control" placeholder="Judul"
                                                             id="title"
                                                             autocomplete="off" required/>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label for="body">Isi</label><textarea type="text" class="form-control" placeholder="Isi" rows="6"
                                                             id="body" required></textarea>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="col-sm-12">
                        <div class="form-group">
                          <label for="url_video">Link Video</label><input type="text" class="form-control"
                                                                          placeholder="Link Video"
                                                                          id="url_video"
                                                                          autocomplete="off"/>
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-group">
                          <label for="master">Master</label><select class="form-control select2bs4" id="master"
                                                                    data-placeholder="Master" style="width: 100%">
                            <option value="1" selected>True</option>
                            <option value="0">False</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Foto</label>
                        <img id="imageTag" class="images" src="../dist/img/avatar.png"
                             style="width: 215px; height: 215px; object-fit: fill;">
                        <input type="file" accept="image/" id="url_foto" required/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer justify-content-between">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary" id="buttonSaveChange">Submit</i>
                  </button>
                </div>
              </form>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
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
  window.onload = function () {
    initApp();
  };
  //Initialize Select2 Elements
  jQuery('.select2bs4').select2({
    theme: 'bootstrap4'
  })
</script>
<script>
  var tableJq = jQuery("#table");
  var db = firebase.firestore();
  var tableData = [];
  var initStat = true;
  var table;
  let $modalTitle = jQuery("#modalTitle");
  let $titleInput = jQuery("#title");
  let $bodyInput = jQuery("#body");
  let $urlVideoInput = jQuery("#url_video");
  let $fotoInput = jQuery("#url_foto");
  let $master = jQuery('#master');
  let $buttonSaveChange = jQuery("#buttonSaveChange");
  let pic = null;
  db.collection("berita")
    .onSnapshot((querySnapshot) => {
      tableData = [];
      if (initStat === false) {
        table.destroy();
      }
      querySnapshot.forEach((doc) => {
        tempData = doc.data();
        tempId = doc.id;
        tempData['keys'] = tempId;
        tempData['date'] = new Date(doc.data().date.seconds * 1000).toLocaleDateString();
        tableData.push(tempData);
      });
      console.log(tableData);
      table = tableJq.DataTable({
        "responsive": true,
        "autoWidth": false,
        "data": tableData,
        "columns": [
          {"data": "date"},
          {"data": "title"},
          {"data": "body", "orderable": false,},
          {
            "data": "url_pic",
            "orderable": false,
            "render": function (data, type, full, meta) {
              return data ? '<a href="' + data + '" target="_blank">Link</a>' : '';
            },

          },
          {
            "data": "url_video",
            "orderable": false,
            "render": function (data, type, full, meta) {
              return data ? '<a href="' + data + '" target="_blank">Link</a>' : '';
            },
          },
          {
            "data": "master",
            "orderable": false,
            "className": "text-center",
            "render": function (data, type, full, meta) {
              return data ? '<input type="checkbox" disabled checked/>' : '<input type="checkbox" disabled/>';
            },
          },
          {
            "data": "keys",
            "orderable": false,
            "render": function (data, type, full, meta) {
              let index = tableData.indexOf(full)
              return '<button class = "btn btn-block btn-primary btn-sm" onclick = "showModal (' + index + ')" data-toggle="modal" data-target="#modal-default">Edit</button>' +
                '<button class ="btn btn-block btn-danger btn-sm" onclick = "deleteNews (' + index + ')">Delete</button>';
            },
          }
        ],
      });
      initStat = false;
      tableJq.removeAttr("hidden");
      jQuery("#spinner").attr("hidden", "");
    });

  function showModal(id) {
    jQuery("#imageTag").removeAttr("hidden");
    $modalTitle.text("Edit News");
    $titleInput.val(tableData[id].title)
    $bodyInput.val(tableData[id].body);
    $urlVideoInput.val(tableData[id].url_video);
    $fotoInput.val("");
    $fotoInput.removeAttr("required");
    console.log(tableData[id].url_pic);
    jQuery("#imageTag").attr("src", tableData[id].url_pic);
    if (tableData[id].master) {
      jQuery('#master').val("1").change();
    } else {
      jQuery('#master').val("0").change();
    }
    $buttonSaveChange.attr("onclick", "editNews(" + id + ")");
  }

  function modalDefault() {
    $fotoInput.attr("required", "required");
    $modalTitle.text("Tambah News");
    $titleInput.val("")
    $bodyInput.val("");
    $urlVideoInput.val("");
    jQuery('#master').val("1").change();
    $buttonSaveChange.attr("onclick", "addNews()");
    $fotoInput.val("");
    jQuery("#imageTag").attr("hidden", "hidden");
    $buttonSaveChange.html('Submit');
  }

  $fotoInput.change(function (e) {
    console.log("test");
    pic = e.target.files[0];
    console.log(pic);
  })

  async function addNews() {
    let todayDate = new Date();
    let title = $titleInput.val();
    let body = $bodyInput.val();
    let url_video = $urlVideoInput.val();
    let master = ($master.val() === '1');
    let pic_link;
    if (title === "" || body === "" || pic === null) {
      return;
    } else if (title.length > 50) {
      alert("Judul berita maks 50 karakter");
      return;
    } else if (body.length > 300) {
      alert("Isi berita maks 300 karakter");
      return;
    } else {
      $buttonSaveChange.html('<i class="fas fa-circle-notch fa-spin" id="spinnerSubmit">');
    }

    var uploadTask = firebase.storage().ref('images/news/' + todayDate.getTime()).put(pic);
    uploadTask.on('state_changed', function (snapshot) {
      var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
      console.log('Upload is ' + progress + '% done');
      switch (snapshot.state) {
        case firebase.storage.TaskState.PAUSED: // or 'paused'
          console.log('Upload is paused');
          break;
        case firebase.storage.TaskState.RUNNING: // or 'running'
          console.log('Upload is running');
          break;
      }
    }, function (error) {
      switch (error.code) {
        case 'storage/unauthorized':
          // User doesn't have permission to access the object
          console.log("unauthorized");
          break;
        case 'storage/canceled':
          // User canceled the upload
          console.log("canceled");
          break;
        case 'storage/unknown':
          // Unknown error occurred, inspect error.serverResponse
          console.log("unknown");
          break;
      }
    }, async function () {
      // Handle successful uploads on complete
      await uploadTask.snapshot.ref.getDownloadURL().then(function (downloadURL) {
        console.log('File available at', downloadURL);
        pic_link = downloadURL;
      });

      var newsData = {
        body: body,
        date: todayDate,
        master: master,
        title: title,
        url_pic: pic_link,
        url_video: url_video
      }

      await db.collection("berita")
        .add(newsData)
        .then(function () {
          console.log("Success");
          $buttonSaveChange.html('Submit');
          jQuery("#modal-default").modal('hide');
          pic = null;
        })
        .catch(function (error) {
          console.error(error);
        });
    });
  }

  function editNews(id) {
    let todayDate = new Date();
    let title = $titleInput.val();
    let body = $bodyInput.val();
    let url_video = $urlVideoInput.val();
    let master = ($master.val() === '1');
    let pic_link;

    if (title === "" || body === "") {
      return;
    } else if (title.length > 50) {
      alert("Judul berita maks 50 karakter");
      return;
    } else if (body.length > 300) {
      alert("Isi berita maks 300 karakter");
      return;
    } else {
      $buttonSaveChange.html('<i class="fas fa-circle-notch fa-spin" id="spinnerSubmit">');
    }

    if (pic === null) {
      db.collection("berita").doc(tableData[id].keys).update({
        body: body,
        master: master,
        title: title,
        url_video: url_video
      })
        .then(function () {
          console.log("Document successfully updated!");
          $buttonSaveChange.html('Submit');
          jQuery("#modal-default").modal('hide');
        })
        .catch(function (error) {
          // The document probably doesn't exist.
          console.error("Error updating document: ", error);
        });
    } else {
      var uploadTask = firebase.storage().ref('images/news/' + todayDate.getTime()).put(pic);
      uploadTask.on('state_changed', function (snapshot) {
        var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
        console.log('Upload is ' + progress + '% done');
        switch (snapshot.state) {
          case firebase.storage.TaskState.PAUSED: // or 'paused'
            console.log('Upload is paused');
            break;
          case firebase.storage.TaskState.RUNNING: // or 'running'
            console.log('Upload is running');
            break;
        }
      }, function (error) {
        switch (error.code) {
          case 'storage/unauthorized':
            // User doesn't have permission to access the object
            console.log("unauthorized");
            break;
          case 'storage/canceled':
            // User canceled the upload
            console.log("canceled");
            break;
          case 'storage/unknown':
            // Unknown error occurred, inspect error.serverResponse
            console.log("unknown");
            break;
        }
      }, async function () {
        // Handle successful uploads on complete
        await uploadTask.snapshot.ref.getDownloadURL().then(function (downloadURL) {
          console.log('File available at', downloadURL);
          pic_link = downloadURL;
        });

        await db.collection("berita").doc(tableData[id].keys).update({
          body: body,
          master: master,
          title: title,
          url_pic: pic_link,
          url_video: url_video
        })
          .then(function () {
            console.log("Document successfully updated!");
            $buttonSaveChange.html('Submit');
            jQuery("#modal-default").modal('hide');
          })
          .catch(function (error) {
            // The document probably doesn't exist.
            console.error("Error updating document: ", error);
          });
      });
    }
  }

  function deleteNews(id) {
    db.collection("berita").doc(tableData[id].keys).delete().then(function(){
      console.log("Document successfully deleted!");
    }).catch(function(error) {
      console.error("Error removing document: ", error);
    });
  }
</script>
</body>
</html>

