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

.row{
	    display: flex;
    justify-content: space-between;
}

.pfp{
	object-fit: cover;
    aspect-ratio: 1/1;
    border-radius: 50%;
    width: 5vh;
}

.acclink{
	    color: white;
    margin: auto 0;
    font-size: 3vh;
}

.inv{
	    display: inline;
    margin: auto 0;
    font-size: 3vh;
}
<?php echo file_get_contents("navbar.css"); ?></style>
</head>
<body>
<?php include "navbar.php";
$jsonData = file_get_contents('accounts.json');
$accounts = json_decode($jsonData, true);
$filteredAccounts = array_filter($accounts, function ($account) {
    return $account['invites'] > 0;
});

usort($filteredAccounts, function ($a, $b) {
    return $b['invites'] - $a['invites'];
});

$topAccounts = array_slice($filteredAccounts, 0, 10);

echo '<div style="display:flex; width:100%; justify-content:center;"><div style="position: relative;width: 80%;">';
echo '<div class="row">
<p class="inv">Avatar</p>
<p class="inv">Username</p><p class="inv">Invites</p></div>';
foreach($topAccounts as $acc){
	echo '<div class="row"><img src="'.encodeImageUrl($acc['avatar_url']).'" class="pfp">
<a href="/profile.php?id='.$acc['id'].'" class="acclink">'.$acc['username'].'</a>
<p class="inv">'.$acc['invites'].'</p>
</div>';
}
echo '</div></div>';
?>

</script>
</body>
</html>