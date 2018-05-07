<?php
$title = "Нова задача";
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
if(isset($_POST['submit'])) {
        $name = mysqli_real_escape_string($db_connect, $_POST['name']); 
        $for_user = mysqli_real_escape_string($db_connect, $_POST['taskoption']); 
        $from = date('Y-m-d', strtotime($_POST['from']));
        $to  = date('Y-m-d', strtotime($_POST['to']));
        $descr = mysqli_real_escape_string($db_connect, $_POST['descr']); 
            if($name == "null") {
                $error= "Моля въведете име";
            }    
        else {
            $ins = mysqli_query($db_connect, "INSERT INTO `tasks` (`name`, `from_date` , `to_date`,`added_by` , `description`, `parent_id` , `for_user`)     VALUES ('$name','$from', '$to','$per_id','$descr', '$per_id','$for_user')");
            if($for_user == "0") {
                $userid = "0";
                $for_admin = $per_id;
                $text = "Добавена е нова задача в Свободни задачи, разгледайте я.";
                $icon = "fa fa-tasks";
            }
            else {
                $userid = $for_user;
                $for_admin = "0";
                $text = "Добавена е нова задача ".$name;
                $icon = "fa fa-thumb-tack";
            }
            mysqli_query($db_connect, "INSERT INTO `notifications` (`icon`, `text`, `parent_id` , `for_user`)     VALUES ('$icon','$text','$per_id', '$userid')");
            if (!$ins)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
            $success = "updated";
        }
        }
?>
    <div id="content2" class="content">
        <?php if ($userinfo['is_admin'] == 'yes') {?>
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Нова задача
            <br>
            <span class="page-head-nav">Начало > Задачи > Нова задача</span>
        </div>
        <div class="row" style="margin:15px;">
            <div class="widget widget-white col-md-10 col-md-offset-1">
                <?php if($success == "updated") {?>
                <script>
                    alertify.success('Успешно добавихте нова задача');
                </script>
                <?php } if($error != NULL) {?>
                <script>
                    alertify.error('<?php echo $error;?>');
                </script>
                <?php } ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="col-sm-6">
                            <label>Име</label>
                            <input type="text" class="form-control" name="name" placeholder="Име">
                        </div>
                        <div class="col-sm-6">
                            <label>Изберете изпълнител</label>
                            <select class="form-control" name="taskoption">
                                    <option value="0">По избор</option>
                                    <?php if($userinfo['parent_id'] == "0") {?>
                                    <option value="<?php echo $userinfo['id'];?>"><?php echo $userinfo['name'];?></option>
                                    <?php }?>
                                    <?php
                                    $get_users = mysqli_query($db_connect, "SELECT * FROM users WHERE parent_id='$per_id'");
                                    while($users = mysqli_fetch_assoc($get_users)) {
                                    ?>
                                    <option value="<?php echo $users['id'];?>"><?php echo $users['name'];?></option>
                                    
                                    <?php }?>
                                </select>
                        </div>
                        <div class="col-sm-6">
                            <label>Начална дата</label>
                            <input type="date" class="form-control" name="from" placeholder="Дата">
                        </div>
                        <div class="col-sm-6">
                            <label>Крайна дата</label>
                            <input type="date" class="form-control" name="to" placeholder="Дата">
                        </div>
                        <div class="col-sm-12">
                            <label>Описание</label>
                            <textarea class="form-control" name="descr" rows="4"></textarea>
                        </div>
                        <span style="float:right;">
                        <button type="submit" name="submit" class="btn btn-login btn-green">Добави</button>
                    </span>
                    </form>
            </div>
        </div>
    </div>
    <?php } else { include("sections/error.php"); } ?>
    <?php
include("sections/footer.php");
?>
