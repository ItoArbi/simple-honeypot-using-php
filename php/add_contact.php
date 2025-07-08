<?php
    session_start();
    include_once "config.php";
    $id = $_SESSION["unique_id"];
    if(!isset($id)){
        header("location: login.php");
    }

    $sql2 = mysqli_query($conn, "SELECT * FROM friend WHERE outgoing_id = '{$id}'");
    if(mysqli_num_rows($sql2) > 0){
        $row2 = mysqli_fetch_assoc($sql2);
    }
    $contact = mysqli_real_escape_string($conn, $_POST['contact-email']);
    if(!empty($contact)){
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$contact}'");
        if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
            if($row["unique_id"] !== $row2["incoming_id"]){
                if($row["unique_id"] !== $row2["outgoing_id"]){
                    $insert_query = mysqli_query($conn, "INSERT INTO friend
                                                        (incoming_id, outgoing_id) VALUES
                                                        ({$row['unique_id']},{$id})");
                    if($insert_query){
                        echo "success";
                    }else{
                        echo "Something went wrong. Please try again!";
                    }
                }else{
                    echo "You cannot choose yourself!";
                }
            }else{
                echo "$contact - Already in your contact list!";
            }
        }else{
            echo "$contact - This email not Exist!";
        }
    }else{
        echo "All input fields are required!";
    }
    
?>