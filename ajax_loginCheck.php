
   <?php
    //set the MIME type to application/json
    //header("Content-Type: application/json");
    
    //get the username and password
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    //require database operation
    require 'database.php';
    
    $stmt = $mysqli->prepare ("SELECT username, password, COUNT(*) FROM userinfo WHERE username=?");

    if(!$stmt){
        echo json_encode(array(
			"success" => false,
			"message" => "an error occured, please try again"
		));
		exit;		
    }

    $stmt->bind_param('s', $username);

    $stmt->execute();

    $stmt->bind_result($returnedUsername, $hashedPassword, $count);

    $stmt->fetch();

    if ($count==1 && crypt($password, $hashedPassword) == $hashedPassword) {
        //all information provided is correct, start a session
        ini_set("session.cookie_httponly", 1);
        session_start();
        
        $previous_ua = @$_SESSION['useragent'];
        $current_ua = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['username'] = $username;
        
        if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
	       die("Session hijack detected");
        } else{
	       $_SESSION['useragent'] = $current_ua;
        }
        
        //create a token
        $_SESSION['token'] = substr(md5(rand()), 0, 10);
        echo json_encode(array(
            "success" => true,
            "token" => htmlentities($_SESSION['token']),
            "username" => htmlentities($_SESSION['username'])
        ));
        exit;
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Incorrect Username or Password"
        ));
        exit;
    }

    $stmt->close();
?>