<?php

//include("hand-logic.php");

	function shuffleCards($cardNum,$roomid){
		$cardNum = (int)$cardNum;
		$roomid = (int)$roomid;
		if($cardNum < 0){ throw new Exception('Invalid game');}
		if($roomid < 0){ throw new Exception('Invalid player');}
		global $db;
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		$gamesession = (int)$r['sessionid']+$roomid*2;

		@mt_srand($gamesession);
		$dock = range(1, $cardNum);
		for($i = $cardNum-1; $i > 0; $i--){
			$j = @mt_rand(0, $i);
			$tmp = $dock[$i];
			$dock[$i] = $dock[$j];
			$dock[$j] = $tmp;
		}
		// Return a shuffled dock
		return $dock;
	}
	
	function getHand(){
		$cardNum = 52;
		$roomid = (int)$_POST['roomid'];
		// Input validation
		if($roomid < 0){ throw new Exception('Invalid player');}		
		// Get the user's seat
		$player = verifySeat($roomid);
		if(!$player){ throw new Exception('Invalid player');}
		$playerid = player2id($player);
		
		// Check whether there is four players
		global $db;
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		if($r['north'] == '0' || $r['east'] == '0' || $r['south'] == '0' || $r['west'] == '0'){
			// When there is an empty seat
			throw new Exception('wait');
		}
			
		$dock = shuffleCards($cardNum,$roomid);
		$cards = array_chunk($dock,($cardNum/4));
		$hand = $cards[$playerid];
		
		// Sort the hand
		rsort($hand);
		
		// Find out who holds diamond 3
		if($hand[($cardNum/4-1)] == '1'){
			// Translate player id to player name
			$player = id2player($playerid);
			$q = $db -> prepare("UPDATE game SET turn = ? WHERE roomid = ?");
			$q->execute(array($player,$roomid));
		}
		
		// Store the card
		$q = $db -> prepare("UPDATE game SET card".$player." = ? WHERE roomid = ?");
		$q->execute(array(implode(",",$hand),$roomid));
		
		// Return a hand
		return $hand;
	}
	
	function getSeat(){
		$roomid = (int)$_POST['roomid'];
		$playerid = (int)$_POST['playerid'];
		// Input validation
		if($playerid > 3 || $playerid < 0){	throw new Exception('Invalid player');}
		if($roomid < 0){ throw new Exception('Invalid player');}
		// Translate player id to player name
		$player = id2player($playerid);
		
		global $db;
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		// playerid = 9 means a computer player
		// The player with id 0 does nothing while the player with id 9 would "pass" everytime (Not implemented yet)
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
		// Get the user's seat
		$player = verifySeat($roomid);
		if(!$player){ throw new Exception('Invalid player');}

		global $db;
		// playerid = 9 means a computer player
		$q = $db -> prepare("UPDATE game SET ".$player." = ? WHERE roomid = ?");
		$q->execute(array(9,$roomid));
		return true;
	}
	
	// Get the status of all seats
	function updateSeats(){
		$roomid = (int)$_POST['roomid'];
		// Input validation
		if($roomid < 0){ throw new Exception('Invalid player');}
		
		// Get current users on seats
		global $db;
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		if(!$r){ throw new Exception('Invalid room');}
		$seats = array(0=>$r['north'], 1=>$r['south'], 2=>$r['east'], 3=>$r['west']);
		// Translate user id to user name
		for($i=0;$i<count($seats);$i++) {
			if($seats[$i] == '9'){$seats[$i] = 'Computer';}
			elseif($seats[$i] == '0'){$seats[$i] = 'No player';}
			else{
				// Get user name
				$q = $db -> prepare("SELECT username FROM user WHERE userid = ? LIMIT 1");
				$q->execute(array($seats[$i]));
				$r = $q->fetch();
				if($r){
					$seats[$i] = $r['username'];
				} else {$seats[$i] = 'Computer';}
			}
		}
		// Get the user's seat in terms of playerid
		$player = verifySeat($roomid);
		if($player){
		$playerid = player2id($player);
		$seats[4] = $playerid;
		}
		
		// Return an array storing the user name of each seat and playerid
		return $seats;
	}
	
	// Identify the user's seat
	function verifySeat($roomid){
		$userid = getUserid();
		// Input validation
		if($roomid < 0){ return null;}
		
		global $db;
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		
		if($r['north'] == $userid){$player = 'north';}
		elseif($r['south'] == $userid){$player = 'south';}
		elseif($r['east'] == $userid){$player = 'east';}
		elseif($r['west'] == $userid){$player = 'west';}
		else{return null;}
		// Return the user's seat
		return $player;
	}
	
	// The check game end moved to game-server
	
	// Get current game session for debugging
	function getSession(){
		$roomid = (int)$_POST['roomid'];
		if($roomid < 0){ throw new Exception('Invalid player');}
		global $db;
		$q = $db -> prepare("SELECT sessionid FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		// Return current game session
		return $r;
	}
	
	// Renew game session
	// It should be called after each game to ensure the dock is shuffled
	function resetSession(){		
		$roomid = (int)$_POST['roomid'];
		if($roomid < 0){ throw new Exception('Invalid player');}
		@mt_srand();
		$newSession = @mt_rand(0,99999999999);
		global $db;
		$q = $db -> prepare("UPDATE game SET sessionid = ? WHERE roomid = ?");
		$q->execute(array($newSession,$roomid));
		// Return the new game session
		return $newSession;
	}
	
	// Create a new game room
	function createRoom(){
		$roomid = (int)$_POST['roomid'];
		if($roomid < 0){ throw new Exception('Invalid player');}
		$session = @mt_rand(0,99999999999);
		global $db;
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		if($r == null){
			$q = $db -> prepare("INSERT INTO game (sessionid,roomid,cardnorth,cardeast,cardsouth,cardwest) VALUES(?,?,?,?,?,?)");
			$q->execute(array($session,$roomid,'0','0','0','0'));
			$q = $db -> prepare("INSERT INTO session (roomid, timer, hand, done, ready) VALUES(?,?,?,?,?)");
			$q->execute(array($roomid,'0','0','0','0'));			
		}
		return true;
	}
	
	// Translate player id (0/1/2/3) to player name (north/east/south/west)
	function id2player($playerid){
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
			default: throw new Exception('Invalid player');
		}
		return $player;
	}
	// Translate player name (north/east/south/west) to player id (0/1/2/3)
	function player2id($player){
		switch($player){
			case 'north':
				$playerid = 0;
				break;
			case 'south':
				$playerid = 1;
				break;
			case 'east':
				$playerid = 2;
				break;
			case 'west':
				$playerid = 3;
				break;
			default: throw new Exception('Invalid player');
		}
		return $playerid;
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

	function getCurrentPlayer(){
		global $db;
		$roomid = (int)$_POST['roomid'];
		$q = $db -> prepare("SELECT turn FROM game WHERE roomid = ? LIMIT 1");
		$q->execute(array($roomid));
		$r = $q->fetch();
		error_log(print_r($r,1));
		return $r["turn"];
	}

header('Content-Type: application/json');
$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
echo json_encode(call_user_func($_REQUEST['action']));

/*
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
*/
?>
