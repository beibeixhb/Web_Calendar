<?php
// use http only options on cookies
ini_set("session.cookie_httponly", 1);
//start the session
session_start();
// require database.php
require 'database.php';

//test for validity of the CSRF token on the server side
if($_SESSION['token'] !== $_POST['token']){
	die("Request forgery detected");
}

//get the session variable
$username = $_SESSION['username'];

// get the post event ID
$id = $_POST['eventId'];
$newDate = $_POST['newDate'];
$newBeginTime = $_POST['newBeginTime'];
$newEndTime = $_POST['newEndTime'];
$newContent = $_POST['newContent'];
$newType = $_POST['newType'];

$stmt = $mysqli->prepare("UPDATE event SET date_of_event=?, beginTime=?, endTime=?, content=?, category=? WHERE id=? and username=?");

if(!$stmt){
	   printf("Query Prep Failed: %s\n", $mysqli->error);
	   exit;
}

$stmt->bind_param('sssssis', $newDate, $newBeginTime, $newEndTime, $newContent, $newType, $id, $username);

if($stmt->execute()){ 
        echo json_encode(array(
                "success" => true
        ));
        $stmt->close();
        exit;
} else {
    echo json_encode(array(
	    "success" => false,
        "message" => "Cannot edit this event."
	));
    $stmt->close();
	exit;
}
?>