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
        <div class="content">
          <?php
            $id = $_GET['id'];
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = $id");
            if(mysqli_num_rows($sql) > 0){
              $row = mysqli_fetch_assoc($sql);
            }
          ?>
          <a style="padding-right: 18px;" href="talk.php?user_id=<?php echo $id ?>" class="back-icon"><i class="fas fa-arrow-left"></i></a>
          <a href="php/images/<?php echo $row['img']; ?>" width="100%" target="_blank"><img width="75%" src="php/images/<?php echo $row['img']; ?>" alt=""></a>
        <div class="details">
            <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
            <p style="font-size: small;"><?php echo $row['email']; ?></p>
        </div>
        </div>
        <div class="details">
            <p style="font-size:medium; padding: 0px 0px 0px 55px;"><?php echo $row['status']; ?></p>
        </div>
    </section>
  </div>

  <script src="javascript/users.js"></script>

</body>
</html>
