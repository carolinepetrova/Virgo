<?php 
include("inc/db_connect.php");
function password($pass) {
	return hash('sha512', $pass);
}
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
        $company = mysqli_real_escape_string($db_connect, $_POST['company']); 
        $duty = mysqli_real_escape_string($db_connect, $_POST['duty']); 
        $query=mysqli_query($db_connect,"select * from users where email = '$email'");
        $numOfRows=mysqli_num_rows($query);
        if($email == null) {
            $error= "Моля въведете имейл";
        }	
     
        else
            if($email == null) {
                $error= "Моля въведете имейл";
            }
        else
            if($password == null) {
                $error= "Моля въведете парола";
            }
    else 
        if($numOfRows==1) {
                $error= "Вече съществува такъв имейл";

            }
       else
            if($_FILES['image']['size'] == 0) {
                $error2= "Моля качете снимка";
            }    
        else
            if($name == null) {
                $error= "Моля въведете име";
            }
        else
            if($company == null) {
                $error= "Моля въведете фирма";
            }
        else
            if($duty == null) {
                $error= "Моля въведете длъжност";
            }
        else {
            $pass_insert = password($password);
            $ins = mysqli_query($db_connect, "INSERT INTO `users` (`email`, `password` , `image` , `name`, `company`, `duty`, `is_admin`)     VALUES ('$email','$pass_insert','$file', '$name','$company', '$duty', 'yes')");
            if (!$ins)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
            $success = "updated";
        }
        }
?>

<!DOCTYPE html>

<head>
    <title>Регистрация | Virgo</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/animate.css">

    <link rel="stylesheet" type="text/css" href="css/calendar.css" />
    <link rel="stylesheet" type="text/css" href="css/mobile.css" />
    <link rel="stylesheet" type="text/css" href="css/custom_2.css" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,900&subset=cyrillic" rel="stylesheet">
    <script src="js/modernizr.custom.63321.js"></script>

</head>

<body>
    <div class="login-container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                <?php
                        if($success == "updated") {
                            echo '<div class="alert alert-success" role="alert">Регистрацията ви е успешна</div>';
                        }
                        if($error != NULL) {
			                 echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                        }
                    ?>
                    <div class="login-form animated fadeIn">
                        <img class="img-responsive" src="img/logo.png">
                        <p>Регистрация в системата</p>
                        <div class="row">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="col-xs-12">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
                                </div>
                                <div class="col-xs-12">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Парола">
                                </div>
                                <div class="input-group" style="padding-left: 20px; padding-right: 10px;">
                                    <span class="input-group-btn">
		                          <button id="fake-file-button-browse" type="button" class="btn btn-default">
			                             <i class="fa fa-upload" aria-hidden="true"></i>
                                    </button>
                                    </span>
                                    <input type="file" id="image" name="image" style="display:none; margin: 0;" class="form-control">
                                    <input type="text" id="fake-file-input-name" style="margin: 0;" disabled="disabled" placeholder="Избери снимка" class="form-control">
                                </div>
                                <div class="col-xs-12">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Име и фамилия">
                                </div>
                                <div class="col-xs-6">
                                    <input type="text" class="form-control" id="company" name="company" placeholder="Фирма">
                                </div>
                                <div class="col-xs-6">
                                    <input type="text" class="form-control" id="duty" name="duty" placeholder="Длъжност">
                                </div>
                                <button type="clear" name="clear" class="btn btn-login btn-red">Изчисти</button>
                                <button type="submit" name="submit" class="btn btn-login btn-green">Регистрация</button>
                            </form>
                        </div>
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-sm-12 text-center">
                                <a href="login.php">
                                    <i class="fa fa-lock" aria-hidden="true"></i> &nbsp; Вече имате регистрация?
                                </a>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>


</body>

</html>
