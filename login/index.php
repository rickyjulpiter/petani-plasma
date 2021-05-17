<!DOCTYPE html>
<html>
<?php include '../templates/header.php'; ?>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="index.php"><b>Go</b>Sawit</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Silahkan Login</p>
      <div class="input-group mb-3">
        <input type="email" class="form-control" placeholder="Email" id="Username_field">
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-envelope"></span>
          </div>
        </div>
      </div>
      <div class="input-group mb-3">
        <input type="password" class="form-control" placeholder="Password" id="Password_field">
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-lock"></span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-8"></div>
        <!-- /.col -->
        <div class="col-4">
          <button onclick="login()" type="submit" class="btn btn-primary btn-block">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<?php include '../templates/script.php'; ?>

<script type="text/javascript">
  function login() {
    var email = document.getElementById("Username_field").value;
    var password = document.getElementById("Password_field").value;

    firebase.auth().signInWithEmailAndPassword(email, password)
      .then(function(result) {
        console.log(result.user.uid);
        firebase.firestore().collection("admin").doc(result.user.uid).get().then((doc) => {
          console.log(doc.data());
          if(doc.exists) {
            window.location.assign("../dashboard/");
          } else {
            firebase.auth().signOut().then(function() {
              // Sign-out successful.
              window.alert("Kamu tidak memiliki akses");
              location.reload();
            }).catch(function(error) {
              // An error happened.
              var errorCode = error.code;
              var errorMessage = error.message;
              window.alert("Error : " + errorMessage);
            });
          }
        }).catch(function(error) {
          console.log("Error getting document:", error);
        });
      }).catch(function(error) {
      // Handle Errors here.
      var errorCode = error.code;
      var errorMessage = error.message;
      if (errorCode === 'auth/invalid-email'){
        window.alert("Invalid Email");
      }
      else if (errorCode === 'auth/wrong-password'){
        window.alert("Wrong Password");
      }
      else {
        window.alert(errorMessage);
      }
      // ...
    });
  }
</script>

</body>
</html>
