<?php

/**********************************************/
/**Remark: For Straight flush & Straight case**/
/***********"JQKA2": The Biggest!!!!***********/
/***********"34567": The Smallest!!!***********/
/***** There is a gap between "2" & "3" *******/
/******** E.g. NO "23456" or "A2345" **********/
/**********************************************/

// Entry point to the logic: checkLogic($cards);
// $cards is an array of strings.
function checkLogic($cards){
	switch (count($cards)){
		case 1:
			$validity = oneCard($cards);
			break;
		case 2:
			$validity = twoCard($cards);
			break;
		case 3:
			$validity = threeCard($cards);
			break;
		case 5:
			$validity = fiveCard($cards);
			break;
		default:
			$validity = false;
			break;
	} return $validity;
} 

function oneCard($cards){
	$r = fetchLast();
	$prevHand = lastHandNotPass($r);

	// 1. Check if previous hand consist of n cards
	if ($prevHand[0] != 1 && $prevHand[0] != "PASS") return false;

	// 2. Validate and calculate the current hand
	$currentHand = array("1", $cards[0]);

	// 3. Check if "passed three times"
	if (checkIfLastThreeIsPass($r)) {
		saveNewHand($r, join("-", $currentHand));
		return true;
	}

	// 4. Chech if the hand is larger than the previous
	if ($currentHand[1] < $prevHand[1]) return false;
	else {
		saveNewHand($r, join("-", $currentHand));
		return true;
	}
}

function twoCard($cards){
	$r = fetchLast();
	$prevHand = lastHandNotPass($r);

	// 1. Check if previous hand consist of n cards
	if ($prevHand[0] != 2 && $prevHand[0] != "PASS") return false;

	// 2. Validate and calculate the current hand
	if (ceil($cards[0]/4) != ceil($cards[1]/4)) return false;
	else {
		$currentHand = array("2", max($cards));
	}

	// 3. Check if "passed three times"
	if (checkIfLastThreeIsPass($r)) {
		saveNewHand($r, join("-", $currentHand));
		return true;
	}

	// 4. Chech if the hand is larger than the previous
	if ($currentHand[1] < $prevHand[1]) return false;
	else {
		saveNewHand($r, join("-", $currentHand));
		return true;
	}
}

function threeCard($cards){
	$r = fetchLast();
	$prevHand = lastHandNotPass($r);

	// 1. Check if previous hand consist of n cards
	if ($prevHand[0] != 3 && $prevHand[0] != "PASS") return false;

	// 2. Validate and calculate the current hand
	if (ceil($cards[0]/4) != ceil($cards[1]/4) || ceil($cards[1]/4) != ceil($cards[2]/4)) return false;
	else {
		$currentHand = array("3", max($cards));
	}

	// 3. Check if "passed three times"
	if (checkIfLastThreeIsPass($r)) {
		saveNewHand($r, join("-", $currentHand));
		return true;
	}

	// 4. Chech if the hand is larger than the previous
	if ($currentHand[1] < $prevHand[1]) return false;
	else {
		saveNewHand($r, join("-", $currentHand));
		return true;
	}
}

function fiveCard($cards){
	$r = fetchLast();
	$prevHand = lastHandNotPass($r);
	
	//1. Check if previous hand consist of 5 cards
	if ($prevHand[0] < 4 &&  $prevHand[0] > 8 && $prevHand[0] != "PASS") return false;
	
	// 2. Validate and calculate the current hand
		// 2.1 For Straight Case
	if($prevHand[0] == 4) {
		//Supposed the cards is from big to small
		//Ban "QKA23","KA234" these 3 cases, hardcoded LOL
		if(ceil($cards[0]/4) == 13 && ceil($cards[1]/4) == 12 && ceil($cards[2]/4) == 11 && ceil($cards[3]/4) == 10 && ceil($cards[4]/4) == 1) {
			return false;
		}
		if(ceil($cards[0]/4) == 13 && ceil($cards[1]/4) == 12 && ceil($cards[2]/4) == 11 && ceil($cards[3]/4) == 2 && ceil($cards[4]/4) == 1) {
			return false;
		}
		//Continue checking from "34567" to "JQKA2"
		if(ceil($cards[1]/4) == (ceil($cards[0]/4) - 1) && ceil($cards[2]/4) == (ceil($cards[1]/4) - 1) && ceil($cards[3]/4) == (ceil($cards[2]/4) - 1) && ceil($cards[4]/4) == (ceil($cards[3]/4) - 1)){
			$currentHand = array("4", max($cards));
		}
		else {
			return false;
		}
		
		// 2.1.1 Check if "passed three times"
		if (checkIfLastThreeIsPass($r)) {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
		
		// 2.1.2 Chech if the hand is larger than the previous
		if ($currentHand[1] < $prevHand[1]) return false;
		else {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
	}
	
		// 2.2 For Flush Case
	if($prevHand[0] == 5) {
		if($cards[4]%4 == $cards[3]%4 && $cards[3]%4 == $cards[2]%4 && $cards[2]%4 == $cards[1]%4 && $cards[1]%4 == $cards[0]%4 && $cards[0]%4 == $cards[4]%4){
			$currentHand = array("5", max($cards));
		}
		else {
			return false;
		}
		
		// 2.2.1 Check if "passed three times"
		if (checkIfLastThreeIsPass($r)) {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
		
		// 2.2.2 Chech if the hand is larger than the previous
		if ($currentHand[1] < $prevHand[1]) return false;
		else {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
	}
		// 2.3 For Full House Case	
	if($prevHand[0] == 6) {
		// For case like "66655"
		if(ceil($cards[1]/4) == (ceil($cards[2]/4))){
			if(ceil($cards[0]/4) == ceil($cards[1]/4) && ceil($cards[3]/4) == ceil($cards[4]/4)){
				$currentHand = array("6", max($cards));
			}
		}
		// For case like "66555"
		else if(ceil($cards[1]/4) != (ceil($cards[2]/4))){
			if(ceil($cards[0]/4) == ceil($cards[1]/4) && ceil($cards[2]/4) == ceil($cards[3]/4) && ceil($cards[3]/4) == ceil($cards[4]/4)){
				$currentHand = array("6", $cards[4]);
			}
		}
		else {
			return false;
		}
		
		// 2.3.1 Check if "passed three times"
		if (checkIfLastThreeIsPass($r)) {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
		
		// 2.3.2 Chech if the hand is larger than the previous
		if ($currentHand[1] < $prevHand[1]) return false;
		else {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
	}
		// 2.4 For Four of a Kind Case
	if($prevHand[0] == 7) {
		// For case like "66665"
		if(ceil($cards[0]/4) == (ceil($cards[1]/4))){
			if(ceil($cards[0]/4) == ceil($cards[1]/4) && ceil($cards[1]/4) == ceil($cards[2]/4) && ceil($cards[2]/4) == ceil($cards[3]/4)){
				$currentHand = array("7", max($cards));
			}
		}
		// For case like "65555"
		else if(ceil($cards[0]/4) != (ceil($cards[1]/4))){
			if(ceil($cards[1]/4) == ceil($cards[2]/4) && ceil($cards[2]/4) == ceil($cards[3]/4) && ceil($cards[3]/4) == ceil($cards[4]/4)){
				$currentHand = array("7", $cards[4]);
			}
		}
		else {
			return false;
		}
		
		// 2.4.1 Check if "passed three times"
		if (checkIfLastThreeIsPass($r)) {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
		
		// 2.4.2 Chech if the hand is larger than the previous
		if ($currentHand[1] < $prevHand[1]) return false;
		else {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
	}
		// 2.4 For Straight Flush Case
	if($prevHand[0] == 8) {
		//Check the Flowers first LOL
		if($cards[4]%4 == $cards[3]%4 && $cards[3]%4 == $cards[2]%4 && $cards[2]%4 == $cards[1]%4 && $cards[1]%4 == $cards[0]%4 && $cards[0]%4 == $cards[4]%4){
			//Supposed the cards is from big to small
			//Ban "QKA23","KA234" these 3 cases, hardcoded LOL
			if(ceil($cards[0]/4) == 13 && ceil($cards[1]/4) == 12 && ceil($cards[2]/4) == 11 && ceil($cards[3]/4) == 10 && ceil($cards[4]/4) == 1) {
				return false;
			}
			if(ceil($cards[0]/4) == 13 && ceil($cards[1]/4) == 12 && ceil($cards[2]/4) == 11 && ceil($cards[3]/4) == 2 && ceil($cards[4]/4) == 1) {
				return false;
			}
			//Continue checking from "76543" to "2AJQK"
			if(ceil($cards[1]/4) == (ceil($cards[0]/4) - 1) && ceil($cards[2]/4) == (ceil($cards[1]/4) - 1) && ceil($cards[3]/4) == (ceil($cards[2]/4) - 1) && ceil($cards[4]/4) == (ceil($cards[3]/4) - 1)){
				$currentHand = array("8", max($cards));
			}
			else {
				return false;
			}
			
			// 2.1.1 Check if "passed three times"
			if (checkIfLastThreeIsPass($r)) {
				saveNewHand($r, join("-", $currentHand));
				return true;
			}
			
			// 2.1.2 Chech if the hand is larger than the previous
			if ($currentHand[1] < $prevHand[1]) return false;
			else {
				saveNewHand($r, join("-", $currentHand));
				return true;
			}
		}
		else {
			return false;
		}
	}
}

function fetchLast(){
	$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
	$q = $db -> prepare("SELECT firstLast, secondLast, thirdLast, forthLast FROM game WHERE sessionid = ? LIMIT 1");
	$q->execute(array($_REQUEST["sessionid"]));
	$r = $q->fetch();

	if (!$r) {
		error_log("Session not found!");
	} else return $r;
}

function saveNewHand($r, $newHand){
	$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
	$q = $db -> prepare("UPDATE game SET firstLast = ?, secondLast = ?, thirdLast = ?, forthLast = ? WHERE sessionid = ?");
	$q->execute(array($newHand, $r["firstLast"], $r["secondLast"], $r["thirdLast"], $_REQUEST["sessionid"]));
}

function lastHandNotPass($r){
	if ($r["firstLast"] != "PASS")$o=$r["firstLast"];
	else if ($r["secondLast"] != "PASS") $o=$r["secondLast"];
	else if ($r["thirdLast"] != "PASS") $o=$r["thirdLast"];
	else $o = "PASS";
	return split("-", $o);
}

function checkIfLastThreeIsPass($r){
	if (($r["firstLast"]=="PASS") && ($r["secondLast"]=="PASS") && ($r["thirdLast"]=="PASS")) return true;
	else return false;
}

/* Debugging Section for Arthur */


function test(){
	//$input = fgets(STDIN);
	//$input = mb_substr($input, 0, -1);
	$input = "52,51,50,1,2";
	$input = explode(",", $input);
	$_REQUEST["sessionid"] = 1;
	if (checkLogic($input)) echo "It is valid.\n\n";
	else echo "It is invalid.\n\n";
} 

function test2(){
	$_REQUEST["sessionid"] = 1;
	fetchLast();
}

test();


?>
