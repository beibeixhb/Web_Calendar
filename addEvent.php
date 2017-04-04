<?php
// use http only options on cookies
ini_set("session.cookie_httponly", 1);
// start the session
session_start();
// require database.php
require 'database.php';

//test for validity of the CSRF token on the server side
if($_SESSION['token'] !== $_POST['token']){
	die("Request forgery detected");
}

// get the post data from the client side and store them in some variables
$date_of_event = $_POST['date_of_event'];
$beginTime = $_POST['beginTime'];
$endTime = $_POST['endTime'];
$content = $_POST['content'];
$category = $_POST['category'];

$stmt = $mysqli->prepare("insert into event (username, date_of_event, beginTime, endTime, content, category) values (?, ?, ?, ?, ?, ?)");

if(!$stmt){
	printf("Something wrong: %s\n", $mysqli->error);
	exit;
}

// bind the param
$stmt->bind_param('ssssss', $_SESSION['username'], $date_of_event, $beginTime, $endTime, $content, $category);

// execute and return the message
if ($stmt->execute()) {
    echo json_encode (array("success" => true));
    $stmt -> close();
    exit;
} else {
    echo json_encode (array ("success" => false, "message" => "ERROR, can't add event."));
    $stmt -> close();
    exit;
}
?>
