<?php
	function getCurrentQueue(){
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT * FROM queue WHERE roomid = ? AND valid = 1");
		$q->execute(array($_REQUEST["roomid"]));
		$r = $q->fetchAll(PDO::FETCH_CLASS);
		return $r;
	}

	function getRoomList(){
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT DISTINCT roomid FROM queue WHERE valid = 1 ORDER BY roomid");
		$q->execute();
		$r = $q->fetchAll(PDO::FETCH_CLASS);
		return $r;
	}

	function createRoom(){
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT roomid FROM queue WHERE valid = 1 ORDER BY roomid DESC LIMIT 1");
		$q->execute();
		$r = $q->fetch();
		return $r["roomid"]+1;
	}

	function removeFromQueue(){
		error_log("removing userid ".$_REQUEST['userid']);
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("UPDATE queue SET valid = 0 WHERE userid = ? AND valid = 1");
		$q->execute(array($_REQUEST["userid"]));		
	}

	function joinRoom(){
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("UPDATE queue SET roomid = ? WHERE userid = ? AND valid = 1");
		$q->execute(array($_REQUEST["roomid"], $_REQUEST["userid"]));		
	}

	header('Content-Type: application/json');
	echo json_encode(call_user_func($_REQUEST['action']));
?>