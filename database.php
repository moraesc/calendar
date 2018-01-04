<?php

$mysqli = new mysqli('localhost', 'camillamoraes', 'lr97lr', 'calendar');

if($mysqli->connect_errno) {
        printf("Connection Failed: $s\n", $mysqli->connect_error);
        exit;
}
?>
