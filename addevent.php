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

//check if entries are valid
$valid_entry=true;
$error_message="";
//title, time, or date field empty
if(empty($_POST['title']) || empty($_POST['time']) || empty($_POST['type']) ){
	$valid_entry=false;
	$error_message="Please fill out all fields.";
}
//date field empty
if(empty($_POST['year']) || empty($_POST['month']) || empty($_POST['day']) ){
	$valid_entry=false;
	$error_message="Please fill out all fields.";
}

if(empty($_POST['recurring'])) {
	$valid_entry=false;
	$error_message="Please fill out all fields.";
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
	$stmt = $mysqli->prepare("insert into events (title, year, month, day, time, type, recurring, owner_id) values (?, ?, ?, ?, ?, ?, ?, ?);");

	if(!$stmt) {
  	printf("Query Prep Failed: :C", $mysqli-> error);
    	exit; }

	$title = $_POST['title'];
        $year = $_POST['year'];
        $month = $_POST['month'];
        $day = $_POST['day'];
        $time = $_POST['time'];
        $type = $_POST['type'];
	$recurring = $_POST['recurring'];
        $owner_id = $_SESSION['user_id'];

	//add recurring to statement
	$stmt->bind_param('siiisssi', $title, $year, $month, $day, $time, $type, $recurring, $owner_id);
	$stmt->execute();
	$stmt->close();

  	echo json_encode(array(
  		"success" => true
        ));
        exit;
}
?>
