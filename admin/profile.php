<?php
include("inc/db_connect.php");
include("inc/functions.php");
$title = "Редактиране на профил";
$person_id = $_SESSION['user'];
$res=mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
include("sections/header.php");
if(isset($_POST['update'])) {
		// text fields
		$email = mysqli_real_escape_string($db_connect, $_POST['email']); 
        $password = mysqli_real_escape_string($db_connect, $_POST['password']); 
        $name = mysqli_real_escape_string($db_connect, $_POST['name']); 
        $company = mysqli_real_escape_string($db_connect, $_POST['company']); 
        $duty = mysqli_real_escape_string($db_connect, $_POST['duty']);
        $phone = mysqli_real_escape_string($db_connect, $_POST['phone']);
        $adress = mysqli_real_escape_string($db_connect, $_POST['adress']);
        $file = rand(1000,100000)."-".$_FILES['image']['name'];
        $file_loc = $_FILES['image']['tmp_name'];
        $fileType = $_FILES["images"]["type"];
        $folder="uploads/";
        $imageFileType = pathinfo($file,PATHINFO_EXTENSION);
        move_uploaded_file($file_loc,$folder.$file);
        $emptyimage = $_FILES['image']['name'];
        if (empty($password) and empty($emptyimage)) {
		mysqli_query($db_connect, "UPDATE `users` SET `email`='$email', `name`='$name', `company` = '$company', `duty` = '$duty', `phone` = '$phone', `adress` = '$adress' WHERE `id`='$person_id'");
         header("Refresh: 0");
     }
    else 
    {   
        if($password != null and empty($emptyimage)) {
        $pass_insert = password($password);
        mysqli_query($db_connect, "UPDATE `users` SET `email`='$email', `password`='$pass_insert', `name`='$name', `company` = '$company', `duty` = '$duty',  `phone` = '$phone', `adress` = '$adress'  WHERE `id`='$person_id'");
        header("Refresh: 0");
        }
        elseif ($emptyimage != null and empty($password)) {
        mysqli_query($db_connect, "UPDATE `users` SET `email`='$email', `name`='$name', `company` = '$company', `duty` = '$duty', `image` = '$file', `phone` = '$phone', `adress` = '$adress'  WHERE `id`='$person_id'");
        header("Refresh: 0");
        }
        else {
            mysqli_query($db_connect, "UPDATE `users` SET `email`='$email', `password`='$pass_insert', `name`='$name', `company` = '$company', `duty` = '$duty', `image` = '$file', `phone` = '$phone', `adress` = '$adress'  WHERE `id`='$person_id'"); 
        header("Refresh: 0");
        }
    }
	}

    ?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Редакция на профил
            <br>
            <span class="page-head-nav">Начало > Редакция на профил</span>
        </div>
        <div class="row" style="margin:10px;">
            <div class="row">
                <?php
		if($success == "updated") {
            success();
			echo '<div class="alert alert-success" role="alert">Успешно обновено</div>';
		}
		if($error != NULL) {
			echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
		}
?>
                    <div class="col-md-8">
                        <div class="widget widget-white">
                            <div class="row">
                                <h2>Главна информация</h2>
                                <form method="POST" action="" enctype="multipart/form-data" autocomplete="off">
                                    <div class="col-sm-6">
                                        <label>Имейл</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $userinfo['email']; ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Парола</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Въведете нова парола">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Име</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $userinfo['name']; ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Фирма</label>
                                        <input type="text" class="form-control" id="company" name="company" value="<?php echo $userinfo['company']; ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Длъжност</label>
                                        <input type="text" class="form-control" id="duty" name="duty" value="<?php echo $userinfo['duty']; ?>">
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
                                <button type="submit" name="update" class="btn btn-login btn-green">Обнови</button>
                                    </span>


                            </div>
                        </div>
                        <div class="widget widget-white">
                            <div class="row">
                                <h2>Допълнителна информация</h2>

                                <div class="col-xs-6">
                                    <label>Телефон</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $userinfo['phone']; ?>">
                                </div>
                                <div class="col-xs-6">
                                    <label>Адрес</label>
                                    <input type="text" class="form-control" id="adress" name="adress" value="<?php echo $userinfo['adress'];?>">
                                </div>
                                <span style="float:right;">
                                <button type="clear" name="clear" class="btn btn-login btn-red">Изчисти</button> &nbsp;
                                <button type="submit" name="update" class="btn btn-login btn-green">Обнови</button>
                                    </span>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-white col-md-4">
                        <center><img class="img-responsive" width="150" style="border-radius: 50%; object-fit: cover; height: 150px;" src="uploads/<?php echo $userinfo['image'];?>"></center>
                        <div class="profile-info">
                            <h3>
                                <?php echo $userinfo['name'];?>
                            </h3>
                            <h4><b><?php echo $userinfo['duty'];?></b></h5>
                                <br>
                                <h5><i class="fa fa-building" style="font-size:14px" aria-hidden="true"></i>
                                    <?php echo $userinfo['company'];?>
                                </h5>
                                <h5><i class="fa fa-envelope" style="font-size:14px" aria-hidden="true"></i>
                                    <?php echo $userinfo['email'];?>
                                </h5>
                                <?php if($userinfo['phone'] != null) { ?>
                                <h5><i class="fa fa-phone" style="font-size:14px" aria-hidden="true"></i>
                                    <?php echo $userinfo['phone'];?>
                                </h5>
                                <?php }?>
                                <?php if($userinfo['adress'] != null) { ?>
                                <h5><i class="fa fa-phone" style="font-size:14px" aria-hidden="true"></i>
                                    <?php echo $userinfo['adress'];?>
                                </h5>
                                <?php }?>

                        </div>
                    </div>
            </div>
        </div>
    </div>

    <?php
include("sections/footer.php");
?>
