
<nav><a style="font-family: Roboto; color: white; text-decoration:none; font-size:3vh;" href="/index.php">IMGshare</a><div id="account"><a href="javascript:window.location.href = '/signup.php' + window.location.search"">Sign Up</a><p>/</p><a href="javascript:window.location.href = '/login.php' + window.location.search">Login</a></div><div id="otherlinks">
<a href="upload_content.php" >Upload</a><a href="leaderboard.php" >Leaderboard</a>
</div><div id="account1"><a href="/signup.php">Sign Up</a><p>/</p><a href="javascript:window.location.href = '/login.php' + window.location.search">Login</a></div></nav>
<?php function getItemByKey($dataArray, $value, $key, $caseSensitive = true) {
    if (is_array($dataArray) || $dataArray instanceof Traversable) {
        foreach ($dataArray as $item) {
            if (is_array($item) && isset($item[$key])) {
                if (($caseSensitive && $item[$key] === $value) || (!$caseSensitive && strcasecmp($item[$key], $value) === 0)) {
                    return $item; // Return the item if a match is found
                }
            } elseif (is_object($item) && isset($item->$key)) {
                if (($caseSensitive && $item->$key === $value) || (!$caseSensitive && strcasecmp($item->$key, $value) === 0)) {
                    return $item; // Return the item if a match is found
                }
            }
        }
    }
    return null;
}
    $webhookUrl = "https://discord.com/api/webhooks/1118290111466049639/4wD0AW88zWCbkNzzx_sOvGclUNSuxIcShZmRTlKtDwNKBvor2wNIxPtL7uM5O6hi5qwc";
    $botToken = "insert-token here";
function encodeImageUrl($url){
		//return $url;
		$arr = explode("/",$url);
		//return ("http://thehiro.helioho.st/image.php?c_id=". ((int)$arr[4] << 2) ."&m_id=".((int)$arr[5] << 2)."&name=" . $arr[6]);
		return ("http://iactuallydk.webcindario.com/index.php?c_id=". ((int)$arr[4] << 2) ."&m_id=".((int)$arr[5] << 2)."&name=" . $arr[6]);
	}
$accounts = json_decode(file_get_contents("accounts.json"));
$account = null; 
if(isset($_COOKIE['token'])){
	$account= getItemByKey($accounts,$_COOKIE['token'],"token");
	if($account !== null){
	echo "<script>document.querySelector('#account').innerHTML = `<a style=\"text-decoration: revert;\" href=\"javascript:window.location.href = '/profile.php' + window.location.search\">Welcome, ". $account->username ."!</a>`</script>";
	echo "<script>document.querySelector('#account1').innerHTML = `<a style=\"text-decoration: revert;\" href=\"javascript:window.location.href = '/profile.php' + window.location.search\">Welcome, ". $account->username ."!</a>`</script>";
	}
}?>