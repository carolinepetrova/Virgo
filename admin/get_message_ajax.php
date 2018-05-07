<?php
    include("inc/functions.php");
    require_once("inc/db_connect.php");
    $person_id = $_SESSION['user'];
    if(isset($_GET['c_id'])) {
        //get the conversation id and
        $conversation_id = $_GET['c_id'];
        echo $val;
        //fetch all the messages of $user_id(loggedin user) and $user_two from their conversation
        $q = mysqli_query($db_connect, "SELECT * FROM `messages` WHERE conversation_id='$conversation_id'");
        //check their are any messages
        if(mysqli_num_rows($q) > 0){
            while ($m = mysqli_fetch_assoc($q)) {
                //format the message and display it to the user
                $user_form = $m['user_from'];
                $user_to = $m['user_to'];
                $message = $m['message'];
                $date_added = $m['time_added'];
 
                //get name and image of $user_form from `user` table
                $user = mysqli_query($db_connect, "SELECT name,image FROM `users` WHERE id='$user_form'");
                $user_fetch = mysqli_fetch_assoc($user);
                $user_form_username = $user_fetch['name'];
                $user_form_img = $user_fetch['image'];
                
                //display the message
                echo "<div class='message'"; 
                if($m['seen'] == 'no')
                { 
                                echo "style='background:#f6f6f6;'";
                 };
                               echo "> <div class='img-con'>
                                    <img src='uploads/{$user_form_img}'>
                                </div>
                                <div class='text-con'>
                                    <a href='#''>{$user_form_username}</a>
                                    <br><small class='text-muted'>{$date_added}</small>
                                    <p>
                                    {$message}
                                    </p>
                                </div>
                            </div>
                            
                            ";
 
            }
           
        }else{
            echo "Няма съобщения";
        }

    }


 if(isset($_GET['val_ue'])) {
     $val = $_GET['val_ue'];
     if($person_id == $user_to) {
     if($val != "0") {
         $ids_array = array();
                $result = mysqli_query($db_connect,"SELECT id FROM messages WHERE conversation_id='$conversation_id'");
                while($row = mysqli_fetch_array($result))
                {
                $ids_array[] = $row['id'];
                }
                        foreach ($ids_array as $value) {
                                mysqli_query($db_connect, "UPDATE `messages` SET `seen`='yes' WHERE `id`='$value'");
                } 
     }
     
 }
 }
?>
