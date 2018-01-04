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

$valid_entry=true;
$error_message="";
//title, time, or date field empty
if(empty($_POST['title']) || empty($_POST['time']) || empty($_POST['type']) ){
	$valid_entry=false;
	$error_message="Please enter a title.";
}
//date field empty
if(empty($_POST['year']) || empty($_POST['month']) || empty($_POST['day']) ){
	$valid_entry=false;
	$error_message="Please enter a date.";
}

//entry not valid
if($valid_entry == false) {
	echo json_encode(array(
      		"success" => false,
	        "message" => $error_message
       	));
        exit;
}
else {

	$stmt = $mysqli->prepare("delete from events where title=? and year=?
  and month=? and day=? and time=? and type=? and recurring=? and owner_id=?;");

	if(!$stmt) {
    		printf("Query Prep Failed: :(", $mysqli->error);
    		exit;
	}

	$title = $_POST['title'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$time = $_POST['time'];
  	$type = $_POST['type'];
  	$recurring = $_POST['recurring'];
	$owner_id = $_SESSION['user_id'];

	$stmt->bind_param('siiisssi', $title, $year, $month, $day, $time, $type, $recurring, $owner_id);
	$stmt->execute();
	$stmt->close();

	echo json_encode(array(
                "success" => true
        ));
        exit;

}
?>
