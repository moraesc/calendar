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
// header("Content-Type: application/json");

//if entries are valid
$valid_entry=true;
if(empty($_POST['newTitle']) || empty($_POST['newYear']) || empty($_POST['newMonth'])
|| empty($_POST['newDay']) || empty($_POST['newTime']) || empty($_POST['newType'])) {
	$valid_entry=false;
}
if(empty($_POST['oldTitle']) || empty($_POST['oldYear']) || empty($_POST['oldMonth'])
|| empty($_POST['oldDay']) || empty($_POST['oldTime']) || empty($_POST['oldType'])) {
	$valid_entry=false;
}
//entry not valid
if(!$valid_entry) {
	echo json_encode(array("success" => false));
    	exit; 
}
else {
	$stmt = $mysqli->prepare("update events set title=?, year=?, month=?, day=?, time=?, type=?, recurring=? where title=? and year=? and month=? and day=? and time=? and type=? and recurring=? and owner_id=?;");
	if(!$stmt) {
		printf("Query Prep Failed: :(", $mysqli->error);
    		exit;
	}

	//new values
	$newTitle = $_POST['newTitle'];
	$newYear = $_POST['newYear'];
	$newMonth = $_POST['newMonth'];
	$newDay = $_POST['newDay'];
	$newTime = $_POST['newTime'];
	$newType = $_POST['newType'];
  	$newRecurring = $_POST['newRecurring'];

	//old values to locate the event in the table
	$oldTitle = $_POST['oldTitle'];
	$oldYear = $_POST['oldYear'];
	$oldMonth = $_POST['oldMonth'];
	$oldDay = $_POST['oldDay'];
	$oldTime = $_POST['oldTime'];
	$oldType = $_POST['oldType'];
  	$oldRecurring = $_POST['oldRecurring'];
	$owner_id = $_SESSION['user_id'];

	$stmt->bind_param('siiissssiiisssi', $newTitle, $newYear, $newMonth, $newDay, $newTime, $newType, $newRecurring, $oldTitle, $oldYear, $oldMonth, $oldDay, $oldTime, $oldType, $oldRecurring, $owner_id);

	$stmt->execute();
	$stmt->close();

	echo json_encode(array(
        	"success" => true
        ));
        exit;
}
?>
