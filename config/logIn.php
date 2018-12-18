<?php

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:
$username = $json_obj['username'];
$password = $json_obj['password'];
//
//
//
//
////This is equivalent to what you previously did with $_POST['username'] and $_POST['password']
//
//// Check to see if the username and password are valid.  (You learned how to do this in Module 3.)
//
//
//
////connect to DATABASE
session_start();
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
//password_verify($pwd_guess, $pwd_hash)
if($cnt == 1 && $pwd_hash==$password){
	// Login succeeded!
	$_SESSION['userID'] = $userID;
	// Redirect to your target page
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); //CSFR
		

	$stmt = $mysqli->prepare("SELECT eventID, name, location, Month(time) as month, Day(time) as day, cast(time as time) as time FROM events WHERE userID=?");
	$stmt->bind_param('i', $userID);
	$stmt->execute();

	$result = $stmt->get_result();
	$events = array();
	while ($row = $result->fetch_assoc()) {
		$events[$row['eventID']] = array('name'=>$row['name'], 'location'=>$row['location'], 'month'=>$row['month'], 'day'=>$row['day'], 'time'=>$row['time']);
	}
	echo json_encode(
		$events);	

} else{

	echo json_encode(array(
		"success" => false,
		"message" => $pwd_guess        
	));
	exit;
	// echo "<div style='color:red'>**Log in failed**</div>";
}




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


// echo "
// 	<p style='font-size:18px'>Log In:</p>
// 	<form method='post'>
// 		Username: <input type='text' name='Username' value='".@$_REQUEST['Username']."' placeholder='username'/><br />
// 		Password: <input type='password' name='Password' value='".@$_REQUEST['Password']."' placeholder='password' id='myInput'/><br />
// 		<input type='checkbox' onclick='togglePasswordVisibility()'>Show Password
// 		<input type='hidden' name='token' value='";echo $_SESSION['token']; echo"' />
// 		<br>
// 		<input type='submit' name='logIn' value='Sign In' />
// 	</form>
// ";
