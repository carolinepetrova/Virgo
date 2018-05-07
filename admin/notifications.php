<?php
$title = "Известия";
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res= mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
if($userinfo['is_admin'] == 'yes') {
    if($userinfo['parent_id'] == "0") {
            $per_id = $_SESSION['user'];
    }
    else {
            $per_id = $userinfo['parent_id'];
        }
    }
    else 
        {
            $per_id = $userinfo['parent_id'];
        }
include("sections/header.php");
if(isset($_POST['updatemsg'])) { 
foreach ($_POST['checkbox'] as $value) {
    mysqli_query($db_connect, "UPDATE notifications SET seen = 'yes' WHERE id = $value");
}
}
if(isset($_GET['deleteID'])) {
    $delVal = $_GET['deleteID'];
    mysqli_query($db_connect, "DELETE FROM `notifications` WHERE `id`='$delVal'");
    hideGET();
}
?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head"> Известия
            <br> <span class="page-head-nav">Начало > Профил > Известия</span> </div>
        <div class="row" style="margin:15px;">
            <div class="widget widget-white col-md-12">
                <div class="col-xs-12">
                    <form method="post"> <span class="pull-right">
                            <button type="submit" name="updatemsg" class="read-all">
                                Маркирай като прочетени
                            </button>
                        </span> </div>
                <div id="notificationsBox" class="col-md-12" style="overflow-y: scroll;">
                    <ul class="all_notif">
                        <?php
                            $get_notif = mysqli_query($db_connect, "SELECT * FROM notifications WHERE parent_id='$per_id' and all_users='yes' or for_user ='$person_id' ORDER BY id DESC");
			                 while($notif = mysqli_fetch_assoc($get_notif)) {
			             ?>
                            <li <?php if($notif[ 'seen']=="no" ) {?> style="background:#F0F0F0;"
                                <?php } ?>>
                                    <div class="row">
                                        <div class="pull-left"> <span class="pull-left">
                                        <i class="<?php echo $notif['icon'];?> fa-2x text-info hidden-xs"></i>
                                        </span> <span class="notif-text">
                                                <?php echo $notif['text'];?>
                                                <br>
                                                <small class="text-muted" data-livestamp="<?php echo $notif['date'];?>"></small>    </span> </div>
                                        <div class="pull-right"> <span class="pull-right">   
                                            <div class="checkbox">
                                            <input name="checkbox[]" value="<?php echo $notif['id'];?>" id="<?php echo $notif['id'];?>" type="checkbox" <?php if($notif[ 'seen']=='yes' ) {?> checked="checked" disabled="disabled"<?php }?>>
                                            </div>
                        </form>
                        <form class="notif-btn" method="get">
                            <button title="Изтрии известие" type="submit" value="<?php echo $notif['id']?>" name="deleteID" class="del-btn"><i class="fa fa-times" aria-hidden="true"></i></button>
                        </form>
                                        </span> </div>
                                    </div>
                            </li>
                            <?php }?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
include("sections/footer.php");
?>