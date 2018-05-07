<?php
$title = "Всички работници";
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res= mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
include("sections/header.php");
if(isset($_POST['submit'])) {
        $name = mysqli_real_escape_string($db_connect, $_POST['name']); 
        $file = rand(1000,100000)."-".$_FILES['image']['name'];
        $file_loc = $_FILES['image']['tmp_name'];
        $fileType = $_FILES["images"]["type"];
        $folder="reports/";
        $imageFileType = pathinfo($file,PATHINFO_EXTENSION);
        move_uploaded_file($file_loc,$folder.$file);
        $uploaded_by = $userinfo['name'];
        if($userinfo['is_admin'] == 'yes') {
            $par_id = $userinfo['id'];
        }
        else 
        {
            $par_id = $userinfo['parent_id'];
        }
            if($_FILES['image']['size'] == 0) {
                $error2= "Моля прикачете файл";
            }    
        else
            if($name == null) {
                $error2= "Моля въведете име";
            }
        else {
            $ins = mysqli_query($db_connect, "INSERT INTO `reports` (`name`, `file` , `uploaded_by` , `parent_id`)     VALUES ('$name','$file', '$uploaded_by','$par_id')");
            mysqli_query($db_connect,"INSERT INTO notifications(`icon`, `text`, `parent_id`, `for_user`,`all_users`) VALUES('fa fa-list-alt','Добавена е нова справка','$par_id','0','yes')");
            if (!$ins)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
            else {
            $success = "updated";
        }
        }
}
?>
    <div id="content2" class="content">
        <?php if ($userinfo['is_admin'] == 'yes') {?>
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Нова справка
            <br>
            <span class="page-head-nav">Начало > Справки > Добави нова</span>
        </div>
        <div class="row" style="margin:15px;">
            <div class="widget widget-white col-md-10 col-md-offset-1">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="col-sm-6">
                        <label>Име</label>
                        <input type="text" class="form-control" name="name" placeholder="Име">
                    </div>
                    <div class="col-sm-6">
                        <label>Файл</label>
                        <div class="input-group" style="margin: 10px 5px;">
                            <span class="input-group-btn">
		                          <button id="fake-file-button-browse" type="button" class="btn btn-default">
			                             <i class="fa fa-upload" style="font-size: 14px; padding: 0; margin: 0;" aria-hidden="true"></i>
                                    </button>
                                    </span>
                            <input type="file" accept=".csv" id="image" name="image" style="display:none; margin: 0;" class="form-control"><input type="text" accept=".csv" id="fake-file-input-name" style="margin: 0;" disabled="disabled" placeholder="Избери .csv файл" class="form-control">
                        </div>
                    </div>
                    <span style="float:right;">
                                
                                <button type="submit" name="submit" class="btn btn-login btn-green">Добави</button>
                    </span>
                </form>
            </div>
        </div>
    </div>
    <?php } else { 
            include("sections/error.php"); 
        } 
    include("sections/footer.php");
    ?>