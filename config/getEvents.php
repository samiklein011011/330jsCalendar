<?php
// login_ajax.php

// require('/home/samiKlein330/public_html/config/init.php');
// require('/Users/samiklein/Desktop/cse330/fall2018-module5-group-441979-448567/config');


$stmt = $mysqli->prepare("SELECT * FROM events WHERE EXTRACT(YEAR_MONTH FROM date)=? AND userID = ?");

// Bind the parameter
$stmt->bind_param('ii', 201808, 1);
	// $user = $_POST['username'];
$stmt->execute();

	// Bind the results
$stmt->bind_result($eventID, $userID, $name, $loc, $time, $date);
$stmt->fetch();

	// Compare the submitted password to the actual password hash


//enconde it as a json array. then create a JS variable, allows u to parse
echo json_encode(array(
		"success" => true
	));


// header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
//
// //Because you are posting the data via fetch(), php has to retrieve it elsewhere.
// $json_str = file_get_contents('php://input');
// //This will store the data into an associative array
// $json_obj = json_decode($json_str, true);
//
// //Variables can be accessed as such:
// $username = $json_obj['username'];
// $password = $json_obj['password'];
// //This is equivalent to what you previously did with $_POST['username'] and $_POST['password']
//
// // Check to see if the username and password are valid.  (You learned how to do this in Module 3.)
//
// if( /* valid username and password */ ){
// 	session_start();
// 	$_SESSION['username'] = $username;
// 	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
//
// 	echo json_encode(array(
// 		"success" => true
// 	));
// 	exit;
// }else{
// 	echo json_encode(array(
// 		"success" => false,
// 		"message" => "Incorrect Username or Password"
// 	));
// 	exit;
// }
//
//
