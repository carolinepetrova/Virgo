<?php
include('inc/db_connect.php');
ob_start();
session_start();
	
$type = $_POST['type'];
$person_id = $_SESSION['user'];
$res= mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
if($userinfo['is_admin'] == 'yes') {
    if($userinfo['parent_id'] == "0") {
            $par_id = $_SESSION['user'];
    }
    else {
            $par_id = $userinfo['parent_id'];
        }
    }
    else 
        {
            $par_id = $userinfo['parent_id'];
        }

if($type == 'new')
{
	$startdate = $_POST['startdate'].'+'.$_POST['zone'];
	$title = $_POST['title'];
    $child_id = $_POST['child_id'];
	$insert = mysqli_query($db_connect,"INSERT INTO calendars(`title`, `startdate`, `enddate`, `allDay`, `parent_id`, `child_id`) VALUES('$title','$startdate','$startdate','false', '$par_id', '$child_id')");
	$lastid = mysqli_insert_id($db_connect);
	echo json_encode(array('status'=>'success','eventid'=>$lastid));
    mysqli_query($db_connect,"INSERT INTO notifications(`icon`, `text`, `parent_id`, `for_user`,`all_users`) VALUES('fa fa-calendar-plus-o','Направени са промени във графика ви','$par_id','$child_id','no')");
}

if($type == 'changetitle')
{
	$eventid = $_POST['eventid'];
	$title = $_POST['title'];
	$update = mysqli_query($db_connect,"UPDATE calendars SET title='$title' where id='$eventid'");
	if($update)
		echo json_encode(array('status'=>'success'));
	else
		echo json_encode(array('status'=>'failed'));
}

if($type == 'resetdate')
{
	$title = $_POST['title'];
	$startdate = $_POST['start'];
	$enddate = $_POST['end'];
	$eventid = $_POST['eventid'];
	$update = mysqli_query($db_connect,"UPDATE calendars SET title='$title', startdate = '$startdate', enddate = '$enddate' where id='$eventid'");
	if($update)
		echo json_encode(array('status'=>'success'));
	else
		echo json_encode(array('status'=>'failed'));
}

if($type == 'remove')
{
	$eventid = $_POST['eventid'];
	$delete = mysqli_query($db_connect,"DELETE FROM calendars where id='$eventid'");
	if($delete)
		echo json_encode(array('status'=>'success'));
	else
		echo json_encode(array('status'=>'failed'));
}

if($type == 'fetch')
{
	$events = array();
	$query = mysqli_query($db_connect, "SELECT * FROM calendars WHERE parent_id='$par_id'");
	while($fetch = mysqli_fetch_array($query,MYSQLI_ASSOC))
	{
	$e = array();
    $e['id'] = $fetch['id'];
    $e['title'] = $fetch['title'];
    $e['start'] = $fetch['startdate'];
    $e['end'] = $fetch['enddate'];

    $allday = ($fetch['allDay'] == "true") ? true : false;
    $e['allDay'] = $allday;

    array_push($events, $e);
	}
	echo json_encode($events);
}


?>