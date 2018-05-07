<?php
$title = "Нова диаграма";
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
        $file = mysqli_real_escape_string($db_connect, $_POST['csv']); 
        $type_chart = mysqli_real_escape_string($db_connect, $_POST['type_chart']); 
        $getname= mysqli_query($db_connect, "SELECT name FROM reports WHERE id='$file'");
        $name=mysqli_fetch_assoc($getname);
        $namecsv = $name['name'];
        $usrname = $userinfo['name'];
        $ins = mysqli_query($db_connect, "INSERT INTO `all_charts` (`name`, `csv`,`added_by`, `type_chart` , `parent_id`)     VALUES ('$namecsv','$file','$usrname','$type_chart', '$per_id')");
        $userid = "0";
        $text = "Добавена е нова диаграма";
        $icon = "fa fa-pie-chart";
         $ins = mysqli_query($db_connect, "INSERT INTO `notifications` (`icon`, `text`, `parent_id` , `for_user`)     VALUES ('$icon','$text','$per_id', '$userid')");
            if (!$ins)  {
                die("SQL Error: ".mysqli_error($db_connect));
                $error = "Възникна грешка";
            }
    else {
            $success = "updated";
    }
        }
?>
    <div id="content2" class="content">
        <?php if ($userinfo['is_admin'] == 'yes') {?>
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Нова диаграма
            <br>
            <span class="page-head-nav">Начало > Диаграми > Нова диаграма</span>
        </div>
        <div class="row" style="margin:15px;">
            <div class="widget widget-white col-md-10 col-md-offset-1">
                <?php if($success == "updated") {?>
                <script>
                    alertify.success('Успешно добавихте нова диаграма');

                </script>
                <?php } if($error != NULL) {?>
                <script>
                    alertify.error('<?php echo $error;?>');

                </script>
                <?php } ?>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="col-sm-6">
                        <label>Изберете справка</label>
                        <select class="form-control" name="csv">
                                    <?php
                                    $get_reports = mysqli_query($db_connect, "SELECT * FROM reports WHERE parent_id='$per_id'");
                                    while($reports = mysqli_fetch_assoc($get_reports)) {
                                    ?>
                                    <option value="<?php echo $reports['id'];?>"><?php echo $reports['name'];?></option>
                                    
                                    <?php }?>
                                </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Вид диаграма</label>
                        <select class="form-control" name="type_chart">
                                    <?php
                                    $get_charts = mysqli_query($db_connect, "SELECT * FROM type_charts");
                                    while($charts = mysqli_fetch_assoc($get_charts)) {
                                    ?>
                                    <option value="<?php echo $charts['js_filename'];?>"><?php echo $charts['name'];?></option>
                                    
                                    <?php }?>
                                </select>
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
