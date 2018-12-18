<?php
header("Content-Type: application/json");

$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:
$name = htmlentities($json_obj['name']);
$location = htmlentities($json_obj['location']);
$datetime = htmlentities($json_obj['datetime']);
$username = htmlentities($json_obj['username']);
$password = htmlentities($json_obj['password']);
$category = htmlentities($json_obj['category']);




$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'Calendar');
if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}

$stmt = $mysqli->prepare("SELECT COUNT(*), userID, password FROM users WHERE username=?");

//// Bind the parameter
$stmt->bind_param('s', $username);
$stmt->execute();

$stmt->bind_result($cnt, $userID, $pwd_hash);
$stmt->fetch();
$stmt->close();

$pwd_guess = $password;


if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
	$stmt = $mysqli->prepare("INSERT INTO events (userID, name, location, time, category) VALUES (?, ?, ?, ?, ?)");

	// Bind the parameter
	$stmt->bind_param('issss', $userID, $name, $location, $datetime, $category);
	$stmt->execute();
	$stmt->close();
}
	// Compare the submitted password to the actual password hash


//enconde it as a json array. then create a JS variable, allows u to parse
echo json_encode(array(
		$name, $location
	));
