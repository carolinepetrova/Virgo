<?php
if(mb_strstr($_SERVER["PHP_SELF"], "functions.php", "UTF-8")) {
include('../404.php');
exit;
}

/* User session */
	ob_start();
	session_start();
	// if session is not set this will redirect to login page
	if( !isset($_SESSION['user']) ) {
		header("Location: login.php");
		exit;
	}

// URL
$siteurl = "http://".str_replace("www.", "", $_SERVER['SERVER_NAME']);
$siteurl_text = str_replace("www.", "", $_SERVER['SERVER_NAME']);



function password($pass) {
	return hash('sha512', $pass);
}

function hideGET() {
    $url =  "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $noget = strtok($url, '?');
    header("Location: ".$noget);
}


/* Admin panel */

/* Alert messages */

function msg($type, $text) {
	// type = success, info, danger, warning
	$msg = '<div class="alert alert-'.$type.'" role="alert">'.$text.'</div>';	
	return $msg;
}