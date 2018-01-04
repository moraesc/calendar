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

if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}

require 'database.php';

header("Content-Type: application/json");

$error_message="";
//check if entries are valid
$valid_entry=true;
//username field empty
if(empty($_POST['share_user'])){
	$valid_entry=false;
	$error_message = "Please enter a username.";
}
//if username doesn't exist
else { 
	$share_user = $mysqli->real_escape_string($_POST['share_user']);
	$stmt = $mysqli->prepare("select user_id, username from users where username='" . $share_user . "';");
		if(!$stmt) {
			printf("Query Prep Failed: :(", $mysqli->error);
			exit;
		}

		$stmt->execute();
		$result = $stmt->get_result();
		if(mysqli_num_rows($result) == 0) {
			$valid_entry=false;
			$error_message="This username doesn't exist";
		}

}
//entry not valid
if($valid_entry == false) {
        echo json_encode(array(
                "success" => false,
		"message" => $error_message
        ));
        exit;
}
//entries are valid
else {

	$stmt = $mysqli->prepare("select user_id from users where username=?;");

	if(!$stmt) {
		printf("Query Prep Failed: :(", $mysqli-> error);
		exit;
	}

 	$stmt->bind_param('s', $_POST['share_user']);
	$stmt->execute();
	$stmt->bind_result($user_id);
	$stmt->fetch();
	$stmt->close();

	$stmt = $mysqli->prepare("insert into events (title, year, month, day, time, type, recurring, owner_id) values (?, ?, ?, ?, ?, ?, ?, ?);");
	if(!$stmt) {
		printf("Query Prep Failed: :(", $mysqli-> error);
		exit;
	}

        $title = $_POST['title'];
        $year = $_POST['year'];
        $month = $_POST['month'];
        $day = $_POST['day'];
        $time = $_POST['time'];
        $type = $_POST['type'];
        $recurring = $_POST['recurring'];
        $owner_id = $user_id;

        $stmt->bind_param('siiisssi', $title, $year, $month, $day, $time, $type, $recurring, $owner_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(array(
	         "success" => true
                ));
                exit;
}
?>
