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
    if(isset($_FILES['image'])){
        $img_name = $_FILES['image']['name'];
        $img_type = $_FILES['image']['type'];
        $tmp_name = $_FILES['image']['tmp_name'];
        
        $img_explode = explode('.',$img_name);
        $img_ext = end($img_explode);

        $extensions = ["jpeg", "png", "jpg","JPG"];
        if(in_array($img_ext, $extensions) === true){
            $types = ["image/jpeg", "image/jpg", "image/JPG", "image/png"];
            if(in_array($img_type, $types) === true){
                $time = time();
                $new_img_name = $time.$img_name;
                if(move_uploaded_file($tmp_name,"images/".$new_img_name)){
                    $insert_query = mysqli_query($conn, "UPDATE users SET img='$new_img_name' WHERE unique_id = '$id'");
                    if($insert_query){
                        echo "success";
                    }else{
                        echo "This email address not Exist!";
                    }
                }else{
                    echo "Something went wrong. Please try again!";
                }
            }
        }else{
            echo "Please upload an image file - jpeg, png, jpg";
        }
    }else{
        echo "Please upload an image file - jpeg, png, jpg";
    }
    
?>