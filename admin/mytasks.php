<?php
$title = "Моите задачи";
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
include("sections/header.php");
$get_tasks = mysqli_query($db_connect, "SELECT * FROM tasks WHERE for_user='$person_id'");
$tasks = mysqli_fetch_assoc($get_tasks);
if(isset($_POST['update'])) {
        $newid = mysqli_real_escape_string($db_connect, $_POST['getid']); 
        $ins =mysqli_query($db_connect, "UPDATE `tasks` SET `for_user`='$person_id'  WHERE `id`='$newid'");
            if (!$ins)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
    else {
            $success = "updated";
    }
        }
$coun = mysqli_query($db_connect,"SELECT COUNT(*) as con2 FROM tasks where for_user='$person_id' and active='no'");
$num = mysqli_fetch_assoc($coun);
?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head"> Моите задачи
            <br> <span class="page-head-nav">Начало > Задачи > Моите задачи </span> </div>
        <div class="row" style="margin:15px;">
            <?php if($success == "updated") {?>
            <script>
                alertify.success('Успешно добавихте задачата в "Мои задачи"');

            </script>
            <?php } if($error != NULL) {?>
            <script>
                alertify.error('<?php echo $error;?>');

            </script>
            <?php } ?>
            <div class="col-md-6">
                <div class="widget widget-white">
                    <div class="row">
                        <h2>Моите задачи</h2>
                        <div>
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Незавършени</a></li>
                                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Завършени</a></li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="home">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Име</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $get_users = mysqli_query($db_connect, "SELECT * FROM tasks WHERE for_user='$person_id' and active='yes'");
                                            while($users = mysqli_fetch_assoc($get_users)) {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $users['name'];?>
                                                    </td>
                                                    <td><a href="viewtask?id=<?php echo $users['id'];?>"><i class="fa fa-search" aria-hidden="true"></i></a></td>
                                                </tr>
                                                <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="profile">
                                    <?php if($num['con2'] != 0) {?>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Име</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                $get_users = mysqli_query($db_connect, "SELECT * FROM tasks WHERE for_user='$person_id' and active='no'");
                                while($users = mysqli_fetch_assoc($get_users)) {
                                    ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $users['name'];?>
                                                    </td>
                                                </tr>
                                                <?php }?>
                                        </tbody>
                                    </table>
                                    <?php } else {?> Няма завършени задачи
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="widget widget-white">
                    <div class="row">
                        <h2>Свободни задачи</h2>
                        <?php
                        $get_freetasks = mysqli_query($db_connect, "SELECT * FROM tasks WHERE for_user='0' and parent_id='$per_id' and active='yes'");
                        while($free_tasks = mysqli_fetch_assoc($get_freetasks)) {
                        ?>
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingTwo">
                                        <div class="panel-title"> <span class="pull-left"><?php echo $free_tasks['name'];?></span> <span class="pull-right">
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#<?php echo $free_tasks['id'];?>" aria-expanded="false" aria-controls="collapseTwo">
                                               Повече<i class="fa fa-arrow-down" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                            <div style="clear: both;"></div>
                                        </div>
                                    </div>
                                    <div id="<?php echo $free_tasks['id'];?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                        <div class="panel-body">
                                            <p class="task-stats"><i class="fa fa-spinner" aria-hidden="true"></i> Активна <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                <?php echo $free_tasks['from_date'];?> -
                                                <?php echo $free_tasks['to_date'];?> <i class="fa fa-tasks" aria-hidden="true" title="Подзадачи"></i>
                                                <?php $taskid = $free_tasks['id']; echo  mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `podtasks` WHERE for_task='$taskid'"));?>
                                            </p>
                                            <p class="task-descr">
                                                <?php echo $free_tasks['description'];?>
                                            </p>
                                            <form method="post">
                                                <input style="visibility: hidden;" name="getid" value="<?php echo $free_tasks['id'];?>">
                                                <button type="submit" name="update" class="btn btn-login btn-green">Поеми задача</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php
include("sections/footer.php");
?>
