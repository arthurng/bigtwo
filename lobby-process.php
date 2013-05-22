<?php
	function joinQueue(){
		$userid = getUserid();
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT * FROM queue WHERE userid = ? AND valid = 1");
		$q->execute(array($userid));
		$r = $q->fetch();
		if (!$r) {
			$q = $db -> prepare("INSERT INTO queue (userid) VALUES (?)");
			$q->execute(array($userid));
		}
	}

	function getCurrentQueue(){
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT * FROM queue WHERE roomid = ? AND valid = 1");
		$q->execute(array($_REQUEST["roomid"]));
		$r = $q->fetchAll(PDO::FETCH_CLASS);
		$userid = getUserid();
		foreach ($r as $i) {
			if ($i->userid == $userid) $inside = 1;
			else $inside = 0;
		}
		$arr = array("userlist" => $r, "inside" => $inside);
		return $arr;
	}

	function getRoomList(){
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT DISTINCT roomid FROM queue WHERE valid = 1 ORDER BY roomid");
		$q->execute();
		$r = $q->fetchAll(PDO::FETCH_CLASS);
		return $r;
	}

	function createRoom(){
		if (!$_REQUEST["name"]) return "name_not_given";
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT roomid FROM queue WHERE valid = 1 AND roomid=? LIMIT 1");
		$q->execute(array(strtoupper($_REQUEST["name"])));
		$r = $q->fetch();
		if ($r) return "name_taken";
		else {
			return "name_ok";
		}
	}

	function removeFromQueue(){
		$userid = getUserid();
		error_log("removing userid ".$userid);
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("UPDATE queue SET valid = 0 WHERE userid = ? AND valid = 1");
		$q->execute(array($userid));		
	}

	function joinRoom(){
		$userid = getUserid();
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("UPDATE queue SET roomid = ? WHERE userid = ? AND valid = 1");
		$q->execute(array(strtoupper($_REQUEST["roomid"]), $userid));		
	}

	function getUsername(){
		$userid = $_REQUEST["userid"];
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT username, picture FROM user WHERE userid = ?");
		$q->execute(array($userid));
		$r = $q->fetchAll(PDO::FETCH_CLASS);
		return $r;
	}

	function getUserid(){
		// Get user id from facebook cookie
		error_reporting(0);
		if(!isset($_COOKIE['fbsr_123059651225050'])){
			throw new Exception('Invalid player');
		}
		$data = parse_signed_request($_COOKIE['fbsr_123059651225050']);
		if($data["user_id"] == null){
			throw new Exception('Invalid Cookie');
		}
		return $data["user_id"];
	}	

	function parse_signed_request($signed_request) {
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
		// decode the data
		$sig = base64_url_decode($encoded_sig);
		$data = json_decode(base64_url_decode($payload), true);
		return $data;
	}

	function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}

	header('Content-Type: application/json');
	echo json_encode(call_user_func($_REQUEST['action']));
?>