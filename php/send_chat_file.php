<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION["unique_id"];
    if(!isset($outgoing_id)){
        header("location: login.php");
    }
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);

    if(isset($_FILES['image'])){
        $img_name = $_FILES['image']['name'];
        $img_type = $_FILES['image']['type'];
        $tmp_name = $_FILES['image']['tmp_name'];
        
        $img_explode = explode('.',$img_name);
        $img_ext = end($img_explode);

        $extensions = ["jpeg", "png", "jpg"];
        if(in_array($img_ext, $extensions) === true){
            $types = ["image/jpeg", "image/jpg", "image/JPG", "image/png"];
            if(in_array($img_type, $types) === true){
                $time = time();
                $new_img_name = $time.$img_name;
                if(move_uploaded_file($tmp_name,"file/".$new_img_name)){
                    $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg,
                                                file_msg, main_user) VALUES ({$incoming_id}, {$outgoing_id},
                                                '{EMPTY}', '{$new_img_name}', {$outgoing_id})") or die();
                    $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg,
                                                file_msg, main_user) VALUES ({$incoming_id}, {$outgoing_id},
                                                '{EMPTY}', '{$new_img_name}', {$incoming_id})") or die();
                    if($sql){
                        header("location: ../talk.php?user_id=$incoming_id");
                    }else{
                        echo "Something went wrong. Please try again!";
                    }
                }
            }
        }else {
            $target_dir = "file/";
            $time = time();
            $file_name = $time . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg,
                                                file_msg, main_user) VALUES ({$incoming_id}, {$outgoing_id},
                                                '{EMPTY}', '{$target_file}', {$outgoing_id})") or die();
                $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg,
                                            file_msg, main_user) VALUES ({$incoming_id}, {$outgoing_id},
                                            '{EMPTY}', '{$target_file}', {$incoming_id})") or die();
                if($sql){
                    header("location: ../talk.php?user_id=$incoming_id");
                }else{
                    echo "Something went wrong. Please try again!";
                }

                // $file_name = $target_dir . basename($_FILES["image"]["name"]);
                // //Get file type and set it as Content Type
                // $finfo = finfo_open(FILEINFO_MIME_TYPE);
                // header('Content-Type: ' . finfo_file($finfo, $file_name));
                // finfo_close($finfo);

                // //Use Content-Disposition: attachment to specify the filename
                // header('Content-Disposition: attachment; filename='.basename($file_name));

                // //No cache
                // header('Expires: 0');
                // header('Cache-Control: must-revalidate');
                // header('Pragma: public');

                // //Define file size
                // header('Content-Length: ' . filesize($file_name));

                // ob_clean();
                // flush();
                // readfile($file_name);
                // echo finfo_file($finfo, $file_name);
                // exit;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }


            
        }
    }
?>