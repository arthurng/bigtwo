<?php
	function shuffleCards($cardNum,$roomid,$gamesession){
		$cardNum = (int)$cardNum;
		$roomid = (int)$roomid;
		$date = getdate();
		$gamesession = (int)$gamesession+$roomid*2+$date["mday"]*3+$date["wday"]*4+$date["mon"]*5;

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
		$gamesession = (int)$_POST['gamesession'];
		// Input validation
		if($playerid > 3 || $playerid < 0){	throw new Exception('Invalid player');}
		if($roomid < 0){ throw new Exception('Invalid player');}
		if($gamesession < 0){ throw new Exception('Invalid player');}
			
		$dock = shuffleCards($cardNum,$roomid,$gamesession);
		$cards = array_chunk($dock,($cardNum/4));
		$hand = $cards[$playerid];
		
		return $hand;
	}
	
	function resetSession(){
		$roomid = (int)$_POST['roomid'];
		error_reporting(0);
		if(!isset($_COOKIE['gamesession'])){
			$current_session = $roomid;
		} else {
			$current_session = $_COOKIE['gamesession'];
		}
		@mt_srand($current_session);				
		$current_session = @mt_rand();
		// Set Cookies
		$exp = time() + 3600 * 24 * 1; // 1day
		setcookie('gamesession', $current_session, $exp,null,null,false,true);

		return $current_session;
	}

header('Content-Type: application/json');
// Input validation
if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode('Failed : Undefined action');
	exit();
}

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
