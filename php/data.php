<?php
    while($row = mysqli_fetch_assoc($query)){
        $sql3 = "SELECT * FROM users WHERE unique_id = {$row['incoming_id']} ORDER BY user_id DESC";
        $query3 = mysqli_query($conn, $sql3);
        $row3 = mysqli_fetch_assoc($query3);

        $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['incoming_id']}
                OR outgoing_msg_id = {$row['incoming_id']}) AND (outgoing_msg_id = {$outgoing_id} 
                OR incoming_msg_id = {$outgoing_id}) AND main_user=$outgoing_id ORDER BY msg_id DESC LIMIT 1";
        $query2 = mysqli_query($conn, $sql2);
        $row2 = mysqli_fetch_assoc($query2);
        if(mysqli_num_rows($query2) > 0) {
            if($row2["msg"] === "{EMPTY}"){
                $result =$row2['file_msg'];
            } else {
                $result = $row2['msg'];
            }
        } else {
            $result ="No message available";
        }
        (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;
        if(isset($row2['outgoing_msg_id'] )){
            ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "You: " : $you = "";
        }else{
            $you = "";
        }
        ($row3['status'] == "Offline now") ? $offline = "offline" : $offline = "";
        ($outgoing_id == $row3['unique_id']) ? $hid_me = "hide" : $hid_me = "";

        $output .= '<a href="talk.php?user_id='. $row3['unique_id'] .'">
                    <div class="content">
                    <img src="php/images/'. $row3['img'] .'" alt="">
                    <div class="details">
                        <span>'. $row3['fname']. " " . $row3['lname'] .'</span>
                        <p>'. $you . $msg .'</p>
                    </div>
                    </div>
                    <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                </a>';
    }
?>