<?php
	$r = 0;

	function getResults(){
		global $r;
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT * FROM game WHERE roomid = ?");
		$q->execute(array($_REQUEST["roomid"]));
		$r = $q->fetch();

		// Determin the number of cards in each field & the position of the user
		$userid = getUserid();
		$arr = array("north", "east", "south", "west");
		for ($j=0; $j<4; $j++){
			$i = $arr[$j];
			if ($r["card$i"]) $$i = explode(",", $r["card{$i}"]); 
			else $$i = null;
			// Determine the user's position
			if ($r[$i] == $userid) $userPosition = $i;
		}

		$arrOfRemainingCards = array(
			"north" => count($north), "east" => count($east), "south" => count($south), "west" => count($west)
		);
		
		$p = 1;
		for ($k=0; $k<=13; $k++){
			$t = array_keys($arrOfRemainingCards, $k);
			if ($t){
				if (array_search($userPosition, $t) !== false) $userRank = $p;
				$rank[$p++] = $t;
			}
		}

		for ($x=1; $x<4; $x++){
			if (count($rank[$x])>1){
				usort($rank[$x], "cmpMax");
			}
		}

		for ($x=1; $x<4; $x++){
			if (count($rank[$x])>1){
				foreach(array_reverse($rank[$x]) as $i){
					if ($rank[$x+1] == null) $rank[$x+1] = [];
					array_push($rank[4], $i);
					array_pop($rank[$x]);
					if (count($rank[$x])==1) break; 
				}
			}
		}

		error_log(print_r($rank,1));

		$scoreScale = array(0, 500, 250, 100, 0);
		$response = array(
			"north" => getUserData($r["north"]),
			"east" => getUserData($r["east"]),
			"south" => getUserData($r["south"]),
			"west" => getUserData($r["west"]),
			"first" => $rank[1],
			"second" => $rank[2],
			"third" => $rank[3],
			"forth" => $rank[4],
//			"north" => $r["north"],
//			"east" => $r["east"],
//			"south" => $r["south"],
//			"west" => $r["west"],
			"countnorth" => count($north), 
			"counteast" => count($east), 
			"countsouth" => count($south), 
			"countwest" => count($west), 
			"position" => $userPosition,
			"rank" => $userRank,
			"score" => $scoreScale[$userRank]
		);
		if ($userRank == 1){
			addScoreToWinner($response, $scoreScale);
		}
		return $response;
	}

	// This function will only be called by the winner's php instance
	function addScoreToWinner($response, $scoreScale){
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("UPDATE user 
			SET score = CASE username
				WHEN ? THEN ?
				WHEN ? THEN ?
				WHEN ? THEN ?
				WHEN ? THEN ?
			END
		");
		$arr = array("first", "second", "third", "forth");
		for ($j=0; $j<4; $j++){
			$i = $arr[$j]; $k=$j+1;
			${"v".$k."a"} = $response[$response[$i][0]][0]->username;
			${"v".$k."b"} = (int)$response[$response[$i][0]][0]->score+$scoreScale[$k];
		}
		$q->execute(array($v1a, $v1b, $v2a, $v2b, $v3a, $v3b, $v4a, $v4b));
	}

	function getUserData($userid){
		$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
		$q = $db -> prepare("SELECT username, picture, score FROM user WHERE userid = ?");
		$q->execute(array($userid));
		$r = $q->fetchAll(PDO::FETCH_CLASS);
		return $r;
	}


	function cmpMax($a, $b){
		global $r;
		//error_log(max(explode(",", $r["card".$a])));
		//error_log(max(explode(",", $r["card".$b])));
		if (max(explode(",", $r["card".$a])) > max(explode(",", $r["card".$b])))
			return 1;
		else return -1;
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