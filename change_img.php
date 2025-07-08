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
    <section class="form new_img">
      <header>Change Username</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field image">
          <label>Select Image</label>
          <input type="file" name="image" accept="image/png,image/gif,image/jpeg,image/jpg,image/JPG" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Change">
        </div>
      </form>
    </section>
  </div>

  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/new_img.js"></script>

</body>
</html>
