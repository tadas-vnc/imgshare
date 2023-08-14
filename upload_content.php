<!DOCTYPE html>
<html>
<head>
<style>
 body{
     margin:0%;
     background-color: rgba(0, 0, 0, 0.8);
     color: white;
     font-family: Roboto,'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
}

[for="file"]{
	text-align:center;
	display: block;
	font-size: 3vh;
}

input, select, label{
	font-size:2vh;
	display: block;
}

@media screen and (orientation: portrait){
#pfp{
	    width: 100%;
    height: 100%;
    object-fit: contain;
}}

@media screen and (orientation: landscape){
#pfp{
	    width: 100%;
    height: 50vh;
    object-fit: contain;
}}
<?php echo file_get_contents("navbar.css") ?>
</style>
</head>
<body>

<?php include("navbar.php");
include("upload.php");
function dialog($message){
	echo base64_decode("PGRpYWxvZyBvcGVuPjxwIHN0eWxlPSJkaXNwbGF5OiBibG9jazsiPg=="). $message . base64_decode("PC9wPjxkaXYgc3R5bGU9IndpZHRoOjEwMCU7IGRpc3BsYXk6IGZsZXg7IGp1c3RpZnktY29udGVudDpzcGFjZS1ldmVubHk7IiI+PGJ1dHRvbiBvbmNsaWNrPSJkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdkaWFsb2cnKS5yZW1vdmVBdHRyaWJ1dGUoJ29wZW4nKSI+Q2xvc2U8L2J1dHRvbj48L2Rpdj48L2RpYWxvZz4=");
}
if($account !== null){
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$selectedcategory = null;
	if($_POST['selectInput'] == 'createown'){
		$pattern = '/^[A-Za-z0-9_]{2,16}$/';
		if (preg_match($pattern, $_POST['newname'])) {
			if(getItemByKey(json_decode(file_get_contents("categories.json"), true), $_POST['newname'], 'name', false) === null){
				if (preg_match('/^.{2,16}$/', $_POST['displayname'])){
					$file = 'categories.json';
					$data = file_get_contents($file);
					$array = json_decode($data, true);

					$newObject = array(
						"name" => $_POST["newname"],
						"title" => $_POST["displayname"],
						"created_by" => $account->id
					);

					$array[] = $newObject;

					$jsonData = json_encode($array);

					file_put_contents($file, $jsonData);
					file_put_contents("categories/".$_POST['newname'].".json", "[]");
					$selectedcategory = $_POST['newname'];
				}
			}else{
				dialog("Category that you are trying to create already exists.");
			}
		}
		
	}else{
		$pattern = '/^[A-Za-z0-9_-]{2,16}$/';
		if (preg_match($pattern, $_POST['selectInput'])) {
			if(getItemByKey(json_decode(file_get_contents("categories.json"), true), $_POST['selectInput'], 'name', false) !== null){
				$selectedcategory =$_POST['selectInput']; 
			}
		}
	}
	echo $selectedcategory;
	if(isset($_FILES['file']) && $selectedcategory !== null) {
		$allowedTypes = array('image/png','image/jpg','image/jpeg', 'image/gif', 'image/webp');
		$maxFileSize = 25 * 1024 * 1024; // 1MB in bytes

		if ($_FILES['file']['error'] === 0) {
			$fileType = $_FILES['file']['type'];
			$fileSize = $_FILES['file']['size'];

			if (in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize && isset($_COOKIE['token'])) {
			    $attachmentUrl = sendFileToDiscord($_FILES['file'], $webhookUrl, $botToken);
				$file = 'categories/'.$selectedcategory.'.json';
				$data = file_get_contents($file);
				$array = json_decode($data, true);
				$newID = count($array);
				$newObject = array(
					"url" => $attachmentUrl,
					"poster" => $account->id,
					"upvotes" => 0,
					"downvotes" => 0,
					"comments" => array(),
					"posted" => time(),
					"id" => $newID
				);

				$array[] = $newObject;

				$jsonData = json_encode($array);

				file_put_contents($file, $jsonData);
				$accounts = json_decode(file_get_contents("accounts.json"));
				foreach ($accounts as $acc) {
					if ($acc->id == $account->id) {
						if ($acc->posts == array()) {
							$acc->posts = (object)[];
						}
						$acc->posts->$selectedcategory[] = $newID;
						break;
					}
				}
				
				file_put_contents("accounts.json", json_encode($accounts));
			} else {
				// Invalid file type or size
				dialog("Invalid file. Please upload a file under 25MB and in one of the following formats: PNG, JPG, GIF, JPEG, or WEBP.");
			}
		} else {
			// Error occurred during file upload
			dialog("Error uploading file. Please try again.");
		}
	}
}}else{
	echo "<script>window.location.href = '/login.php' + window.location.search</script>";
}
?>
<form action="" method="POST" enctype="multipart/form-data">
    <label for="file">Upload Content</label>
	<input id="file" type="file" name="file" accept=".jpg, .png, .gif, .webp,  .jpeg" required>
	<img id="pfp" src="https://cdn.discordapp.com/attachments/1102677795479498863/1121433600903102585/image.png">
	<label for="selectInput">Select a category</label>
        <select id="selectInput" name="selectInput">
            <?php 
			$categories = json_decode(file_get_contents("categories.json"), true);
			foreach($categories as $category){
				echo '<option value="'.$category['name'].'">'.$category['title'].'</option>';
			} ?>
			<option value="createown">Create new Category</option>
        </select>
	<div id="newfield">
		
	</div>
    <input type="submit" id="submit" value="Upload">
</form>
<div id="dialogs"></div>
<script>
// Get references to the file input and image elements
const uploadFileInput = document.getElementById('file');
const uploadedImage = document.getElementById('pfp');
const submit = document.getElementById('submit');
function dialog(post){
	var dialoghtml = "<dialog open><p style=\"display: block;\">"+post+"</p><div style=\"width:100%; display: flex; justify-content:space-evenly;\"><button onclick=\"this.parentNode.parentNode.remove()\">Close</button></div></dialog>";
	document.querySelector("#dialogs").innerHTML += dialoghtml;
	
}
uploadFileInput.addEventListener('change', function(event) {
  const file = event.target.files[0];

  if (file) {
    if (file.size > 25000000) {
      dialog('File size exceeds the maximum limit of 25MB.');
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
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
document.querySelector("#selectInput").addEventListener("change", ()=>{
    if(document.querySelector("#selectInput").selectedOptions[0].value == "createown"){
        document.querySelector("#newfield").innerHTML += '<label for="newname">Name of new category:</label><input name="newname" type="text" pattern="^[A-Za-z0-9_]{2,16}$" title="Please enter a valid input (2-16 characters, English letters, numbers, and underscores)." required><label for="displayname">Display Name of new category:</label><input name="displayname" type="text" pattern="^.{2,16}$" title="Please enter a valid input (2-16 characters, English letters)." required>'
    }else{
        document.querySelector("#newfield").innerHTML = '';
    }
})
</script>
</body>
</html>