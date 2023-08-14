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
 .grid-container {
     display: grid;
     grid-template-columns: repeat(4, 1fr);
     grid-template-rows: repeat(2, 1fr);
     gap: 5vh;
	 filter: drop-shadow(14px 21px 22px black);
}
 .grid-item{
     background-color: #cccccc69;
     text-align: center;
     aspect-ratio: 1/1;
     border-radius: 25px;
	 background-repeat: no-repeat;
	background-position: center;
	background-size: cover;
	

}

.grid-item > div{
	align-items: center;
display: flex;
justify-content: center;
	width:100%;
	height:100%;
}

/* Media query for phones */
 @media screen and (orientation: portrait) {
     .grid-container {
         grid-template-columns: repeat(1, 1fr);
         grid-template-rows: repeat(8, 1fr);
    }
	
	.grid-item{
		width:75%;
		margin:auto;
	}
	
     .grid-face{
         margin: 0% 20% 0% 20%;
    }
     h2{
         font-size: 8vw;
		 margin:0px;
    }
     h3{
         font-size: 4vw;
    }
    .grid-face > a{
         font-size: 8vw;
    }
	h1{
		font-size: 3vh;
	}
	label{
		font-size: 2vh;
	}
	input, button, p{
		font-size:3vh;
	}
	.input-container{
		margin-top: 1vh;
		margin-bottom: 1vh;
	}
	p,a,h1,h2,h3,label{
	text-shadow: -2px -2px 0 #000, 2px -2px 0 #000, -2px 2px 0 #000, 2px 2px 0 #000;
}
}
 dialog{
     position: fixed;
     top: 50%;
     transform: translate(0px, -50%);
     background-color: rgba(0, 0, 0, 0.346);
     color: white;
	 z-index:1;
}
 #ref{
     width: 100%;
     height: 100%;
     position: fixed;
     top: 0px;
     left:0px;
}

.grid-face > a{
     color: white;
     border: white 5px solid;
	 outline: black 2px solid;
     text-decoration: none;
     bottom: 0px;
     position: relative;
}


 @media screen and (orientation: landscape) {
    .grid-item > a{
         font-size: 3vh;
    }
	h2{
		font-size: 2.5vw;
		margin:0px;
	}
	h3{
		font-size: 1.5vw;
	}
	p,a,h1,h2,h3,label{
	text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
}
}
<?php echo file_get_contents('navbar.css'); ?>
    </style>
	<title>Image Sharing Website</title>
    <meta content="IMGshare" property="og:title" />
	<meta name="description" content="Share your images here">    
    <meta content="Current category: <?php echo $_GET['category'];?>" property="og:description" />
    <meta content="#43B581" data-react-helmet="true" name="theme-color" />
</head>
<body>
<?php include 'navbar.php'; 
?>
<h1 style="text-align:center;">Select a category</h1>	
	<div class="grid-container">
	<?php
	$ip = $_SERVER['REMOTE_ADDR'];
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	$jsonData = file_get_contents("categories.json");
	$categories = json_decode($jsonData, true);

	foreach ($categories as &$category) {
		$categoryName = $category['name'];
		$categoryData = json_decode(file_get_contents("categories/" . $categoryName . ".json"), true);

		$imageCount = 0;
		foreach($categoryData as $item){
			if($item['url'] != 'deleted'){
				$imageCount += 1;
			}
		}
		
		// Add the image count to the category
		$category['imageCount'] = $imageCount;
	}

	// Sort the categories by image count in descending order
	usort($categories, function ($a, $b) {
		return $b['imageCount'] - $a['imageCount'];
	});
	
	function getRandomImageUrl($categoryName) {
    $categoryData = json_decode(file_get_contents("categories/" . $categoryName . '.json'), true);
    $filteredData = array_filter($categoryData, function($item) {
        return strpos($item['url'], 'deleted') === false;
    });

    if (count($filteredData) === 0) {
        return null; // No available images after filtering
    }

    $randomIndex = rand(0, count($filteredData) - 1);
    return $filteredData[$randomIndex]['url'];
}

	
	foreach ($categories as $item) {
		if(array_key_exists("locked",$item) && $item["locked"]){
			if($account !== null){
				if(($account->invites) >= $item["invite-level"]){
					$cnt = $item['imageCount'];
			echo '<div class="grid-item" style="background-image: url(&quot;'. encodeImageUrl(getRandomImageUrl($item['name'])) .'&quot;)"><div><div class="grid-face" style="display:block; position:relative;">';
			echo "<h2>" . $item['title'] . "</h2>";
			echo "<h3>" . $cnt . " Images." . "</h3>";
			echo '<a href="javascript:if(window.location.search.startsWith(\'?\')){window.location.href = \'/viewer.php\' +window.location.search +\'&category='.$item['name'].'\';}else{window.location.href = \'/viewer.php?category='.$item['name'].'\';}'.$item['name'].'">Continue</a>';
			echo '</div></div></div>';
				}else{
					$cnt = $item['imageCount'];
					echo '<div class="grid-item" style="background-image: url(&quot;'. encodeImageUrl(getRandomImageUrl($item['name'])) .'&quot;)"><div style="backdrop-filter: blur(6px);"><div class="grid-face" style="display:block; position:relative;">';
					echo "<h2 style=\"color:red;\">LOCKED!</h2>";
					echo "<h2>" . $item['title'] . "</h2>";
					echo "<h3>" . $cnt . " Images." . "</h3>";
					echo '<a href="javascript:navigator.clipboard.writeText(new URL(window.location.href).origin+\'/index.php/?ref='.$account->id.'\')">Copy Invite Link</a>';
					echo "<p style=\"display:block;\"> You have ".$account->invites." out of ".$item["invite-level"]." required invites";
					echo '</div></div></div>';
				}
				
			}else{
				$cnt = $item['imageCount'];
				echo '<div class="grid-item" style="background-image: url(&quot;'. encodeImageUrl(getRandomImageUrl($item['name'])) .'&quot;)"><div style="backdrop-filter: blur(6px);"><div class="grid-face" style="display:block; position:relative;">';
				echo "<h2 style=\"color:red;\">LOCKED!</h2>";
				echo "<h2>" . $item['title'] . "</h2>";
				echo "<h3>" . $cnt . " Images." . "</h3>";
				echo '<a href="javascript:window.location.href = \'/signup.php\' + window.location.search">Login to Unlock</a>';
				echo '</div></div></div>';
			}
		}else{
			
			$cnt = $item['imageCount'];
			echo '<div class="grid-item" style="background-image: url(&quot;'. encodeImageUrl(getRandomImageUrl($item['name'])) .'&quot;)"><div><div class="grid-face" style="display:block; position:relative;">';
			echo "<h2>" . $item['title'] . "</h2>";
			echo "<h3>" . $cnt . " Images." . "</h3>";
			echo '<a href="javascript:if(window.location.search.startsWith(\'?\')){window.location.href = \'/viewer.php\' +window.location.search +\'&category='.$item['name'].'\';}else{window.location.href = \'/viewer.php?category='.$item['name'].'\';}'.$item['name'].'">Continue</a>';
			echo '</div></div></div>';
		}
	}
	
	?>

    </div>
<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
if(window.location.href.include("&category") || window.location.href.include("?category")){
var obj = new URL(window.location.href)
obj.searchParams.delete("category")
obj.searchParams.delete("seed")
obj.searchParams.delete("sort")
obj.searchParams.delete("page")
window.location.href = obj.origin + obj.pathname + obj.search}
</script>
</body>
</html>
