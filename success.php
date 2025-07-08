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
    <h2 style="padding: 50px; align-items:center;">YOUR DATA IS SUCCESSFULLY CHANGED!</h2>
    <div style="padding: 50px; align-items:center;" class="link"><a href="users.php">Click here</a> to go to the main page!</div>
  </div>

  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/new_pwd.js"></script>

</body>
</html>
