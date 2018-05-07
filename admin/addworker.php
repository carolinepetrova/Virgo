<?php
$title = "Добавяне на работник";
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res=mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
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
        $email = mysqli_real_escape_string($db_connect, $_POST['email']); 
        $password = mysqli_real_escape_string($db_connect, $_POST['password']); 
        $file = rand(1000,100000)."-".$_FILES['image']['name'];
        $file_loc = $_FILES['image']['tmp_name'];
        $fileType = $_FILES["images"]["type"];
        $folder="uploads/";
        $imageFileType = pathinfo($file,PATHINFO_EXTENSION);
        move_uploaded_file($file_loc,$folder.$file);
            // upload image
        $name = mysqli_real_escape_string($db_connect, $_POST['name']); 
        $company2 = $userinfo['company']; 
        $duty = mysqli_real_escape_string($db_connect, $_POST['duty']); 
            $query=mysqli_query($db_connect,"select * from users where email = '$email'");
        $numOfRows=mysqli_num_rows($query);
        if($email == null) {
            $error= "Моля въведете имейл";
        }	
        else 
        if($numOfRows==1) {
                $error= "Вече съществува такъв имейл";

            }
        else
            if($password == null) {
                $error= "Моля въведете парола";
            }
       
        else
            if($_FILES['image']['size'] == 0) {
                $error= "Моля качете снимка";
            }    
        else
            if($name == null) {
                $error= "Моля въведете име";
            }
        else
            if($duty == null) {
                $error= "Моля въведете длъжност";
            }
        else {
            $pass_insert = password($password);
            $ins = mysqli_query($db_connect, "INSERT INTO `users` (`email`, `password` , `image` , `name`, `company`, `duty`,`parent_id`, `is_admin`)     VALUES ('$email','$pass_insert','$file', '$name','$company2', '$duty','$person_id', 'no')");
            if (!$ins)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
            $success = "updated";
        }
        }
?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Добави нов
            <br>
            <span class="page-head-nav">Начало > Работници > Добави нов</span>
        </div>
        <div class="row">
            <div class="widget widget-white col-md-10 col-md-offset-1">
                <?php if($success == "updated") {?>
                <script>
                    alertify.success('Успешно добавихте работник"');

                </script>
                <?php } if($error != NULL) {?>
                <script>
                    alertify.error('<?php echo $error;?>');

                </script>
                <?php } ?>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="col-sm-6">
                        <label>Имейл</label>
                        <input type="email" class="form-control" name="email" placeholder="Имейл">
                    </div>
                    <div class="col-sm-6">
                        <label>Парола</label>
                        <input type="password" class="form-control" name="password" placeholder="Парола">
                    </div>
                    <div class="col-sm-6">
                        <label>Име</label>
                        <input type="text" class="form-control" name="name" placeholder="Име">
                    </div>
                    <div class="col-sm-6">
                        <label>Фирма</label>
                        <input type="text" class="form-control" id="company2" name="company2" value="<?php echo $userinfo['company']; ?>" disabled="disabled">
                    </div>
                    <div class="col-sm-6">
                        <label>Длъжност</label>
                        <input type="text" class="form-control" id="duty" name="duty" placeholder="Длъжност">
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
                    <span style="float:right;">
                                <button type="clear" name="clear" class="btn btn-login btn-red">Изчисти</button> &nbsp;
                                <button type="submit" name="submit" class="btn btn-login btn-green">Добави</button>
                    </span>
                </form>
            </div>
        </div>
    </div>
    <?php
include("sections/footer.php");
?>
