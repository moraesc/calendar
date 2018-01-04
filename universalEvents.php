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

$stmt = $mysqli->prepare("insert into events (title, year, month, day, time, type, recurring, owner_id)
values ('Halloween', 1911, 9, 31, '00:00:00', 'Other', 'yes', ?);");

if(!$stmt) {
	printf("Query Prep Failed: :C", $mysqli-> error);
  	exit; 
}

$owner_id = $_SESSION["user_id"];
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$stmt->close();

$stmt = $mysqli->prepare("insert into events (title, year, month, day, time, type, recurring, owner_id)
values ('Thanksgiving', 1621, 10, 23, '00:00:00', 'Other', 'yes', ?);");

if(!$stmt) {
	printf("Query Prep Failed: :C", $mysqli-> error);
  	exit; 
}

$owner_id = $_SESSION["user_id"];
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$stmt->close();

$stmt = $mysqli->prepare("insert into events (title, year, month, day, time, type, recurring, owner_id)
values ('New Years Eve', 1000, 11, 31, '00:00:00', 'Other', 'yes', ?);");

if(!$stmt) {
	printf("Query Prep Failed: :C", $mysqli-> error);
  	exit; 
}

$owner_id = $_SESSION["user_id"];
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$stmt->close();

$stmt = $mysqli->prepare("insert into events (title, year, month, day, time, type, recurring, owner_id)
values ('New Years Day', 1000, 0, 1, '00:00:00', 'Other', 'yes', ?);");

if(!$stmt) {
	printf("Query Prep Failed: :C", $mysqli-> error);
  	exit; 
}

$owner_id = $_SESSION["user_id"];
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$stmt->close();

$stmt = $mysqli->prepare("insert into events (title, year, month, day, time, type, recurring, owner_id)
values ('Fourth of July', 1777, 6, 4, '00:00:00', 'Other', 'yes', ?);");

if(!$stmt) {
	printf("Query Prep Failed: :C", $mysqli-> error);
  	exit; 
}

$owner_id = $_SESSION["user_id"];
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$stmt->close();

 ?>
