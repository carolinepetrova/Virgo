<?php
   include("inc/db_connect.php");
// Selecting Database
$db = mysql_select_db("virgo", $db_connect);
session_start();// Starting Session
// Storing Session
$user_check=$_SESSION['login_user'];
// SQL Query To Fetch Complete Information Of User
$ses_sql=mysql_query("select email from users where email='$user_check'", $db_connect);
$row = mysql_fetch_assoc($ses_sql);
$login_session =$row['username'];
if(!isset($login_session)){
header('Location: index.php'); // Redirecting To Home Page
}
?>