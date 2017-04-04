<?php
//header("Content-Type: application/json");
//get the username and password
$username = $_POST['username'];
$password = $_POST['password'];

//require database.php
require 'database.php';

//do select operation from table userinfo
$stmt = $mysqli->prepare("select username from userinfo");
//if the operation failed, print some error information
if(!$stmt){
    echo json_encode(array(
		"success" => false,
        "message" => "an error occured, please try again"
    ));
    exit;		
}
//execute the operation
$stmt->execute();
//bind the results into variable username
$stmt->bind_result($oldusername);
//if the newusername already exists, cannot register successfully
while ($stmt->fetch()) {
    if ($oldusername == $username) {
        echo json_encode(array(
            "success" => false,
            "message" => "username already exists..."
        ));
        exit;
    }
}

//use salted one-way encryption
$crypted_password = crypt($password);
//insert the username and password into table userinfo
$stmt = $mysqli->prepare("insert into userinfo (username, password) values (?, ?)");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('ss', $username, $crypted_password);

//if insert successfully
if ($stmt->execute()) {
    $stmt->close();
    echo json_encode(array(
        "success" => true
    ));
    exit;
}

?>