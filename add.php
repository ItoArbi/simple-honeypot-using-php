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
          <a style="padding-right: 20px;" href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
          <a href="php/images/<?php echo $row['img']; ?>" width="100%" target="_blank"><img src="php/images/<?php echo $row['img']; ?>" alt=""></a>
          <div class="details">
            <span><a style="color:black;" href="account.php"><?php echo $row['fname']. " " . $row['lname'] ?></a></span>
            <p><?php echo $row['status']; ?></p>
          </div>
        </div>
        <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">Logout</a>
      </header>
      
        <section class="form add_contact">
        <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="error-text"></div>
            <div class="field input">
            <label>Contact Email</label>
            <input type="input" name="contact-email" placeholder="Enter your new contact email" required>
            </div>
            <div class="field button">
            <input type="submit" name="submit" value="Add">
            </div>
        </form>
        </section>
    </section>
  </div>

  <script src="javascript/new_contact.js"></script>

</body>
</html>
