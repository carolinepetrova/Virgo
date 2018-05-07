<?php
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res=mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
$title = "Съобщение";
include("sections/header.php");
if($userinfo['is_admin'] == 'yes') {
    if($userinfo['parent_id'] == "0") {
            $per_id = $_SESSION['user'];
            $needed_id = $userinfo['id'];
    }
    else {
            $per_id = $userinfo['parent_id'];
            $needed_id = $userinfo['parent_id'];
        }
    }
    else 
        {
            $per_id = $userinfo['parent_id'];
            $needed_id = $userinfo['parent_id'];
        }
?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head"> Съобщения
            <br> <span class="page-head-nav">Начало > Съобщения</span> </div>
        <div class="message-body col-md-12">
            <div class="col-lg-3 col-md-4 col-sm-4">
                <div class="message-left">
                    <ul>
                <?php
                    //show main admin if the profile is not his/hers
                    if($userinfo['is_admin'] == 'no' and $userinfo['parent_id'] != "0") {
                        if($userinfo['is_admin'] == 'yes') { 
                        $notxt = "notxt";
                        }
                        else {
                        $getadmin = mysqli_query($db_connect, "SELECT * FROM `users` WHERE id='$per_id'");
                         while($admin = mysqli_fetch_assoc($getadmin)){
                        ?>
                        
                        <a href="?id=<?php echo $admin['id'];?>">
                                <li><img src='uploads/<?php echo $admin['image'];?>'> <span class="name"><?php echo $admin['name'];?><br>
                                    <small class="text-muted"><?php echo $admin['duty'];?></small>
                                        </span> </li>
                            </a>
                        <?php }} }
                    //show all the users expect me
                    if($userinfo['is_admin'] == 'yes') {
                    $q = mysqli_query($db_connect, "SELECT * FROM `users` WHERE id!='$person_id' and parent_id='$per_id'");
                    }
                        else {
                            $q = mysqli_query($db_connect, "SELECT * FROM `users` WHERE id!='$person_id' and parent_id='$per_id'");
                        }
                    //display all the results
                    while($row = mysqli_fetch_assoc($q)){
                ?>
                            <a href='messages?id=<?php echo $row['id'];?>'>
                                <li><img src='uploads/<?php echo $row['image'];?>'> <span class="name"><?php echo $row['name'];?><br>
                                    <small class="text-muted"><?php echo $row['duty'];?></small>
                                        </span> </li>
                            </a>
                            <?php }?>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-8">
                <div class="message-right">
                    <!-- display message -->
                    <div id="chat" class="display-message">
                        <?php if(isset($_GET['id'])) {
                    $user_two = trim(mysqli_real_escape_string($db_connect, $_GET['id']));
                    //check $user_two is valid
                    $q = mysqli_query($db_connect, "SELECT `id` FROM `users` WHERE id='$user_two' AND id!='$person_id'");
                    //valid $user_two
                    if(mysqli_num_rows($q) == 1){
                        //check $user_id and $user_two has conversation or not if no start one
                        $conver = mysqli_query($db_connect, "SELECT * FROM `conversation` WHERE (user_one='$person_id' AND user_two='$user_two') OR (user_one='$user_two' AND user_two='$person_id')");
 
                        //they have a conversation
                        if(mysqli_num_rows($conver) == 1){
                            //fetch the converstaion id
                            $fetch = mysqli_fetch_assoc($conver);
                            $conversation_id = $fetch['id'];
                        }else{ //they do not have a conversation
                            //start a new converstaion and fetch its id
                            $q = mysqli_query($db_connect, "INSERT INTO `conversation` VALUES ('','$person_id',$user_two)");
                            $conversation_id = mysqli_insert_id($db_connect);
                        }
                    }else{
                        die("Invalid $_GET ID.");
                    }
                }else {
                    die("Кликнете върху човека, за да отворите чата");
                }
            ?>
                    </div>
                    <!-- /display message -->
                    <!-- send message -->
                    <div class="send-message">
                        <!-- store conversation_id, user_from, user_to so that we can send send this values to post_message_ajax.php -->
                        <input type="hidden" id="conversation_id" value="<?php echo $conversation_id; ?>">
                        <input type="hidden" id="user_form" value="<?php echo $person_id; ?>">
                        <input type="hidden" id="user_to" value="<?php echo $user_two; ?>">
                        <div class="form-group">
                            <textarea class="form-control" id="message" placeholder="Вашето съобщение"></textarea>
                        </div>
                        <button class="btn btn-primary" id="reply">Отговор</button> <span id="error"></span> </div>
                    <!-- / send message -->
                </div>
            </div>
        </div>
    </div>
    <?php
include("sections/footer.php");
?>
        <script>
            $(document).ready(function () {
                /*post message via ajax*/
                $("#reply").on("click", function () {
                    var message = $.trim($("#message").val())
                        , conversation_id = $.trim($("#conversation_id").val())
                        , user_form = $.trim($("#user_form").val())
                        , user_to = $.trim($("#user_to").val())
                        , error = $("#error");
                    if ((message != "") && (conversation_id != "") && (user_form != "") && (user_to != "")) {
                        error.text("Изпраща се ...");
                        $.post("post_message_ajax.php", {
                            message: message
                            , conversation_id: conversation_id
                            , user_form: user_form
                            , user_to: user_to
                        }, function (data) {
                            error.text(data);
                            //clear the message box
                            $("#message").val("");
                        });
                    }
                });
                //get message
                c_id = $("#conversation_id").val();
                if (c_id != null) {
                    var val_ue = c_id;
                };
                //get new message every 2 second
                setInterval(function () {
                    $(".display-message").load("get_message_ajax.php?c_id=" + c_id + "&val_ue=" + val_ue);
                }, 2000);
                setTimeout(function () {
                    $(".display-message").animate({
                        scrollTop: $($(".display-message")).height() + $($(".display-message")).height()
                    });
                }, 2000);
                var chatheight = $(document).height() - 350;
                $("#chat").attr('style', 'height:' + chatheight + 'px');
            });
        </script>