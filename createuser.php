<?php
ini_set("session.cookie_httponly", 1);

session_start();

$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];

if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
        die("Session hijack detected");
}else{
        $_SESSION['useragent'] = $current_ua;
}

require 'database.php';

header("Content-Type: application/json");

$error_message = "";
//check if entries are valid
$valid_entry=true;
//if password is empty
if(empty($_POST['password'])){
        $valid_entry=false;
	$error_message="Please enter a password.";
}
//is username is empty
else if(empty($_POST['new_user'])){
        $valid_entry=false;
	$error_message="Please enter a username.";
}
//is username already exists
else {
        $stmt = $mysqli->prepare("select user_id, username from users where username='" . $_POST['new_user'] . "'");
        if(!$stmt)
        {
                printf("Query Prep Failed: :(", $mysqli->error);
                exit;
        }

        $stmt->execute();
        $result = $stmt->get_result();
        if(mysqli_num_rows($result) != 0)
        {
                $valid_entry=false;
		$error_message="Username already exists.";
        }

}
//if entries are not valid
if($valid_entry == false) {
	echo json_encode(array(
        	"success" => false,
		"message" => $error_message
        ));
        exit;
}
//if entries are valid
else {
        $username = $_POST['new_user'];
        $password = $_POST['password'];
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

      	$stmt = $mysqli->prepare("insert into users (password_hash, username) values (?, ?);");
        if(!$stmt) {
        	printf("Query Prep Failed: :C", $mysqli-> error);
               	exit;
       	}

        $stmt->bind_param('ss', $password_hash, $username);
        $stmt->execute();
	$stmt->close();

	$command="select user_id from users order by user_id desc limit 1";
	$stmt = $mysqli->prepare($command);
	if(!$stmt) {
		printf("Query Prep Failed: :C", $mysqli-> error);
		exit;
	}

	$stmt->execute();
	$stmt->bind_result($user_id);
	$stmt->fetch();
	$_SESSION['user_id']=$user_id;
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
  	$stmt->close();

	echo json_encode(array(
        	"success" => true,
		"user_id" => $_SESSION['user_id'],
          	"token" => $_SESSION['token']
       	));
        exit;
}
?>
