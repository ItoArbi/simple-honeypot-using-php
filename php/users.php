<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $sql = "SELECT * FROM friend WHERE outgoing_id = {$outgoing_id} ORDER BY id DESC";
    $query = mysqli_query($conn, $sql);

    $output = "";
    if(mysqli_num_rows($query) == 0){
        $output .= "No users are available to talk";
    }elseif(mysqli_num_rows($query) > 0){
        include_once "data.php";
    }
    echo $output;
?>