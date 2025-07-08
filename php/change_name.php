<?php
    session_start();
    include_once "config.php";
    $id = $_SESSION["unique_id"];
    if(!isset($id)){
        header("location: login.php");
    }

    $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = $id");
    if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);
    }
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    if(!empty($fname) && !empty($lname)){
        $insert_query = mysqli_query($conn, "UPDATE users SET fname = '$fname', lname = '$lname' WHERE unique_id = '$id'");
        if($insert_query){
            echo "success";
        }else{
            echo "Something went wrong. Please try again!";
        }
    }else{
        echo "All input fields are required!";
    }
    
?>