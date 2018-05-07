<?php
$title = "Всички работници";
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
?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Всички работници
            <br>
            <span class="page-head-nav">Начало > Работници > Всички работници</span>
        </div>
        <div class="row" style="margin:15px;">
            <?php
            if($userinfo['is_admin']=='yes') {
            $get_users = mysqli_query($db_connect, "SELECT * FROM users WHERE parent_id='$per_id'");
            }
            else {
                $getid = $userinfo['parent_id'];
                $get_users = mysqli_query($db_connect, "SELECT * FROM users WHERE parent_id='$getid'");
            }
			while($users = mysqli_fetch_assoc($get_users)) {
			?>
                <div class="widget widget-white col-md-3">
                    <center><img class="img-responsive" width="150" style="border-radius: 50%; object-fit: cover; height: 150px;" src="uploads/<?php echo $users['image'];?>"></center>
                    <div class="profile-info">
                        <h3>
                            <?php echo $users['name'];?>
                        </h3>
                        <h4><b><?php echo $users['duty'];?></b></h5>
                            <br>
                            <h5><i class="fa fa-building" style="font-size:14px" aria-hidden="true"></i>
                                <?php echo $users['company'];?>
                            </h5>
                            <h5><i class="fa fa-envelope" style="font-size:14px" aria-hidden="true"></i>
                                <?php echo $users['email'];?>
                            </h5>
                            <?php if($users['phone'] != null) { ?>
                            <h5><i class="fa fa-phone" style="font-size:14px" aria-hidden="true"></i>
                                <?php echo $users['phone'];?>
                            </h5>
                            <?php }?>
                            <?php if($users['adress'] != null) { ?>
                            <h5><i class="fa fa-map-marker" style="font-size:14px" aria-hidden="true"></i>
                                <?php echo $users['adress'];?>
                            </h5>
                            <?php }?>

                    </div>
                </div>

                <?php }?>

        </div>

    </div>

    <?php
include("sections/footer.php");
?>
