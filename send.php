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
    <section class="talk-area">
      <header>
        <?php 
          $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
          $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
          if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
          }else{
            header("location: users.php");
          }
        ?>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <a href="php/images/<?php echo $row['img']; ?>" width="100%" target="_blank"><img src="php/images/<?php echo $row['img']; ?>" alt=""></a>
        <div class="details">
          <a style="color:black;" href="user_info.php?id=<?php echo $row['unique_id']; ?>"><?php echo $row['fname']. " " . $row['lname'] ?></a>
          <p style="font-size: small;"><?php echo $row['status']; ?></p>
        </div>
        <div style="padding-left: 120px;" class="details">
          <a href="php/clear_talk.php?id=<?php echo $row['unique_id'] ?>" onclick="return  confirm('Do you want to delete Y/N')" class="logout">Clear Chat</a>
        </div>
        </header>
      
       <section class="form signup">
      <form action="php/send_chat_file.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
        <div class="error-text"></div>
        <div class="field image">
          <input type="file" name="image">
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Send File">
        </div>
      </form>
    </section>
    </section>
  </div>

  <!-- <script src="javascript/send_file.js"></script> -->

</body>
</html>
