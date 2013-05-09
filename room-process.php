<?php
	function shuffleCards($cardNum,$roomid){
		$cardNum = (int)$cardNum;
		$roomid = (int)$roomid;
		if($cardNum < 0){ throw new Exception('Invalid game');}
		if($roomid < 0){ throw new Exception('Invalid player');}
		global $db;
		$q = $db -> prepare("SELECT session FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$gamesession = $q->fetch();
		$gamesession = (int)$gamesession+$roomid*2;

		@mt_srand($gamesession);
		$dock = range(1, $cardNum);
		for($i = $cardNum-1; $i > 0; $i--){
			$j = @mt_rand(0, $i);
			$tmp = $dock[$i];
			$dock[$i] = $dock[$j];
			$dock[$j] = $tmp;
		}
		
		return $dock;
	}
	
	function getHand(){
		$cardNum = 52;
		$roomid = (int)$_POST['roomid'];
		$playerid = (int)$_POST['playerid'];
		// Input validation
		if($playerid > 3 || $playerid < 0){	throw new Exception('Invalid player');}
		if($roomid < 0){ throw new Exception('Invalid player');}
			
		$dock = shuffleCards($cardNum,$roomid);
		$cards = array_chunk($dock,($cardNum/4));
		$hand = $cards[$playerid];
		
		return $hand;
	}
	
	function getSeat(){
		$roomid = (int)$_POST['roomid'];
		$playerid = (int)$_POST['playerid'];
		// Input validation
		if($playerid > 3 || $playerid < 0){	throw new Exception('Invalid player');}
		if($roomid < 0){ throw new Exception('Invalid player');}
		
		switch($playerid){
			case 0:
				$player = 'north';
				break;
			case 1:
				$player = 'south';
				break;
			case 2:
				$player = 'east';
				break;
			case 3:
				$player = 'west';
				break;
		}
		
		global $db;
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		// playerid = 9 means a computer player
		if($r[$player] == '0' || $r[$player] == '9'){
			$userid = getUserid();
			$q = $db -> prepare("UPDATE game SET ".$player." = ? WHERE roomid = ?");
			$q->execute(array($userid,$roomid));			
			return $playerid;
		}
		
		return null;
	}
	
	function leaveSeat(){
		$roomid = (int)$_POST['roomid'];
		$userid = getUserid();
		// Input validation
		if($roomid < 0){ throw new Exception('Invalid player');}
		
		global $db;
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		if($r['north'] == $userid){$player = 'north';}
		elseif($r['south'] == $userid){$player = 'south';}
		elseif($r['east'] == $userid){$player = 'east';}
		elseif($r['west'] == $userid){$player = 'west';}
		else{throw new Exception('Invalid player');}
		
		// playerid = 9 means a computer player
		$q = $db -> prepare("UPDATE game SET ".$player." = ? WHERE roomid = ?");
		$q->execute(array(9,$roomid));
		return true;
	}
	
	function checkSeats(){
		$roomid = (int)$_POST['roomid'];
		// Input validation
		if($roomid < 0){ throw new Exception('Invalid player');}
		
		global $db;
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		if(!$r){ throw new Exception('Invalid room');}
		$seats = array(0=>$r['north'], 1=>$r['south'], 2=>$r['east'], 3=>$r['west']);

		for($i=0;$i<count($seats);$i++) {
			if($seats[$i] == '9'){$seats[$i] = 'Computer';}
			elseif($seats[$i] == '0'){$seats[$i] = 'No player';}
			else{
				$q = $db -> prepare("SELECT username FROM user WHERE userid = ? LIMIT 1");
				$q->execute(array($seats[$i]));
				$r = $q->fetch();
				if($r){
					$seats[$i] = $r['username'];
				} else {$seats[$i] = 'Computer';}
			}
		}
		
		return $seats;
	}
	
	function getSession(){
		$roomid = (int)$_POST['roomid'];
		if($roomid < 0){ throw new Exception('Invalid player');}
		global $db;
		$q = $db -> prepare("SELECT session FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		
		return $r;
	}
	
	function resetSession(){
		$roomid = (int)$_POST['roomid'];
		if($roomid < 0){ throw new Exception('Invalid player');}
		$newSession = @mt_rand(0,99999999999);
		global $db;
		$q = $db -> prepare("UPDATE game SET session = ? WHERE roomid = ?");
		$q->execute(array($newSession,$roomid));
		return $newSession;
	}
	
	function createRoom(){
		$roomid = (int)$_POST['roomid'];
		if($roomid < 0){ throw new Exception('Invalid player');}
		$session = @mt_rand(0,99999999999);
		global $db;
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		if($r == null){
			$q = $db -> prepare("INSERT INTO game (session,roomid) VALUES(?,?)");
			$q->execute(array($session,$roomid));
		}
		return true;
	}
	
	function getUserid(){
		error_reporting(0);
		if(!isset($_COOKIE['fbsr_123059651225050'])){
			throw new Exception('Invalid player');
		} 
		$data = parse_signed_request($_COOKIE['fbsr_123059651225050']);
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
// Input validation
if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode('Failed : Undefined action');
	exit();
}

$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");

// The return values of the functions are then encoded in JSON format and used as output
try {
	if (($returnVal = call_user_func($_REQUEST['action'])) === false) {
		if ($db && $db->errorCode()) 
			error_log(print_r($db->errorInfo(), true));
		echo json_encode('Failed');
	}
	// Return value
	echo json_encode($returnVal);
} catch(PDOException $e) {
	error_log($e->getMessage());
	echo json_encode('Failed : Error-db');
} catch(Exception $e) {
	echo json_encode('Failed: '.$e->getMessage());
}
?>
