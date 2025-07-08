<?php
    session_start();
    include_once "config.php";
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $id = $_SESSION["unique_id"];
    if(!isset($id)){
        header("location: login.php");
    }

    $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = $id");
    if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);
    }

    $insert_query = mysqli_query($conn, "DELETE FROM users WHERE unique_id = {$row['unique_id']}");
    if($insert_query){
        session_unset();
        session_destroy();
        header("location: ../login.php");
    }
?>