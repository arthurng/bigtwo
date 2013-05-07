<?php
	$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
	$q = $db -> prepare("SELECT * FROM user WHERE userid = ?");
	$q->execute(array($_REQUEST["userid"]));
	$r = $q->fetch();
	if (!$r) {
		$path = "http://graph.facebook.com/".$_REQUEST["userid"]."/picture";
		
		$q = $db -> prepare("INSERT INTO user (userid, username, score, picture) VALUES (?, ?, ?, ?)");
		$q->execute(array($_REQUEST["userid"], $_REQUEST["username"], 0, $path));
	}
	
	header('Location: lobby.php?userid='.$_REQUEST["userid"]);	
?>