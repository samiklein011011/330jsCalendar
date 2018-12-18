<?php
// Content of database.php

session_start();
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'Calendar');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>
