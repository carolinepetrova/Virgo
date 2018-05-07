<?php 
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

$notifications = mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM notifications WHERE  seen = 'no' and (for_user='$person_id' or for_user='0')")); 

if(isset($_POST['update'])) { 
foreach ($_POST['checkbox'] as $value) {
    mysqli_query($db_connect, "UPDATE notifications SET seen = 'yes' WHERE id = $value");
}
}

$notSeenMsg = mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `messages` WHERE user_to='$person_id' and seen='no' GROUP By conversation_id"));

$counterNotif = $notifications + $notSeenMsg;
?>

<!DOCTYPE html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php echo $title; if($counterNotif !=0) { echo " (".$counterNotif.") "; }?> - Virgo</title>
    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" type="text/css" href="css/custom_2.css" />
    <link rel="stylesheet" href="css/fullcalendar.css" />
    <link href='css/fullcalendar.print.css' rel='stylesheet' media='print' />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,900&subset=cyrillic" rel="stylesheet">
    <script src="js/modernizr.custom.63321.js"></script>
    <link rel="stylesheet" href="css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/default.rtl.min.css" />
    
    <!-- JS that needs to be put in head -->
    <script src="js/alertify.min.js"></script>
    <script src="js/zingchart.min.js"></script>
    <script> zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
        ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9","ee6b7db5b51705a13dc2339db3edaf6d"];</script>

</head>

<body>
    <header class="top-head container-fluid">
        <div id="content" class="content">
            <button type="button" id="toggle-nav" class="navbar-toggle pull-left visible-xs">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

            <!-- Left navbar -->
            <nav class=" navbar-default " role="navigation">
                <!-- Right navbar -->
                <ul class="nav navbar-nav navbar-right top-menu top-right-menu">
                    <!-- Notification -->
                    <li class="dropdown2">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <div class="group">
                                <i class="fa fa-bell-o"></i>
                                <span <?php if($notifications=='0' ) {?> style="visibility: hidden"  <?php }?> class="badge badge-sm up bg-green count"><?php  echo $notifications; ?></span>

                            </div>
                        </a>
                        <ul class="dropdown-menu extended fadeIn animated nicescroll " tabindex="5002" style="overflow: hidden; outline: none;">
                            <form method="post">
                                <li class="noti-header">
                                    <span class="pull-left"><p>Известия</p></span>
                                    <span class="pull-right"><button type="submit" name="update" class="read-all">Маркирай като прочетени</button></span>
                                    <div style="clear:both"></div>
                                </li>
                                <?php 
                                $get_notif = mysqli_query($db_connect, "SELECT * FROM notifications WHERE parent_id='$per_id' and all_users='yes' or for_user ='$person_id' ORDER BY id DESC LIMIT 5");
                            while($notif = mysqli_fetch_assoc($get_notif)) {?>
                                <li id="<?php echo $notif['id'];?>" <?php if($notif[ 'seen']=="no" ) {?> style="background:#F0F0F0;"
                                    <?php } ?>>
                                    <div class="checkbox">
                                        <label>
                                        <input name="checkbox[]" value="<?php echo $notif['id'];?>" id="<?php echo $notif['id'];?>" type="checkbox" <?php if($notif[ 'seen']=='yes' ) {?> checked="checked" disabled="disabled"
                                        <?php }?>>
                                                   <span class="pull-left"><i class="<?php echo $notif['icon'];?> fa-2x text-info"></i></span>
                                    <div class="notification-text"><?php echo $notif['text'];?> <br><small class="text-muted" data-livestamp="<?php echo $notif['date'];?>"></small></div>
                                </label>
                                    </div>

                                </li>
                                <?php }?>
                                <li>
                                    <p><a href="notifications" class="text-right">Виж всички</a></p>
                            </form>
                            </li>
                        </ul>
                    </li>
                                <li class="dropdown2">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <div class="group">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                <span <?php if($notSeenMsg=='0' ) {?> style="visibility: hidden"  <?php }?> class="badge badge-sm up bg-red count"><?php  echo $notSeenMsg; ?></span>

                            </div>
                        </a>
                        <ul class="dropdown-menu extended fadeIn animated nicescroll " tabindex="5002" style="overflow: hidden; outline: none;">
                            <form method="post">
                                <li class="noti-header">
                                    <span class="pull-left"><p>Нови съобщения</p></span>
                                    <div style="clear:both"></div>
                                </li>
                                <?php 
                                $conver = mysqli_query($db_connect, "SELECT * FROM `messages` WHERE user_to='$person_id' and seen='no' GROUP By conversation_id ORDER BY `time_added` DESC");
                                while ($fetch = mysqli_fetch_assoc($conver)) {?>
                                <li>
                                    <a href="messages?id=<?php echo $fetch['user_from'];?>">
                                <?php     
                                    $senderId = $fetch['user_from']; 
                                    $getUsr = mysqli_query($db_connect, "SELECT * FROM `users` WHERE id='$senderId'");
                                    while ($usr = mysqli_fetch_assoc($getUsr)) { ?>
                                    <span class="pull-left msg-icon"><img style="" src="uploads/<?php echo $usr['image'];?>"></img></span>
                                    <div class="notification-text"><?php echo $usr['name']; }?><br><small class="text-muted" data-livestamp="<?php echo $fetch['time_added'];?>"></small><br><?php echo $fetch['message'];?> </div>
                                </a>

                                </li>
                                <?php }?>
                                <li>
                                    <p><a href="messages" class="text-right">Всички разговори</a></p>
                            </form>
                            </li>
                        </ul>
                    </li>
                    <!-- /Notification -->
                    <!-- user login dropdown start-->
                    <li class="dropdown2 text-center 0">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <?php if($userinfo['image'] != null) {?>
                            <img alt="" src="uploads/<?php echo $userinfo['image']; ?>" class="img-circle profile-img thumb-sm">
                            <?php }?>
                            <span class="username hidden-xs"><?php echo $userinfo['name']; ?></span> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu extended fadeIn animated nicescroll" tabindex="5003" style="overflow: hidden; outline: none;">
                            <li><a href="profile"><i class="fa fa-briefcase"></i>Профил</a></li>
                            <li><a href="logout.php?logout"><i class="fa fa-sign-out"></i>Изход</a></li>
                        </ul>
                    </li>
                    <!-- user login dropdown end -->
                </ul>
                <!-- End right navbar -->
            </nav>
        </div>
    </header>
    <aside class="nav" id="" tabindex="5001" style="overflow: hidden; outline: none;">
        <!-- brand -->
        <div class="logo hidden-xs">
            <a href="index" class="logo-expanded">
                <i class="ion-social-buffer"></i>
                <center>
                    <span class="nav-label"><img class="img-responsive" width="90" src="img/logo.png"></span>
                </center>
            </a>
        </div>
        <!-- / brand -->
        <li class="text-left me hidden-xs">
            <?php if($userinfo['image'] != null) {?>
            <img alt="" src="uploads/<?php echo $userinfo['image']; ?>" class="profile-img thumb-sm">
            <?php }?>
            <span class="welcome hidden-xs">Добре Дошли</span><br>
            <span class="username2 hidden-xs"><?php echo $userinfo['name']; ?></span>
        </li>
        <!-- Navbar Start -->
        <nav class="inner-nav">
            <ul class="list-unstyled">
                <li class="active" id="">
                    <a href="index"><span class="nav-label"><i class="fa fa-home" aria-hidden="true"></i>Начало</span></a>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user" aria-hidden="true"></i><span class="nav-label">Служители</span></a>
                    <ul class="dropdown-menu animated fadeIn">
                        <li id="workers"><a href="workers"><i class="fa fa-users" aria-hidden="true"></i> Всички служители<span class="badge bg-blue"><?php  echo mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `users` WHERE `parent_id`='$per_id'")); ?></span></a></li>
                        <?php if ($userinfo['is_admin'] == 'yes') {?>
                        <li id="addworker">
                            <a href="addworker"> <i class="fa fa-user-plus" aria-hidden="true"></i> Добави нов</a>
                        </li>
                        <li id="workersettings"><a href="workersettings"><i class="fa fa-cogs" aria-hidden="true"></i>Настройки</a></li>
                        <?php }?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="nav-label"><i class="fa fa-calendar" aria-hidden="true"></i>Графици</span></a>
                    <ul class="dropdown-menu animated fadeIn">
                        <?php if ($userinfo['is_admin'] == 'yes') {?>
                        <li id="editcalendar"><a href="editcalendar"><i class="fa fa-pencil" aria-hidden="true"></i> Редактирай графици</a></li>
                        <?php }?>
                        <li id="mycalendar">
                            <a href="mycalendar"> <i class="fa fa-th-large" aria-hidden="true"></i> Моят график</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown" id="spravki"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-list" aria-hidden="true"></i><span class="nav-label">Справки</span></a>
                    <ul class="dropdown-menu animated fadeIn">
                        <li id="allreports"><a href="allreports"><i class="fa fa-th-large" aria-hidden="true"></i> Всички справки<span class="badge bg-blue"><?php  echo mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `reports` WHERE `parent_id`='$per_id'")); ?></span></a></li>
                        <?php if ($userinfo['is_admin'] == 'yes') {?>
                        <li id="addreport">
                            <a href="addreport"> <i class="fa fa-plus" aria-hidden="true"></i> Добави нов</a>
                        </li>
                        <?php }?>
                    </ul>
                </li>
                <li class="dropdown" id="tasks"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-pie-chart" aria-hidden="true"></i><span class="nav-label">Диаграми</span></a>
                    <ul class="dropdown-menu animated fadeIn">
                        <?php if ($userinfo['is_admin'] == 'yes') {?>
                        <li id="newchart">
                            <a href="newchart"> <i class="fa fa-plus" aria-hidden="true"></i> Нова диаграма</a>
                        </li>
                        <?php }?>
                        <li id="allcharts"><a href="allcharts"><i class="fa fa-bar-chart" aria-hidden="true"></i> Всички диаграми <span class="badge bg-blue"><?php  echo mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `all_charts` WHERE `parent_id`='$per_id'")); ?></span></a></li>
                        

                    </ul>
                </li>
                <li class="dropdown" id="tasks"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-tasks" aria-hidden="true"></i><span class="nav-label">Задачи</span></a>
                    <ul class="dropdown-menu animated fadeIn">
                        <li id="mytasks">
                            <a href="mytasks"> <i class="fa fa-thumb-tack" aria-hidden="true"></i> Моите задачи<span class="badge bg-blue"><?php  echo mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `tasks` WHERE `for_user`='$person_id' AND active='yes'")); ?></span></a>
                        </li>
                        <?php if ($userinfo['is_admin'] == 'yes') {?>
                        <li id="newtask"><a href="newtask"><i class="fa fa-plus" aria-hidden="true"></i> Добави задача</a></li>
                        <li id="tasksettings"><a href="tasksettings"><i class="fa fa-cogs" aria-hidden="true"></i>Настройки</a></li>
                        <?php }?>

                    </ul>
                </li>
                <li id="messages"><a href="messages"><i class="fa fa-comments" aria-hidden="true"></i><span class="nav-label">Съобщения</span></a>
                    
                </li>
        
            </ul>
        </nav>
    </aside>
