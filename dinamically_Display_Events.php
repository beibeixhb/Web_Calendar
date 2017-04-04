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

$stmt = $mysqli->prepare("select id, date_of_event, beginTime, endTIme, content, category from event where username = ?");

if(!$stmt){
	   printf("Query Prep Failed: %s\n", $mysqli->error);
	   exit;
}

$stmt->bind_param('s', $username);

$stmt->execute();

$stmt->bind_result($id, $date_of_event, $beginTime, $endTime, $content, $category);

$stmt->store_result();

$counter = $stmt->num_rows;

if($counter >= 1){
	while($stmt->fetch()){
		$data[] = array(
            "id" => htmlentities($id),
            "date_of_event" => htmlentities($date_of_event),
            "beginTime" => htmlentities($beginTime),
            "endTime" => htmlentities($endTime),
            "content" => htmlentities($content),
            "category" =>htmlentities($category)
        );
	}
	echo json_encode(array(
		"success"=>true,
		"data"=>$data,
	));
	exit;
}elseif($count == null) {
    $data[] = array(
        "id" => htmlentities($id),
        "date_of_event" => htmlentities($date_of_event),
        "beginTime" => htmlentities($beginTime),
        "endTime" => htmlentities($endTime),            
        "content" => htmlentities($content),
        "category" =>htmlentities($category)
        );
    echo json_encode(array(
	"success" => "true",
    "data"=>$data,
	));
	exit;
}else{
	echo json_encode(array(
	"success" => "false",
	"message" => "ERROR..."
	));
	exit;
}
$stmt->close();
?>