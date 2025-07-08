<?php 
    session_start();
    function append_string($str1, $str2){
        $str1 .= $str2;
        return $str1;
    }
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $output = "";
        $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id} AND main_user = {$outgoing_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id} AND main_user = {$outgoing_id}) ORDER BY msg_id";
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                if($row["msg"] !== "{EMPTY}"){
                    if($outgoing_id === $row["main_user"]){
                        if($row['outgoing_msg_id'] === $outgoing_id){
                            
                            $output .= '<div class="talk outgoing">
                                        <div class="details">
                                            <p>'. $row['msg'] .'</p>
                                        </div>
                                        </div>';
                        }else{
                            $output .= '<div class="talk incoming">
                                        <img src="php/images/'.$row['img'].'" alt=""
                                        style="height:35px; width:35px;">
                                        <div class="details">
                                            <p>'. $row['msg'] .'</p>
                                        </div>
                                        </div>';
                        }
                    }else{
                        if($row['outgoing_msg_id'] === $outgoing_id){
                            
                            $output .= '<div class="talk incoming">
                                        <img src="php/images/'.$row['img'].'" alt=""
                                        style="height:35px; width:35px;">
                                        <div class="details">
                                            <p>'. $row['msg'] .'</p>
                                        </div>
                                        </div>';
                        }else{
                            $output .= '<div class="talk outgoing">
                                        <div class="details">
                                            <p>'. $row['msg'] .'</p>
                                        </div>
                                        </div>';
                        }
                    }
                } else {
                    $fileType = strtolower(pathinfo($row['file_msg'], PATHINFO_EXTENSION));
                    if ($fileType != "docx" && $fileType != "pdf") {
                        if($outgoing_id === $row["main_user"]){
                            if($row['outgoing_msg_id'] === $outgoing_id){

                                $output .= '<div class="talk outgoing">
                                            <div class="details">

                                                <a href="php/file/'.$row['file_msg'].'" width="100%" target="_blank"><p><img class="file_chat" src="php/file/'.$row['file_msg'].'" alt=""
                                                style="border-radius:0%; width:100%;"></p></a>
                                            </div>
                                            </div>';
                            }else{
                                $output .= '<div class="talk incoming">
                                            <img src="php/images/'.$row['img'].'" alt=""
                                            style="height:35px; width:35px;">
                                            <div class="details">
                                                
                                                <a href="php/file/'.$row['file_msg'].'" width="100%" target="_blank"><p><img class="file_chat" src="php/file/'.$row['file_msg'].'" alt=""
                                                style="border-radius:0%; width:100%;"></p></a>
                                            </div>
                                            </div>';
                            }
                        }else{
                            if($row['outgoing_msg_id'] === $outgoing_id){
                                
                                $output .= '<div class="talk incoming">
                                            <img src="php/images/'.$row['img'].'" alt=""
                                            style="height:35px; width:35px;">
                                            <div class="details">
                                                
                                                <a href="php/file/'.$row['file_msg'].'" width="100%" target="_blank"><p><img class="file_chat" src="php/file/'.$row['file_msg'].'" alt=""
                                                style="border-radius:0%; width:100%;"></p></a>
                                            </div>
                                            </div>';
                            }else{
                                $output .= '<div class="talk outgoing">
                                            <div class="details">
                                                
                                                <a href="php/file/'.$row['file_msg'].'" width="100%" target="_blank"><p><img class="file_chat" src="php/file/'.$row['file_msg'].'" alt=""
                                                style="border-radius:0%; width:100%;"></p></a>
                                            </div>
                                            </div>';
                            }
                        }
                    } else {
                        if($outgoing_id === $row["main_user"]){
                            if($row['outgoing_msg_id'] === $outgoing_id){

                                $output .= '<div class="talk outgoing">
                                            <div class="details">

                                                <a href="php/'.$row['file_msg'].'" width="100%" target="_blank"><p style="background-color:#afafaf;"><img src="php/images/doc.png" alt=""
                                            style="height:35px; width:35px;">'.$row['file_msg'].'</p></a>
                                            </div>
                                            </div>';
                            }else{
                                $output .= '<div class="talk incoming">
                                            <img src="php/images/'.$row['img'].'" alt=""
                                            style="height:35px; width:35px;">
                                            <div class="details">
                                                
                                                <a href="php/'.$row['file_msg'].'" width="100%" target="_blank"><p style="background-color:#afafaf;"><img src="php/images/doc.png" alt=""
                                            style="height:35px; width:35px;">'.$row['file_msg'].'</p></a>
                                            </div>
                                            </div>';
                            }
                        }else{
                            if($row['outgoing_msg_id'] === $outgoing_id){
                                
                                $output .= '<div class="talk incoming">
                                            <img src="php/images/'.$row['img'].'" alt=""
                                            style="height:35px; width:35px;">
                                            <div class="details">
                                                
                                                <a href="php/'.$row['file_msg'].'" width="100%" target="_blank"><p style="background-color:#afafaf;"><img src="php/images/doc.png" alt=""
                                            style="height:35px; width:35px;">'.$row['file_msg'].'</p></a>
                                            </div>
                                            </div>';
                            }else{
                                $output .= '<div class="talk outgoing">
                                            <div class="details">
                                                
                                                <a href="php/'.$row['file_msg'].'" width="100%" target="_blank"><p style="background-color:#afafaf;"><img src="php/images/doc.png" alt=""
                                            style="height:35px; width:35px;">'.$row['file_msg'].'</p></a>
                                            </div>
                                            </div>';
                            }
                        }
                    }
                }
            }
        }else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }
        echo $output;
    }else{
        header("location: ../login.php");
    }

?>