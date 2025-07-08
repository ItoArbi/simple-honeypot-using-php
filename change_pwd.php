<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
  }
?>

<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="form new_pwd">
      <header>Change Password</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Old Password</label>
          <input type="input" name="password" placeholder="Enter old password" required>
        </div>
        <div class="field input">
          <label>New Password</label>
          <input type="password" name="password_new" placeholder="Enter new password" required>
        <i class="fas fa-eye"></i>
        </div>
        <div class="field input">
          <label>Confirm Password</label>
          <input type="password" name="password1" placeholder="Confirm password" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Change">
        </div>
      </form>
    </section>
  </div>

  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/new_pwd.js"></script>

</body>
</html>
