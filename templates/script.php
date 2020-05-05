<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="../plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../plugins/moment/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- jsGrid -->
<script src="../plugins/jsgrid/demos/db.js"></script>
<script src="../plugins/jsgrid/jsgrid.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- Leafletjs -->
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
        integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
        crossorigin=""></script>


<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.14.2/firebase-app.js"></script>

<!-- Add SDKs for Firebase products that you want to use https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/7.14.2/firebase-analytics.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.14.2/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.14.2/firebase-firestore.js"></script>

<script>
  // Your web app's Firebase configuration
  var firebaseConfig = {
    apiKey: "AIzaSyCxyFfJFi2rp9YjI2A_TJWWWqNF7u1Nfv0",
    authDomain: "petani-plasma.firebaseapp.com",
    databaseURL: "https://petani-plasma.firebaseio.com",
    projectId: "petani-plasma",
    storageBucket: "petani-plasma.appspot.com",
    messagingSenderId: "382317878771",
    appId: "1:382317878771:web:ae816b7ab4dd07e3214686",
    measurementId: "G-QNSQ75VVH8"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  firebase.analytics();
</script>

<!-- Authentication -->
<script type="text/javascript">
  // function admin() {
  //   window.alert('test');
  //   var user = firebase.auth().currentUser;
  //   user.updateProfile({
  //     displayName: 'Admin'
  //   }).then(function () {
  //     var displayName = user.displayName;
  //     window.alert(displayName);
  //   }, function (error) {
  //     window.alert(error);
  //   });
  // }

  function copy() {
    var db = firebase.firestore();
    db.collection("kapling").add({
      kode: "00004",
      nama_petani: "Abdul",
      no_kontak: "",
      kt: "32",
      kud: "003",
      kebun: "KLB",
      master: true
    }).then(function () {
      alert("SS");
    });
  }

  function logout() {
    firebase.auth().signOut().then(function() {
      // Sign-out successful.
      window.alert("Sign Out Success")
      window.location.assign("../login/index.php");
    }).catch(function(error) {
      // An error happened.
      var errorCode = error.code;
      var errorMessage = error.message;

      window.alert("Error : " + errorMessage);
    });
  }

  function initApp() {
    firebase.auth().onAuthStateChanged(function(user) {
      if (user) {
        // User is signed in.
        $("#spinnerContent").remove();
        $("#content").removeAttr('hidden');
      } else {
        // No user is signed in.
        window.location.assign("../login");
      }
    });
  }
</script>
