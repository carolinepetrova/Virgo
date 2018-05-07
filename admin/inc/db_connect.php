<?php
	@$db_connect = mysqli_connect("localhost", "virgoapp", "iYa7Y8Gzc]+A", "virgoapp_virgo");
	if(mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		exit;
	}
	mysqli_set_charset($db_connect, 'utf8');
	date_default_timezone_set("Europe/Sofia");
	error_reporting(0);
?>