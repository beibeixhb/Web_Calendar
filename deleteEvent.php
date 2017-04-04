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

// get the post data and session username
$eventId = $_POST['eventId'];
$username = $_SESSION['username'];

$stmt = $mysqli->prepare ("delete from event where id = ? and username = ?");

if(!$stmt){
        printf("Something wrong: %s\n", $mysqli->error);
        exit;
}

$stmt->bind_param('is', $eventId, $username);

if($stmt->execute()){ 
        echo json_encode(array(
                "success" => true
        ));
        $stmt->close();
        exit;
} else {
    echo json_encode(array(
	    "success" => false,
        "message" => "Cannot delete this event. Something Wrong."
	));
    $stmt->close();
	exit;
}
?>