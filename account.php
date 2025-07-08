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
    <section class="users">
      <header>
        <div class="content">
          <?php 
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
            if(mysqli_num_rows($sql) > 0){
              $row = mysqli_fetch_assoc($sql);
            }
          ?>
          <a style="padding-right: 18px;" href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
          <a href="php/images/<?php echo $row['img']; ?>" width="100%" target="_blank"><img src="php/images/<?php echo $row['img']; ?>" alt=""></a>
        <div class="details">
            <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
            <p style="font-size: small;"><?php echo $row['email']; ?></p>
            </div>
            </div>
        <div class="status-dot"><i class="fas fa-circle"></i></div>
        <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">Logout</a>
      </header>
      <div class="details">
        <br>
        <center><div class="link"><a href="change_pwd.php">Change Password</a></div></center>
      </div>
      <div class="details">
        <center><div class="link"><a href="change_name.php">Change Username</a></div></center>
      </div>
      <div class="details">
        <center><div class="link"><a href="change_img.php">Change Photo Profile</a></div></center>
      </div>
      <br>
      <div class="details">
        <center><div class="link">
          <a onclick="return  confirm('Do you want to delete Y/N')" 
          href="php/delete.php">
          Delete Account
        </a></div></center>
      </div>
    </section>
  </div>

  <script src="javascript/users.js"></script>

</body>
</html>
