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

p{
	text-align: center;
}
<?php include "navbar.css";?>
</style>
</head>
<body>
	<?php include "navbar.php"; 
	if(isset($_GET['post'])){
		if(isset($account)){
			$ids = explode(":", $_GET['post']);
			$category = json_decode(file_get_contents("categories/". $ids[0].".json"));
			$imgobj = getItemByKey($category, (int)$ids[1], "id");
			if($account->id == 0 || $account->id == $imgobj['poster']){
				foreach($category as &$item){
					if($item->id == (int)$ids[1]){
						$item->url = "deleted";
						break;
					}
				} 
				file_put_contents("categories/".$ids[0].".json", json_encode($category));
				echo "<p>your post has been deleted successfully.</p>";
			}else{
				echo "<p>you can only delete posts that you own</p>";
			}
		}else{
			echo "<p>if you want to delete something, you have to be logged in</p>";
		}
	}else{
		echo "<p>I have no idea what you are trying to delete.</p>";
	}
	?>
</body>
</html>