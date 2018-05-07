<?php
$title = "Моите задачи";
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res= mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
include("sections/header.php");
$getid = $_GET['id'];
$getinfo= mysqli_query($db_connect, "SELECT * FROM tasks WHERE id='$getid'");
$gettask=mysqli_fetch_assoc($getinfo);
if(isset($_POST['submit'])) { 
     $ins =mysqli_query($db_connect, "UPDATE `tasks` SET `active`='no'  WHERE id='$getid'");
            if (!$ins)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
    else {
            $success = "updated";
    }

}
if(isset($_POST['update'])) { 
    $notes = mysqli_real_escape_string($db_connect, $_POST['notes']); 
     $ins =mysqli_query($db_connect, "UPDATE `tasks` SET `notes`='$notes'  WHERE id='$getid'");
            if (!$ins)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
    else {
            $success2 = "updated";
    }
foreach ($_POST['checkbox'] as $value) {
    mysqli_query($db_connect, "UPDATE podtasks SET active = 'no' WHERE id = $value");
}
}
?>
    <?php if($gettask['active'] == 'yes') { ?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Преглед и редакция на задача
            <br>
            <span class="page-head-nav">Начало > Задачи > Преглед и редакция на задача</span>
        </div>
        <div class="row" style="margin:15px;">
            <div class="col-md-12">
                <div class="widget widget-white">
                    <?php if($success == "updated") {?>
                    <script>
                        alertify.success('Успешно завършихте задачата! Ще бъдете пренасочени след 5 секунди');

                        window.setTimeout(function() {
                            location.href = "http://virgoapp.eu/admin/mytasks";
                        }, 3000);

                    </script>
                    <?php } if($error != NULL) {?>
                    <script>
                        alertify.error('<?php echo error;?>');

                    </script>
                    <?php }  if($success2 == "updated") {?>
                    <script>
                        alertify.success('Успешно запазихте прогреса на задачата!');

                    </script>
                    <?php }?>
                    <div class="row">
                        <h2 class="text-center">
                            <?php echo $gettask['name'];?>
                        </h2>
                        <p class="task-stats"><i class="fa fa-spinner" aria-hidden="true"></i> Активна <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                            <?php echo $gettask['from_date'];?> -
                            <?php echo $gettask['to_date'];?> <i class="fa fa-tasks" aria-hidden="true" title="Подзадачи"></i>
                            <?php echo mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `podtasks` WHERE `for_task`='$getid'")); ?>
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="task-descr">
                                    <h4>Oписание</h4>
                                    <?php echo $gettask['description'];?>
                                </p>
                            </div>
                            <form method="post">
                                <div class="col-md-6">
                                    <h4>Бележки</h4>
                                    <textarea class="form-control" name="notes"><?php echo $gettask['notes'];?></textarea>
                                </div>
                        </div>
                        <div class="col-md-12">
                            <h4>Подзадачи</h4>
                            <?php 
                                    $get_taskid = $gettask['id'];
                                    $getallpodtasks = mysqli_query($db_connect, "SELECT * FROM  `podtasks` WHERE for_task='$get_taskid'");
                                        while($podtask = mysqli_fetch_assoc($getallpodtasks)) {
                                    
                            ?>
                            <div class="checkbox checkbox-success">
                                <input name="checkbox[]" value="<?php echo $podtask['id'];?>" id="<?php echo $podtask['id'];?>" type="checkbox" <?php if($podtask[ 'active']=='no' ) {?> checked="checked" disabled="disabled"
                                <?php }?>>
                                <label for="<?php echo $podtask['id'];?>" <?php if($podtask[ 'active']=='no' ) {?>style="text-decoration: line-through;" <?php }?>>
                                    <?php echo $podtask['name'];?>
                                </label>
                            </div>
                            <?php }?>
                        </div>
                        <button type="submit" id="submit" name="update" class="btn btn-login btn-blue">Запази прогрес</button>
                        <button type="submit" name="submit" class="btn btn-login btn-green">Завърши задача</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php } else { include("sections/error.php"); } ?>
    <?php
include("sections/footer.php");
?>
