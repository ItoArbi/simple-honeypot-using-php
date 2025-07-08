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

    $id1 = $_GET["id"];

    $insert_query = mysqli_query($conn, "DELETE FROM messages WHERE
    (incoming_msg_id= $id1 AND outgoing_msg_id=$id AND main_user=$id) OR
    (incoming_msg_id= $id AND outgoing_msg_id=$id1 AND main_user=$id) ");

    if($insert_query){
        header("Location: ../talk.php?user_id=$id1");
    }else{
        echo "Something went wrong. Please try again!";
    }
?>