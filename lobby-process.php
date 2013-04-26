<?php
	function getCurrentQueue(){
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT * FROM queue");
		$q->execute();
		$r = $q->fetchAll(PDO::FETCH_CLASS);
		return $r;
	}

	function removeFromQueue(){
		error_log("removing userid ".$_REQUEST['userid']);
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("DELETE FROM queue WHERE userid = ?");
		$q->execute(array($_REQUEST["userid"]));		
	}

	header('Content-Type: application/json');
	echo json_encode(call_user_func($_REQUEST['action']));
?>