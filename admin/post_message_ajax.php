<?php
    require_once("inc/db_connect.php");
    //post message
    if(isset($_POST['message'])){
        $message = mysqli_real_escape_string($db_connect, $_POST['message']);
        $conversation_id = mysqli_real_escape_string($db_connect, $_POST['conversation_id']);
        $user_form = mysqli_real_escape_string($db_connect, $_POST['user_form']);
        $user_to = mysqli_real_escape_string($db_connect, $_POST['user_to']);
 
        //decrypt the conversation_id,user_from,user_to
        $conversationid = $conversation_id;
        $userform = $user_form;
        $userto = $user_to;
 
        //insert into `messages`
        $q = mysqli_query($db_connect, "INSERT INTO `messages` (`conversation_id`, `user_from`,`user_to`, `message`)     VALUES ('$conversationid','$userform','$userto','$message')");
        if($q){
            echo "Posted";
        }
        else
        {
            echo $userto;
        }
    }
?>