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

$usernames = array();

$stmt = $mysqli->prepare("select username from users;");
if(!$stmt) {
        printf("Query Prep Failed: :(", $mysqli->error);
        exit;
}

$stmt->execute();
$result = $stmt->get_result();

//push variables onto array
while($row = $result->fetch_assoc()) {
        array_push($usernames, htmlentities($row['username']));
}

$stmt->close();

echo json_encode($usernames); //you'll get an array of all the stuff on the other end

?>
