<?php
// use http only options on cookies
ini_set("session.cookie_httponly", 1);
//start the session
session_start();

if(!isset($_SESSION['username'])){
	//$data[] = null;
	echo json_encode(array(
	    "success" => "true",
	    "data" => null,
	));
	exit;
}

// require database.php
require 'database.php';
//get the session variable
$username = $_SESSION['username'];

//test for validity of the CSRF token on the server side
if($_SESSION['token'] !== $_POST['token']){
	die("Request forgery detected");
}

$stmt = $mysqli->prepare("select date_of_event, beginTime, endTIme, content, category from event where username = ?");

if(!$stmt){
	   printf("Query Prep Failed: %s\n", $mysqli->error);
	   exit;
}

$stmt->bind_param('s', $username);

$stmt->execute();

$stmt->bind_result($date_of_event, $beginTime, $endTime, $content, $category);

$stmt->store_result();

$counter = $stmt->num_rows;

if($counter >= 1){
	while($stmt->fetch()){
		$data[] = array(
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
}else if($counter == null) {
    $data[] = array(
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

//while ($stmt->fetch()) {
//        $data[] = array(
//            "date_of_event" => htmlentities($date_of_event),
//            "beginTime" => htmlentities($beginTime),
//            "endTime" => htmlentities($endTime),
//            "content" => htmlentities($content),
//            "category" =>htmlentities($category)
//        );
//}
//// send data in a json encode array
//echo json_encode(array(
//		"success" => true,
//		"data" => $data
//	));
//
//$stmt->close();


?>