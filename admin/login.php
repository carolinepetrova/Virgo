<?php
	ob_start();
    session_start();
	require_once "inc/db_connect.php";
	
	// it will never let you open index(login) page if session is set
	if ( isset($_SESSION['user'])!="" ) {
		header("Location: index.php");
		exit;
	}

function password($pass) {
	return hash('sha512', $pass);
}
	
	$error = false;
	
	if( isset($_POST['submit']) ) {	
		
		
		$pass = trim($_POST['password']);
		$pass = strip_tags($pass);
		$pass = htmlspecialchars($pass);
		// prevent sql injections / clear user invalid inputs
		
		
		if(empty($pass)){
			$error = true;
			$passError = "Моля въведете паролата си.";
		}
		
		// if there's no error, continue to login
		if (!$error) {
			
            $password = password($_POST['password']);
		
			$res=mysqli_query($db_connect, "SELECT id, email, password FROM users WHERE email='$email'");
			$row=mysqli_fetch_array($res);
			$count = mysqli_num_rows($res); // if uname/pass correct it returns must be 1 row
			
			if( $count == 1 && $row['password']==$password ) {
				$_SESSION['user'] = $row['id'];
				header("Location: index.php");
			} else {
				$errMSG = "Грешна парола или имейл. Опитайте отново.";
			}
				
		}
		
	}
?>
    <!DOCTYPE html>

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login - Virgo</title>
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
                <div class="col align-self-center">
                    <div class="login-form  animated fadeIn">
                        <img class="img-responsive" src="img/logo.png">
                        <p>Влезте в системата</p>
                        <div class="row">
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                                <?php
			if ( isset($errMSG) ) {
				
				?>
                                    <div class="form-group">
                                        <div class="alert alert-danger">
                                            <?php echo $errMSG; ?>
                                        </div>
                                    </div>
                                    <?php
			}
			?>
                                        
                                        <div class="col-12">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Парола">
                                            <span class="text-danger"><?php echo $passError; ?></span>
                                        </div>
                                        <button type="submit" id="submit" name="submit" class="btn btn-login btn-blue">Вход</button>
                            </form>
                        </div>
                        <div class="row" style="margin-top: 15px; ">
                            <div class="col-sm-12 text-right">
                                <a href="register.php">
                                    <i class="fa fa-lock" aria-hidden="true"></i> &nbsp; Нов акаунт
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/jquery.calendario.js"></script>

    </body>

    </html>
