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
// secure other profiles not connected to the admin
$link =  "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$getname = basename($link);
$ids_array = array();
$result = mysqli_query($db_connect,"SELECT id FROM users WHERE parent_id='$per_id'");
while($row = mysqli_fetch_array($result))
{
    $ids_array[] = $row['id'];
}
include("sections/header.php");
$show_modal = false;
// get ids for the modals
if (isset($_GET['id'])) {
    $val = $_GET['id'];
}
if (isset($_GET['delete_id'])) {
    $del = $_GET['delete_id'];
}
if(isset($_POST['update'])) {
		// text fields
    $email = mysqli_real_escape_string($db_connect, $_POST['email']); 
    $name = mysqli_real_escape_string($db_connect, $_POST['name']); 
    $company2 = $userinfo['company']; 
    $duty = mysqli_real_escape_string($db_connect, $_POST['duty']);
    $isadmin = mysqli_real_escape_string($db_connect, $_POST['isadmin']);
    $file = rand(1000,100000)."-".$_FILES['image']['name'];
    $file_loc = $_FILES['image']['tmp_name'];
    $fileType = $_FILES["images"]["type"];
    $folder="uploads/";
    $imageFileType = pathinfo($file,PATHINFO_EXTENSION);
    move_uploaded_file($file_loc,$folder.$file);
    $emptyimage = $_FILES['image']['name'];
    if ($emptyimage == null) {
         mysqli_query($db_connect, "UPDATE `users` SET `email`='$email', `name`='$name', `company` = '$company2', `duty` = '$duty', `is_admin` = '$isadmin' WHERE `id`='$val'");

     }
    else 
    {   
        mysqli_query($db_connect, "UPDATE `users` SET `email`='$email', `password`='$pass_insert', `name`='$name', `company` = '$company2', `duty` = '$duty', `image` = '$file', `is_admin` = '$isadmin'  WHERE `id`='$val'");
        
        
    }
    hideGET();
	}

    if(isset($_POST['delete'])) {
        mysqli_query($db_connect, "DELETE FROM `users` WHERE `id`='$del'");
        hideGET();
	}
?>
    <div id="content2" class="content">
        <?php if ($userinfo['is_admin'] == 'yes') {?>
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Настройки на работниците 
            <br>
            <span class="page-head-nav">Начало > Работници > Настройки на работниците</span>
        </div>

        <div class="row">
            <?php
    $get_users = mysqli_query($db_connect, "SELECT * FROM users WHERE parent_id='$per_id'");
    while($users = mysqli_fetch_assoc($get_users)) {
            ?>
                <div class="col-md-6 col-sm-6">
                    <div class="workers widget-white ">
                        <div class="row">
                            <div class="col-sm-9">
                                <ul class="date-list-inline">
                                    <li class="worker-pic">
                                        <img src="<?php echo 'uploads/' . $users['image']; ?>">
                                    </li>
                                    <li class="widget-title">
                                        <p class="worker-name">
                                            <?php echo $users['name']; ?>
                                        </p>
                                    </li>
                                    <li class="widget-content worker-post">
                                        <p class="worker-prof">
                                            <?php echo $users['duty']; ?>
                                        </p>
                                    </li>

                                </ul>
                            </div>
                            <div class="col-sm-3 text-right" style="padding-top: 10px;">
                                <form action='' method="get">
                                    <button type="submit" id="edit" value="<?php echo $users['id'];?>" name="id" class="btn btn-success">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </button>
                                    <button type="submit" id="d" value="<?php echo $users['id'];?>" name="delete_id" class="btn btn-danger">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
        </div>
        <?php } else { include("sections/error.php");}?>
    </div>

    <?php
if (in_array("$val", $ids_array, true) or in_array("$del", $ids_array, true) or $getname == "workersettings") {
    if($_GET['id']) {?>
        <script type="text/javascript">
            window.onload = function() {
                $('#message').modal('show');
            }

        </script>
        <?php }?>
        <?php if($_GET['delete_id']) {?>
        <script type="text/javascript">
            window.onload = function() {
                $('#delete').modal('show');
            }

        </script>
        <?php }?>

        <div class="modal fade" id="message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <?php $personal_info = mysqli_query($db_connect, "SELECT * FROM users WHERE id='$val'");
			         while($per_inf = mysqli_fetch_assoc($personal_info)) {?>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">
                            Редактиране на
                            <?php  echo $per_inf['name']; ?>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Имейл</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $per_inf['email']; ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Име</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $per_inf['name']; ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Фирма</label>
                                    <input type="text" class="form-control" id="company" name="company" value="<?php echo $per_inf['company']; ?>" disabled="disabled">
                                </div>
                                <div class="col-sm-6">
                                    <label>Длъжност</label>
                                    <input type="text" class="form-control" id="duty" name="duty" value="<?php echo $per_inf['duty']; ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Снимка</label>
                                    <div class="input-group" style="margin: 10px 5px;">
                                        <span class="input-group-btn">
		                          <button id="fake-file-button-browse" type="button" class="btn btn-default">
			                             <i class="fa fa-upload" style="font-size: 14px; padding: 0; margin: 0;" aria-hidden="true"></i>
                                    </button>
                                    </span>
                                        <input type="file" id="image" name="image" style="display:none; margin: 0;" class="form-control"><input type="text" id="fake-file-input-name" style="margin: 0;" disabled="disabled" placeholder="Избери снимка" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label>Пълен достъп</label>
                                    <select class="form-control" id="isadmin" name="isadmin">
                              <?php if ($per_inf['is_admin'] == "no") {?> 
                                        <option selected="selected" value="no">Не</option>
                                        <option value="yes">Да</option>
                                    <?php } else {?>
                                        <option selected="selected" value="yes">Да</option>
                                        <option value="no">Не</option>
                                <?php }?>
                                </select>
                                </div>

                            </div>

                    </div>
                    <div class="modal-footer">
                        <span style="float:right;">
                                <button type="button" name="exit" id="exit" data-dismiss="modal" class="btn btn-login btn-red">Затвори</button> &nbsp;
                                <button type="submit" id="update" name="update" class="btn btn-login btn-green">Редактирай</button>
                    </span>
                        </form>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
        <div class="modal fade" id="delete" tabindex="-2" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <?php $delete_info = mysqli_query($db_connect, "SELECT * FROM users WHERE id='$del'");
			while($del_inf = mysqli_fetch_assoc($delete_info)) {?>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">
                            Изтриване на
                            <?php echo $del_inf['name'];?>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <span class="text-center">
                            Наистина ли искате да изтриете
                            <?php echo $del_inf['name'];?>
                            <form method="POST" action="" enctype="multipart/form-data">

                                <button type="button" style="float: none" name="exit" id="exit" data-dismiss="modal" class="btn btn-login btn-red">Не</button> &nbsp;
                                <button type="submit" style="float: none" id="delete" name="delete" class="btn btn-login btn-green">Да</button>

                            </form>
                        </span>
                        </div>

                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
        <?php } else {?>
        <script type="text/javascript">
            window.onload = function() {
                $('#warning').modal('show');
            }

        </script>
        <div class="modal fade" id="warning" tabindex="-2" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">
                            Предупреждение
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <span class="text-center">
                            Нямате права за достъп този профил!
                        </span>
                        </div>
                        <div class="modal-footer">
                            <span style="float:right;">
                                <button type="button" style="float: none" name="exit" id="exit" data-dismiss="modal" class="btn btn-info">Разбрах</button>
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        </div>
        <?php }?>
        <?php
include("sections/footer.php");
?>
