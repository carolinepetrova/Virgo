<?php
$title = "Настройки на задачите";
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res= mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
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

// secure other profiles not connected to the admin
$link =  "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$getname = basename($link);
$ids_array = array();
$result = mysqli_query($db_connect,"SELECT id FROM tasks WHERE parent_id='$needed_id'");
while($row = mysqli_fetch_array($result))
{
    $ids_array[] = $row['id'];
}
$show_modal = false;
// get ids for the modals
if (isset($_GET['id'])) {
    $val = $_GET['id'];
}
if (isset($_GET['addtoID'])) {
    $taskid = $_GET['addtoID'];
}
if (isset($_GET['deleteID'])) {
    $delid = $_GET['deleteID'];
}
if(isset($_POST['update'])) {
    $taskname = mysqli_real_escape_string($db_connect, $_POST['taskname']); 
    $foruser = mysqli_real_escape_string($db_connect, $_POST['foruser']); 
    $status = mysqli_real_escape_string($db_connect, $_POST['status']); 
    $date_to  = date('Y/m/d', strtotime($_POST['date_to']));
    $descr = mysqli_real_escape_string($db_connect, $_POST['descr']);
    $notes = mysqli_real_escape_string($db_connect, $_POST['notes']); 
    $upd = mysqli_query($db_connect, "UPDATE `tasks` SET `name`='$taskname', `to_date`='$date_to', `description`='$descr', `for_user` = '$foruser',`active` = '$status',`notes` = '$notes' WHERE id='$val'");
    if (!$upd)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
    else {
            $success = "updated";
    }
    hideGET();
	}
if(isset($_POST['submit'])) {
    $podtaskname = mysqli_real_escape_string($db_connect, $_POST['podtaskname']); 
    $ins = mysqli_query($db_connect, "INSERT INTO `podtasks` SET `name`='$podtaskname', `for_task`='$taskid', `parent_id`='$per_id'");
    if (!$ins)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
    else {
            $success2 = "updated";
    }
	}
    if(isset($_POST['delete'])) {
       $delet = mysqli_query($db_connect, "DELETE FROM `tasks` WHERE `id`='$delid'");
         mysqli_query($db_connect, "DELETE FROM `podtasks` WHERE `for_task`='$delid'");
       if (!$delet)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
    else {
            $success3 = "updated";

    } 
        hideGET();     
	}
?>

    <div id="content2" class="content">
        <?php if ($userinfo['is_admin'] == 'yes') {?>
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Настройки на задачите
            <?php echo $get_added;?>
            <br>
            <span class="page-head-nav">Начало > Задачи > Настройки на задачите</span>
        </div>
        <div class="row" style="margin:15px;">
            <?php if($success == "updated") {?>
            <script>
                alertify.success('Успешно обновихте задачата');

            </script>
            <?php } if($success2 == "updated") {?>
            <script>
                alertify.success('Успешно добавихте подзадача');

            </script>
            <?php } if($success3 == "updated") {?>
            <script>
                alertify.success('Успешно изтрихте задачата');

            </script>
            <?php } if($error != NULL) {?>
            <script>
                alertify.error('<?php echo $error;?>');

            </script>
            <?php } ?>
            <div class="col-md-12">
                <div class="widget widget-white">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Име</th>
                                <th>Изпълнител</th>
                                <th>Статус</th>
                                <th>До</th>
                                <th>Добавил</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                                            $get_tasks = mysqli_query($db_connect, "SELECT * FROM tasks WHERE parent_id='$per_id'");
                                            while($tasks = mysqli_fetch_assoc($get_tasks)) {
                                            ?>
                                <tr>

                                    <td>
                                        <?php echo $tasks['name'];?>
                                    </td>
                                    <td>
                                        <?php
                                        $get_userid = $tasks['for_user'];
                                                $getusrs = mysqli_query($db_connect, "SELECT * FROM users WHERE id='$get_userid'");
                                                $forusr=mysqli_fetch_assoc($getusrs);
                                                if($tasks['for_user'] == "0") {
                                                    echo "По избор";
                                                }
                                                {
                                                    echo $forusr['name'];
                                                }
                                                ?>
                                    </td>
                                    <?php if($tasks['active'] == 'yes') {?>
                                    <td>Активна</td>
                                    <?php } else {?>
                                    <td>Неактивна</td>
                                    <?php }?>
                                    <td>
                                        <?php echo $tasks['to_date'];?>
                                    </td>
                                    <td>
                                        <?php 
                                                $get_added = $tasks['added_by'];
                                                $getuser = mysqli_query($db_connect, "SELECT * FROM users WHERE id='$get_added'");
                                                $user_name=mysqli_fetch_assoc($getuser);
                                                echo $user_name['name'];
                                        ?>
                                    </td>
                                    <form action='' method="get">
                                        <td><button type="submit" title="Редактирай" value="<?php echo $tasks['id']?>" name="id" style="border: 0px; background: transparent;"><i class="fa fa-pencil" aria-hidden="true"></i></button><button title="Добави подзадачи" type="submit" value="<?php echo $tasks['id']?>" name="addtoID" style="border: 0px; background: transparent;"><i class="fa fa-plus" aria-hidden="true"></i></button><button title="Добави подзадачи" type="submit" value="<?php echo $tasks['id']?>" name="deleteID" style="border: 0px; background: transparent;"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                                    </form>
                                </tr>
                                <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <?php
if (in_array("$val", $ids_array, true) or in_array("$taskid", $ids_array, true) or in_array("$delid", $ids_array, true) or $getname == "tasksettings") {
    if($_GET['id']) {?>
        <script type="text/javascript">
            window.onload = function() {
                $('#edittask').modal('show');
            }

        </script>
        <?php }?>
        <?php if($_GET['addtoID']) {?>
        <script type="text/javascript">
            window.onload = function() {
                $('#addpodtasks').modal('show');
            }

        </script>
        <?php }?>
        <?php if($_GET['deleteID']) {?>
        <script type="text/javascript">
            window.onload = function() {
                $('#deletetask').modal('show');
            }

        </script>
        <?php }?>
        <div class="modal fade" id="edittask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <?php $edit_task = mysqli_query($db_connect, "SELECT * FROM tasks WHERE id='$val'");
			while($taskinfo = mysqli_fetch_assoc($edit_task)) {?>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Редактиране на задача
                            <?php echo $taskinfo['name'];?>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form method="post">
                                <div class="col-sm-6">
                                    <label>Име</label>
                                    <input type="text" name="taskname" class="form-control" value="<?php echo $taskinfo['name'];?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Изпълнител</label>
                                    <select class="form-control" id="foruser" name="foruser">
                                        <?php if($userinfo['parent_id'] == 0 and $userinfo['id'] == "$person_id") {?>
                                        <option value="<?php echo $userinfo['id'];?>"><?php echo $userinfo['name'];?></option>
                                        <?php } elseif($userinfo['parent_id'] != 0) {
                                        $get_parent_id = $userinfo['parent_id'];
                                        $getadmin = mysqli_query($db_connect, "SELECT * FROM users WHERE id='$get_parent_id'");
                                        $admin=mysqli_fetch_assoc($getadmin);
                                        ?>
                                        <option value="<?php echo $admin['id'];?>"><?php echo $admin['name'];?></option>
                                        <?php 
                                        }
                                        $option = mysqli_query($db_connect, "SELECT * FROM  `users` WHERE parent_id='$per_id'");
                                        while($opt = mysqli_fetch_assoc($option)) {
                                            if($taskinfo['for_user'] == $opt['id']) { ?> 
                                        <option selected="selected" value="<?php echo $opt["id"];?>"><?php echo $opt["name"];?></option>
                                        <?php } else { ?> 
                                        <option value="<?php echo $opt['id'];?>"><?php echo $opt['name'];?></option>
                                        <?php }}?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label>До</label>
                                    <input type="date" class="form-control" name="date_to" value="<?php echo $taskinfo['to_date'];?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Статус</label>
                                    <select class="form-control" id="status" name="status">
                              <?php if ($taskinfo['active'] == "no") {?> 
                                        <option selected="selected" value="no">Неактивна</option>
                                        <option value="yes">Активна</option>
                                    <?php } else {?>
                                        <option selected="selected" value="yes">Активна</option>
                                        <option value="no">Неактивна</option>
                                <?php }?>
                                </select>
                                </div>
                                <div class="col-sm-12">
                                    <label>Описание</label>
                                    <textarea class="form-control" name="descr"><?php echo $taskinfo['description'];?></textarea>
                                </div>
                                <div class="col-sm-12">
                                    <label>Бележки</label>
                                    <textarea class="form-control" name="notes"><?php echo $taskinfo['notes'];?></textarea>
                                </div>
                        </div>
                    </div>
                    <?php }?>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal">Затвори</button>
                        <button type="submit" name="update" class="btn btn-green">Запази</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addpodtasks" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Добавяне на подзадача</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <label>Име на подзадачата</label>
                            <input type="text" name="podtaskname" class="form-control">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Затвори</button>
                        <button type="submit" name="submit" class="btn btn-primary">Запази</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deletetask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <?php $delete_info = mysqli_query($db_connect, "SELECT * FROM tasks WHERE id='$delid'");
			while($del_inf = mysqli_fetch_assoc($delete_info)) {?>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Изтриване на задача</h4>
                    </div>
                    <div class="modal-body">
                        <span class="text-center">
                            Наистина ли искате да изтриете
                            <?php echo $del_inf['name'];?>
                            <form method="POST" action="" enctype="multipart/form-data">

                                <button type="button" style="float: none" name="exit" id="exit" data-dismiss="modal" class="btn btn-login btn-red">Не</button> &nbsp;
                                <button type="submit" style="float: none" id="delete" name="delete" class="btn btn-login btn-green">Да</button>

                            </form>
                        </span>
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
        <?php } else { include("sections/error.php"); } ?>
        <?php
include("sections/footer.php");
?>
