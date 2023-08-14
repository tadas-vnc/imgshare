<!DOCTYPE html>
<html>
<head>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: "Open Sans", sans-serif;
            background-color: rgb(69, 69, 69);
        }
		 @media screen and (orientation: landscape) {
        .imgbox {
            display: inline-block;
			width: 25%;
		}
		}
        .center-fit {
            max-width: 100%;
            max-height: 100vh;
            border-radius: 10px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 5px 10px;
            border: 1px solid #fff;
            color: #fff;
            text-decoration: none;
        }
        .pagination a.active {
            background-color: #fff;
            color: #000;
        }
		
		.pagination > a{
			font-size: 2vh;
			outline: white 3px solid;
		}
		
		#sorting{
			width:100%;
			display: flex;
			justify-content: center;
			color: white;
			font-size:2.5vh;
		}
		
		#sorting > select{
			
			font-size:2.5vh;
		}
		
		 @media screen and (orientation: portrait) {
			 .imgbox {
            display: flex;
			width: 100%;
			 justify-content: center;}
		}
		
		.wrapper{
			width:70%;
		}
		  dialog{
         position: fixed;
    top: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(0, 0, 0, 0.346);
    color: white;
    z-index: 1;
    font-size: 3vh;
    left: 50%;
}		.link2profile{
	color: white;
    top: 0.5vh;
    font-size: 2vh;
    position: relative;
}

.pfp{
	width: 10%;
    border-radius: 50%;
    object-fit: cover;
    aspect-ratio: 1/1;
}

		dialog > p, dialog > div > button {
			font-size: 3vh;
		}
		.delete{
			color: white;
			background: red;
			padding: 5px;
			border-radius: 5px;
			bottom: 4px;
			font-size: 2vh;
			position: inherit;
			height: fit-content;
			float: right;
		}
		.pagination{
			margin-top:10px;
			margin-bottom:10px;
		}<?php echo file_get_contents("navbar.css");?>
    </style>
	<title>Image Sharing Website</title>
    <meta content="IMGshare" property="og:title" />
	<meta name="description" content="Share your images here">    
    <meta content="Current category: <?php echo $_GET['category'];?>" property="og:description" />
    <meta content="#43B581" data-react-helmet="true" name="theme-color" />
</head>
<body>
<?php
	include "navbar.php";
    $category = $_GET['category']; // Get the category parameter from the URL
    $imageUrls = getImagesFromJson($category); // Get the image URLs from the JSON file
	$imageUrls = array_filter($imageUrls, function ($item) {
		return $item['url'] !== 'deleted';
	});
    $imagesPerPage = 20;
    $totalImages = count($imageUrls);
    $totalPages = ceil($totalImages / $imagesPerPage);
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
	$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

    // Display pagination links
    $pagination = '<div class="pagination">';
    // Show first page link
    if ($currentPage > 1) {
        $pagination .= '<a href="javascript:changePage(1)">1</a>';
    }

    // Show previous page links
    if ($currentPage >= 2) {
        $pagination .= '<a href="javascript:changePage('.($currentPage - 1).')">Back</a>';
    }

    // Show page links
    $startPage = max($currentPage - 2, 1);
    $endPage = min($currentPage + 2, $totalPages);
    for ($page = $startPage; $page <= $endPage; $page++) {
        $activeClass = ($page == $currentPage) ? 'active' : '';
        $url = '?category=' . $category . '&page=' . $page;
        $pagination .= '<a href="javascript:changePage('.$page.')" class="' . $activeClass . '">' . $page . '</a>';
    }

    // Show next page links
    if ($currentPage <= ($totalPages - 1)) {
        $pagination .= '<a href="javascript:changePage('.($currentPage+1).')">Next</a>';
    }

    // Show last page link
    if ($currentPage < $totalPages) {
        $pagination .= '<a href="javascript:changePage('.($totalPages).')">'.($totalPages).'</a>';
    }
    $pagination .= '</div>';
	
	function shuffleArrayWithSeed(array &$array, $seed) {
		mt_srand($seed);
		shuffle($array);
	}

    $category = $_GET['category']; // Get the category parameter from the URL

    // Function to read and decode the JSON file
    function getImagesFromJson($category) {
        $jsonFile =  "categories/" . $category . '.json';
        $jsonContent = file_get_contents($jsonFile);
        $imageUrls = json_decode($jsonContent, true);
        return $imageUrls;
    }
	
	$imageUrls = array_reverse($imageUrls);
	switch ($sortOption) {
    case 'oldest':
        $imageUrls = array_reverse($imageUrls); 
        break;
    case 'random':
		$seed = isset($_GET['seed']) ? $_GET['seed'] : mt_rand(); 
		$redirect = !isset($_GET['seed']); 
		shuffleArrayWithSeed($imageUrls, $seed); 
		if ($redirect) {
			$redirectUrl = '?category=' . $category . '&page=' . $currentPage . '&seed=' . $seed . '&sort=random'; 
			header('Location: ' . $redirectUrl);
			exit;
		}
        break;
	}
    
	?>
	<iframe id="deleter" style="display: none;"></iframe>
	<div id="sorting">
	<label for="sort">Sort by:</label>
<select name="sort" id="sort" onchange="updateSortParameter()">
  <option value="newest">Newest</option>
  <option value="oldest">Oldest</option>
  <option value="random">Random</option>
</select></div>
	<?php
	
	echo $pagination;
    $start = ($currentPage - 1) * $imagesPerPage;
    $end = $start + $imagesPerPage;
    for ($i = $start; $i < $end && $i < $totalImages; $i++) {
		
       $imageObj = $imageUrls[$i];
$imageUrl = $imageObj['url'];
$imageId = $imageObj['id'];

echo '<div class="imgbox" id="' . $imageId . '">';
echo '<div class="wrapper" href="' . htmlspecialchars(encodeImageUrl($imageUrl)) . '">';
echo '<a class="link2profile" href="/profile.php?id='. htmlspecialchars($imageObj['poster']) .'">';
echo '<img class="pfp" src="'.htmlspecialchars(getItemByKey($accounts, $imageObj['poster'], 'id')->avatar_url).'" /><span style="font-size: 1vh;">Uploaded by </span>'. htmlspecialchars(getItemByKey($accounts, $imageObj['poster'], 'id')->username) .'</a>';

if(isset($account)){
    if($account->id == 0 || $account->id == $imageObj['poster']){
        echo "<a class=\"delete\" href=\"javascript:dialog('".htmlspecialchars($category).":".$imageId."')\">Delete</a>";
    }
}
echo '<img src="' .  htmlspecialchars(encodeImageUrl($imageUrl)) . '" class="center-fit" /></div></div>';

    }

	echo $pagination;
    
?>
<div id="dialogs"></div>
<script>
var clock;

function dialog(post){
	var dialoghtml = "<dialog open><p style=\"display: block;\">Are you sure you want to delete this post?</p><div style=\"width:100%; display: flex; justify-content:space-evenly;\"><button onclick=\"this.parentNode.parentNode.remove()\">No</button><button onclick=\"document.querySelector('iframe').src='/delete.php?post="+post+"';clock = setInterval(()=>{tick('"+post+"');}, 1000);this.parentNode.parentNode.remove();\">Yes</button></div></dialog>";
	document.querySelector("#dialogs").innerHTML += dialoghtml;
	
}

function tick(post){
	var output = (document.querySelector('iframe').contentDocument.body.children[2].innerHTML);
	if(output == ('your post has been deleted successfully.') || output == ('you can only delete posts that you own') || output == ('if you want to delete something, you have to be logged in') || output == ('I have no idea what you are trying to delete.')){
		var dialoghtml = "<dialog open><p style=\"display: block;\">"+output+"</p><div style=\"width:100%; display: flex; justify-content:space-evenly;\"><button onclick=\"document.querySelector('dialog').remove()\">Okay</button></div></dialog>";
		document.querySelector("#dialogs").innerHTML += dialoghtml;
		document.getElementById(post.split(':')[1]).remove();
		clearInterval(clock);
	}
	
	
}

function changePage(pageNumber) {
  var currentURL = window.location.href;
  var url = new URL(currentURL);
  var queryParams = url.searchParams;
  queryParams.set('page', pageNumber);
  var modifiedURL = url.pathname + '?' + queryParams.toString();
  window.location.href = modifiedURL;
}
function updateSortParameter() {
  
  var currentURL = window.location.href;
  var url = new URL(currentURL);
  var queryParams = url.searchParams;
  var selectedValue = document.getElementById('sort').value;
  queryParams.set('sort', selectedValue);
  var modifiedURL = url.pathname + '?' + queryParams.toString();
  window.location.href = modifiedURL;
}

var url = new URL(window.location.href);
var sortParameter = url.searchParams.get('sort');

if (sortParameter !== null) {
  document.getElementById('sort').value = sortParameter;
}

</script>

</body>
</html>
