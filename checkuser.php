<?php
ini_set("session.cookie_httponly", 1);
require 'database.php';

header("Content-Type: application/json");

$error_message = "";
$valid_entry = true;
//check is username and password are set
if(empty($_POST['username'])){
	$valid_entry = false;
	$error_message = "Please fill out all fields.";
}
if(empty($_POST['pswd'])){
	$valid_entry = false;
	$error_message = "Please fill out all fields.";
}
//if fields are not empty
else {	
$pswd = $mysqli->real_escape_string($_POST['pswd']);
	$username = $mysqli->real_escape_string($_POST['username']);

	$stmt = $mysqli->prepare("select password_hash from users where username =?");
	if(!$stmt){
		printf("Querty Prep Failed: :(", $mysqli->error);
		exit;
	}

	$pswd = $_POST['pswd'];
	$username = $_POST['username'];
	$stmt->bind_param('s', $username);
      	$stmt->execute();
    	$stmt->bind_result($password_hash);
      	$stmt->fetch();

        if(password_verify($pswd, $password_hash))
        {
        	$stmt->close();

		//get user_id
		$stmt = $mysqli->prepare("select user_id from users where username =?");
		if(!$stmt){
			printf("Querty Prep Failed: :(", $mysqli->error);
			exit;
		}

		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->bind_result($user_id);
		$stmt->fetch();
        	session_start();

		$previous_ua = @$_SESSION['useragent'];
		$current_ua = $_SERVER['HTTP_USER_AGENT'];

		if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
        		die("Session hijack detected");
		}else{
		        $_SESSION['useragent'] = $current_ua;
		}

        	$_SESSION['user_id'] = $user_id;
          	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));

       		echo json_encode(array(
			"success" => true,
			"message" => "Logged in",
			"user_id" => $_SESSION['user_id'],
			"token" => $_SESSION['token']
		));
		exit;
        }
        else {
		$valid_entry=false;
		$error_message="Incorrect login information.";
		$stmt->close();
	}

}
if($valid_entry == false) {
 	echo json_encode(array(
        "success" => false,
        "message" => $error_message
        ));
        exit;
}
?>
