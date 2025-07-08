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
    $password0 = mysqli_real_escape_string($conn, $_POST['password']);
    $password1 = mysqli_real_escape_string($conn, $_POST['password_new']);
    $password2 = mysqli_real_escape_string($conn, $_POST['password1']);
    if(!empty($password0) && !empty($password1) && !empty($password2)){
        if(md5($password0) === $row["password"]) {
            if(($password0 !== $password1) or ($password0 !== $password2)) {
                if($password1 === $password2){
                    $encrypt_pass = md5($password1);
                    $insert_query = mysqli_query($conn, "UPDATE users SET password = '$encrypt_pass' WHERE unique_id = '$id'");
                    if($insert_query){
                        echo "success";
                    }else{
                        echo "Something went wrong. Please try again!";
                    }
                }else{
                    echo "The new password is not equal with confirm password!";
                }
            }else{
                echo "The old and the new password is equal!";
            }
        }else{
            echo "The old password is incorrect!";
        }
    }else{
        echo "All input fields are required!";
    }
    
?>