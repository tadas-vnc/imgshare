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
	#accinfo{
		width: 80%;
		font-size: 2vh;
	}
	button, p, a, input{
		font-size: 2vh;
	}
}

#accinfo{
	background: #1f1f1f;
    margin: auto;
    text-align: center;
    border-radius: 5px;
}

@media screen and (orientation: landscape) {
	#accinfo{
		
    width: 35%;
	}
}

#accinfo > p{
	display: block;
	margin: 0px;
}



#logoutButton{
	margin:auto;
	display:block;
}
#pfp{
	object-fit: cover;
	aspect-ratio:1/1;
	    width: 80%;
    border-radius: 50%;
}
<?php echo file_get_contents("navbar.css"); ?></style>
</head>
<body>
<?php include "navbar.php";

$noaccountselected = (!isset($_GET['id']) && !isset($_GET['user']));
function dialog($message){
	echo base64_decode("PGRpYWxvZyBvcGVuPjxwIHN0eWxlPSJkaXNwbGF5OiBibG9jazsiPg=="). $message . base64_decode("PC9wPjxkaXYgc3R5bGU9IndpZHRoOjEwMCU7IGRpc3BsYXk6IGZsZXg7IGp1c3RpZnktY29udGVudDpzcGFjZS1ldmVubHk7IiI+PGJ1dHRvbiBvbmNsaWNrPSJkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdkaWFsb2cnKS5yZW1vdmVBdHRyaWJ1dGUoJ29wZW4nKSI+Q2xvc2U8L2J1dHRvbj48L2Rpdj48L2RpYWxvZz4=");
}
if(isset($_COOKIE["token"]))
{
	$accounts = json_decode(file_get_contents("accounts.json"));
	$account = getItemByKey($accounts, $_COOKIE["token"], 'token', false);
	if($account === null && $noaccountselected){
		echo "<script>window.location.href = '/login.php' + window.location.search</script>";
	}else{
		if(isset($_GET['alt'])){
			if($_GET['alt'] == 'true'){
				dialog("Alternative account has been detected, this invite will not be counted for user you got invited by.");
			}
		}
	}
}else{
	if($noaccountselected){
	echo "<script>window.location.href = '/login.php' + window.location.search</script>";
	}
}

include "upload.php";

	if(!$noaccountselected){
		if(isset($_GET['id'])){
			$selaccount = getItemByKey($accounts, $_GET["id"], 'id', false);
		}else if(isset($_GET['user'])){
			$selaccount = getItemByKey($accounts, $_GET["user"], 'username', false);
		}
	}else{
		$selaccount = $account;
	}
	
	
	if(isset($_FILES['file'])) {
    $allowedTypes = array('image/png', 'image/jpeg', 'image/gif', 'image/webp');
    $maxFileSize = 1 * 1024 * 1024; // 1MB in bytes

    if ($_FILES['file']['error'] === 0) {
        $fileType = $_FILES['file']['type'];
        $fileSize = $_FILES['file']['size'];

        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize && isset($_COOKIE['token'])) {
            // File is valid, perform further actions
            // Move the uploaded file to a permanent location, etc.
           $attachmentUrl = sendFileToDiscord($_FILES['file'], $webhookUrl, $botToken);
			$accounts = json_decode(file_get_contents("accounts.json"));
			$accountId = $_COOKIE["token"]; 
			$foundAccount = null;

			foreach ($accounts as &$account) {
				if ($account->token === $accountId) {
					$account->avatar_url = $attachmentUrl;
					$foundAccount = $account;
					break;
				}
			}
			file_put_contents('accounts.json', json_encode($accounts));
        } else {
            // Invalid file type or size
            dialog("Invalid file. Please upload a file under 1MB and in one of the following formats: PNG, JPG, GIF, JPEG, or WEBP.");
        }
    } else {
        // Error occurred during file upload
        dialog("Error uploading file. Please try again.");
    }
}

?>
<div id="accinfo"><h1>Account Info</h1><img id="pfp" src="<?php echo encodeImageUrl($selaccount->avatar_url)?>">
<?php
if($noaccountselected && null !== $account){
echo '<form action="" method="POST" enctype="multipart/form-data">
    <label for="file">Change Profile Picture</label>
	<input id="file" type="file" name="file" accept=".jpg, .png, .gif, .webp,  .jpeg" required>
    <input type="submit" id="submit" value="Upload">
</form>';
}
?>
<p>Username: <?php echo $selaccount->username ?></p>
<p>Invites: <?php echo $selaccount->invites?></p>
<p>Invite Link: <a href="<?php echo 'javascript:navigator.clipboard.writeText(new URL(window.location.href).origin+\'/signup.php/?ref='.$selaccount->id.'\')'; ?>"><?php echo $_SERVER['HTTP_HOST'].'/signup.php/?ref='.$selaccount->id ?></a></p>
<p>Created at: <?php echo date('Y-m-d H:i:s', $selaccount->created);?></p>
<?php 
if($noaccountselected && null !== $account){ 
echo '<button id="logoutButton" onclick="document.cookie=\'token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;\'; window.location.reload();">Logout</button>';
}
?>
</div>
<div id="dialogs"></div>
<script>
function dialog(post){
	var dialoghtml = "<dialog open><p style=\"display: block;\">"+post+"</p><div style=\"width:100%; display: flex; justify-content:space-evenly;\"><button onclick=\"this.parentNode.parentNode.remove()\">Close</button></div></dialog>";
	document.querySelector("#dialogs").innerHTML += dialoghtml;
	
}
// Get references to the file input and image elements
const uploadFileInput = document.getElementById('file');
const uploadedImage = document.getElementById('pfp');
const submit = document.getElementById('submit');

uploadFileInput.addEventListener('change', function(event) {
  const file = event.target.files[0];

  if (file) {
    if (file.size > 1000000) {
      dialog('File size exceeds the maximum limit of 1MB.');
	  submit.setAttribute('disabled', 'true');
      return;
    }
	submit.removeAttribute('disabled');
    const reader = new FileReader();	

    reader.onload = function(e) {
      uploadedImage.src = e.target.result;
    };

    reader.readAsDataURL(file);
  }
});

</script>
</body>
</html>