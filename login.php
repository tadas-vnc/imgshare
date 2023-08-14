<!DOCTYPE html>
<html>
<head>
<style>
body{
	margin:0px;
	background-color: rgba(0,0,0,0.8);
	color: white;
	font-family: Roboto,'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
}

@media screen and (orientation: portrait) {
	#signup{
		position: relative;
		width: 90%;
	}
	
	
	#email, #password, #username{
		font-size: 3.5vh;
	}
	input[type="submit"]{
		font-size:3.5vh;
	}
	
	#adleft{
	position: absolute;
	top: 50%;
	left:10%;
}

#adright{
	position: absolute;
	top: 50%;
	right:10%;
}
}
@media screen and (orientation: landscape) {
	#signup{
		width: 25%;
	}
	#adleft{
	position: absolute;
	top: 50%;
	transform: translate(0px, -50%);
	left:10%;
}

#adright{
	position: absolute;
	top: 50%;
	transform: translate(0px, -50%);
	right:10%;
}
	
}

#signup{
	position: relative;
	margin: auto;
	heigth: 10%;
	text-align: center;
	padding: 5px;
	border-radius: 5px;
	background-color: #6a6a6a;
	font-size: 2vh;
}



#email, #password, #username{
	width:100%;
	box-sizing: border-box;
	margin-right: 5px;
	margin-top: 1vh;
	margin-bottom: 1vh;
	padding: 0.5vh;
}

h1{
	margin: 1vh 0px 1vh 0px;
}

 dialog{
     position: fixed;
     top: 50%;
     transform: translate(0px, -50%);
     background-color: rgba(0, 0, 0, 0.346);
     color: white;
	 z-index:1;
}


<?php echo file_get_contents("navbar.css"); ?></style>
</head>
<body>
<?php include "navbar.php";
function dialog($message){
	echo base64_decode("PGRpYWxvZyBvcGVuPjxwIHN0eWxlPSJkaXNwbGF5OiBibG9jazsiPg=="). $message . base64_decode("PC9wPjxkaXYgc3R5bGU9IndpZHRoOjEwMCU7IGRpc3BsYXk6IGZsZXg7IGp1c3RpZnktY29udGVudDpzcGFjZS1ldmVubHk7IiI+PGJ1dHRvbiBvbmNsaWNrPSJkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdkaWFsb2cnKS5yZW1vdmVBdHRyaWJ1dGUoJ29wZW4nKSI+Q2xvc2U8L2J1dHRvbj48L2Rpdj48L2RpYWxvZz4=");
}
?>
<div id="signup">
	<h1>Login</h1>
	<form method="POST">
		<label for="username">Username</label>
		<input type="text" name="username" id="username" placeholder="Enter username..." />
		
		<label for="password">Password</label>
		<input type="password" name="password" id="password" placeholder="****" />
		<input style="display:block; margin: auto;" type="submit" name="submit" id="submit" value="Login">
		<p>Don't have an account yet? <a href="javascript:window.location.href = '/signup.php' + window.location.search">Sign Up!</a></p>
	</form>
</div>

<?php


if(isset($_COOKIE['token'])){
	if(getItemByKey(json_decode(file_get_contents("accounts.json")),$_COOKIE['token'],"token") !== null){
		echo "<script>window.location.href = '/index.php' + window.location.search</script>";
	}
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_POST["submit"])){
		$username = $_POST["username"];
		$password = $_POST["password"];
		$accounts = json_decode(file_get_contents("accounts.json"));
		
		$account = getItemByKey($accounts, $username, 'username', false);
		if ($account !== null) {
			if ($account->password == $password) {
				setcookie('token', $account->token, 0, '/');
				echo "<script>window.location.href = '/profile.php' + window.location.search</script>";
			} else {
				dialog("Incorrect password.");
			}
		} else {
			dialog("No accounts were found with this username.");
		}

	}
}

?>


<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>
</body>
</html>