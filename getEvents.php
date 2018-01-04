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

$events = array();

$stmt = $mysqli->prepare("select title, day, time, type from events where owner_id=? and month=? and year=? and recurring=?");
if(!$stmt) {
        printf("Query Prep Failed: :(", $mysqli->error);
        exit;
}

$owner_id = $_SESSION['user_id'];
$month = $_POST['month'];
$year = $_POST['year'];
$recurring = $_POST['recurring'];

$stmt->bind_param('ssss', $owner_id, $month, $year, $recurring);
$stmt->execute();
$stmt->bind_result($title, $day, $time, $type);

//push variables onto array
while($stmt->fetch()) {
	array_push($events, array("title" => htmlentities($title), "day" => htmlentities($day),
  "time" => htmlentities($time), "type" => htmlentities($type)));
}

echo json_encode($events); //you'll get an array of all the stuff on the other end
?>
