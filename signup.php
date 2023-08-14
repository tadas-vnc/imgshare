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
	<h1>Sign Up</h1>
	<form method="POST">
		<label for="username">Username</label>
		<input type="text" name="username" id="username" pattern="^[A-Za-z0-9_]{2,16}$" placeholder="Enter username..." />

		<label for="email">E-mail</label>
		<input type="email" name="email" id="email" placeholder="Enter E-mail..." />
		
		<label for="password">Password</label>
		<input type="password" name="password" id="password" placeholder="****" />
		<input style="display:block; margin: auto;" type="submit" name="submit" id="submit" value="Create account">
		<p>Already have an account? <a href="javascript:window.location.href = '/login.php' + window.location.search">Login...</a></p>
	</form>
</div>

<?php

function addAccount($username, $email, $password, $token, $ref = null)
{
    // Read existing data from "accounts.json"
    $jsonData = file_get_contents('accounts.json');
    $dataArray = json_decode($jsonData, true);

    // Generate a new account object
    $newAccount = [
        "id" => count($dataArray),
        "username" => $username,
        "email" => $email,
        "password" => $password,
        "created" => time(), // Using the current timestamp
        "posts" => [],
		"token" => $token,
		"invites" => 0,
		"avatar_url" => "https://cdn.discordapp.com/attachments/1102677795479498863/1118952133736280134/default-profile-picture1-3713618199.jpg",
		"ip" => $_SERVER['REMOTE_ADDR'],
		"invited_by" => $ref
    ];
	
	
    // Add the new account to the existing array
    $dataArray[] = $newAccount;

    // Write the updated array back to "accounts.json"
    file_put_contents('accounts.json', json_encode($dataArray));

    // Return the new account object
    return $newAccount;
}



include "webhook_send.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_POST["submit"])){
		$username = $_POST["username"];
		$email = $_POST["email"];
		$password = $_POST["password"];
		if(strlen($username) > 1 && strlen($username) < 16){
			if(strlen($password) < 16){
				if(strlen($email) < 64){
					$accounts = json_decode(file_get_contents("accounts.json"));
					if(getItemByKey($accounts, $username,"username", false) !== null){
						dialog("Username is already taken.");
					}else{
						$token = str_replace('=','',base64_encode(random_int(0,9999999999999999).random_int(0,9999999999999999)));
						
						$add = '';
						if(isset($_GET['ref'])){
							$accounts = json_decode(file_get_contents("accounts.json"));
							$accountId = (int)$_GET['ref']; // The ID of the account you want to update
							
							$foundAccount = null;
							if(getItemByKey($accounts, $_SERVER['REMOTE_ADDR'], "ip") !== null){
								$add = "&alt=true";
							}else{

									foreach ($accounts as &$account) {
									if ($account->id === $accountId) {
										$account->invites += 1; 
										$foundAccount = $account;
										break;
									}
								}
								file_put_contents('accounts.json', json_encode($accounts));
							}
							
						}
						if(isset($_GET['ref'])){
							$newaccount = addAccount($username, $email, $password, $token, (int)$_GET['ref']);
						}else{
							$newaccount = addAccount($username, $email, $password, $token);
						}
						setcookie('token', $token, 0, '/');
						echo "<script>window.location.href = '/profile.php' + window.location.search +'". $add ."'</script>";
					}
				}else{
					dialog("E-mail has to be under 64 character.");
				}
			}else{
				dialog("Password has to be under 16 characters.");
			}
		}else{
			dialog("Username must be between 2 and 16 characters long");
		}
	}
}
if(isset($_COOKIE['token'])){
	if(getItemByKey(json_decode(file_get_contents("accounts.json")),$_COOKIE['token'],"token") !== null){
	echo "<script>window.location.href = '/index.php' + window.location.search</script>";
	}
}
?>

?>
<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>
</body>
</html>