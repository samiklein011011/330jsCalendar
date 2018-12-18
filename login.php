<?php

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);


//Variables can be accessed as such:
//escaping input
$username = htmlentities($json_obj['username']);
$password = htmlentities($json_obj['password']);


// This is equivalent to what you previously did with $_POST['username'] and $_POST['password']

// Check to see if the username and password are valid.  (You learned how to do this in Module 3.)

//connect to DATABASE

//COOKIE
ini_set("session.cookie_httponly", 1);
session_start();
$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];

if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
	die("Session hijack detected");
}
else{
	$_SESSION['useragent'] = $current_ua;
}

$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'Calendar');
if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}

// login_ajax.php

$stmt = $mysqli->prepare("SELECT COUNT(*), userID, password FROM users WHERE username=?");

//// Bind the parameter
$stmt->bind_param('s', $username);
$stmt->execute();

$stmt->bind_result($cnt, $userID, $pwd_hash);
$stmt->fetch();
$stmt->close();



$pwd_guess = $password;
//Compare the submitted password to the actual password hash
if($userID != 6){
	if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
	// Login succeeded!
	$_SESSION['userID'] = $userID;
	// Redirect to your target page
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); //CSFR


	$stmt = $mysqli->prepare("SELECT eventID, name, location, Month(time) as month, Day(time) as day, Year(time) as year, cast(time as time) as time, category, userID FROM events WHERE userID = ?");
	$stmt->bind_param('i', $userID);
	$stmt->execute();

	$result = $stmt->get_result();
	$events = array();
	while ($row = $result->fetch_assoc()) {
		//XSS
		$events[$row['eventID']] = array('name'=>htmlentities($row['name']), 'location'=>htmlentities($row['location']), 'month'=>htmlentities($row['month']), 'day'=>htmlentities($row['day']), 'year'=>htmlentities($row['year']),
		'time'=>htmlentities($row['time']), 'category'=>htmlentities($row['category']), 'userID'=>htmlentities($row['userID']), 'eventID'=>htmlentities($row['eventID']));
	}
	echo json_encode(
		$events
	// 	array(
	// 	// "events"=>$events, //call data.events to get this on JS side
	// 	// "userID"=>$userID
	// )
);
		//something like an array
		//need a key for events

} else{

	echo json_encode(array(
		"success" => false,
		"message" => $pwd_guess
	));
	exit;
	// echo "<div style='color:red'>**Log in failed**</div>";
}

}else{

	if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
	// Login succeeded!
	$_SESSION['userID'] = $userID;
	// Redirect to your target page
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); //CSFR


	$stmt = $mysqli->prepare("SELECT eventID, name, location, Month(time) as month, Day(time) as day, Year(time) as year, cast(time as time) as time, category FROM events");
	//$stmt->bind_param('i', $userID);
	$stmt->execute();

	$result = $stmt->get_result();
	$events = array();
	while ($row = $result->fetch_assoc()) {
		//XSS
		$events[$row['eventID']] = array('name'=>htmlentities($row['name']), 'location'=>htmlentities($row['location']), 'month'=>htmlentities($row['month']), 'day'=>htmlentities($row['day']), 'year'=>htmlentities($row['year']),
		'time'=>htmlentities($row['time']), 'category'=>htmlentities($row['category']), 'userID'=>htmlentities($row['userID']), 'eventID'=>htmlentities($row['eventID']));
	}
	echo json_encode(
		$events
	// 	array(
	// 	// "events"=>$events, //call data.events to get this on JS side
	// 	// "userID"=>$userID
	// )
);
		//something like an array
		//need a key for events

} else{

	echo json_encode(array(
		"success" => false,
		"message" => $pwd_guess
	));
	exit;
	// echo "<div style='color:red'>**Log in failed**</div>";
}

}
