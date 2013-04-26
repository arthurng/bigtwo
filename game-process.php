<?php
  function shuffCards($cardNum,$roomid){
		$cardNum = (int)$cardNum;
		$roomid = (int)$roomid;
		
		@mt_srand($roomid);
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
		$roomid = (int)$_REQUEST['roomid'];
		$playerid = (int)$_REQUEST['playerid'];		
	
		$dock = shuffCards($cardNum,$roomid);
		$cards = array_chunk($dock,($cardNum/4));
		$hand = $cards[$playerid];
		
		return $hand;
	}

	header('Content-Type: application/json');
	echo json_encode(call_user_func($_REQUEST['action']));
?>
