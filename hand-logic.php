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
	//$cards = explode(",", $cards);
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
	if ($currentHand[1] <= $prevHand[1]) return false;
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
	if ($currentHand[1] <= $prevHand[1]) return false;
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
	if ($currentHand[1] <= $prevHand[1]) return false;
	else {
		saveNewHand($r, join("-", $currentHand));
		return true;
	}
}

function fiveCard($cards){
	$r = fetchLast();
	$prevHand = lastHandNotPass($r);
	$currentHand = null;
	
	//1. Check if previous hand consist of 5 cards
	if ($prevHand[0] < 4 &&  $prevHand[0] > 8 && $prevHand[0] != "PASS") return false;
	
	// 2. Validate and calculate the current hand	
		// 2.4 For Straight Flush Case
	if($prevHand[0] == 8 || $prevHand[0] < 8 || $prevHand[0] == "PASS") {
		//Check the Flowers first LOL
		if($cards[4]%4 == $cards[3]%4 && $cards[3]%4 == $cards[2]%4 && $cards[2]%4 == $cards[1]%4 && $cards[1]%4 == $cards[0]%4 && $cards[0]%4 == $cards[4]%4){
			//Supposed the cards is from big to small
			//Ban "QKA23","KA234" these 3 cases, hardcoded LOL
			/*if(ceil($cards[0]/4) == 13 && ceil($cards[1]/4) == 12 && ceil($cards[2]/4) == 11 && ceil($cards[3]/4) == 10 && ceil($cards[4]/4) == 1) {
				return false;
			}
			if(ceil($cards[0]/4) == 13 && ceil($cards[1]/4) == 12 && ceil($cards[2]/4) == 11 && ceil($cards[3]/4) == 2 && ceil($cards[4]/4) == 1) {
				return false;
			}*/
			//Continue checking from "76543" to "2AJQK"
			if(ceil($cards[1]/4) == (ceil($cards[0]/4) - 1) && ceil($cards[2]/4) == (ceil($cards[1]/4) - 1) && ceil($cards[3]/4) == (ceil($cards[2]/4) - 1) && ceil($cards[4]/4) == (ceil($cards[3]/4) - 1)){
				$currentHand = array("8", max($cards));
			}
			else {
				goto four_kind;
				return false;
			}
			
			// 2.1.1 Check if "passed three times"
			if (checkIfLastThreeIsPass($r)) {
				saveNewHand($r, join("-", $currentHand));
				return true;
			}
			
			// 2.1.2 Chech if the hand is larger than the previous
			if($currentHand[0] > $prevHand[0]) {
				saveNewHand($r, join("-", $currentHand));
				return true;
			}
			else if ($currentHand[0] == $prevHand[0]) {
				if($currentHand[1] > $prevHand[1]){
					saveNewHand($r, join("-", $currentHand));
					return true;
				}
				else {
					goto four_kind;
					return false;
				}
			}
			else {
				goto four_kind;
				return false;
			}
		}
		else {
			goto four_kind;
			return false;
		}
	}	
	
		// 2.4 For Four of a Kind Case
	four_kind:
	if($prevHand[0] == 7 || $prevHand[0] < 7 || $prevHand[0] == "PASS") {
		// For case like "66665"
		if(ceil($cards[0]/4) == (ceil($cards[1]/4))){
			if(ceil($cards[0]/4) == ceil($cards[1]/4) && ceil($cards[1]/4) == ceil($cards[2]/4) && ceil($cards[2]/4) == ceil($cards[3]/4)){
				$currentHand = array("7", max($cards));
			}
			else {
				goto full_house;
				return false;
			}
		}
		// For case like "65555"
		else if(ceil($cards[0]/4) != (ceil($cards[1]/4))){
			if(ceil($cards[1]/4) == ceil($cards[2]/4) && ceil($cards[2]/4) == ceil($cards[3]/4) && ceil($cards[3]/4) == ceil($cards[4]/4)){
				$currentHand = array("7", $cards[1]);
			}
			else {
				goto full_house;
				return false;
			}			
		}
		else {
			goto full_house;
			return false;
		}
		
		// 2.4.1 Check if "passed three times"
		if (checkIfLastThreeIsPass($r)) {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
		
		// 2.4.2 Chech if the hand is larger than the previous
		if($currentHand[0] > $prevHand[0]) {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
		else if ($currentHand[0] == $prevHand[0]) {
			if($currentHand[1] > $prevHand[1]){
				saveNewHand($r, join("-", $currentHand));
				return true;
			}
			else {
				goto full_house;
				return false;
			}
		}
		else {
			goto full_house;
			return false;
		}
	}	
	
		// 2.3 For Full House Case
	full_house:
	if($prevHand[0] == 6 || $prevHand[0] < 6 || $prevHand[0] == "PASS") {
		// For case like "66655"
		if(ceil($cards[1]/4) == (ceil($cards[2]/4))){
			if(ceil($cards[0]/4) == ceil($cards[1]/4) && ceil($cards[3]/4) == ceil($cards[4]/4)){
				$currentHand = array("6", max($cards));
			}
			else {
				goto flower;
				return false;
			}
		}
		// For case like "66555"
		else if(ceil($cards[1]/4) != (ceil($cards[2]/4))){
			if(ceil($cards[0]/4) == ceil($cards[1]/4) && ceil($cards[2]/4) == ceil($cards[3]/4) && ceil($cards[3]/4) == ceil($cards[4]/4)){
				$currentHand = array("6", $cards[2]);
			}
			else {
				goto flower;
				return false;
			}			
		}
		else {
			goto flower;
			return false;
		}
		
		// 2.3.1 Check if "passed three times"
		if (checkIfLastThreeIsPass($r)) {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
		
		// 2.3.2 Chech if the hand is larger than the previous
		if($currentHand[0] > $prevHand[0]) {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
		else if ($currentHand[0] == $prevHand[0]) {
			if($currentHand[1] > $prevHand[1]){
				saveNewHand($r, join("-", $currentHand));
				return true;
			}
			else {
				goto flower;
				return false;
			}
		}
		else {
			goto flower;
			return false;
		}
	}

		// 2.2 For Flush Case
	flower:
	if($prevHand[0] == 5 || $prevHand[0] < 5 || $prevHand[0] == "PASS") {
		if($cards[4]%4 == $cards[3]%4 && $cards[3]%4 == $cards[2]%4 && $cards[2]%4 == $cards[1]%4 && $cards[1]%4 == $cards[0]%4 && $cards[0]%4 == $cards[4]%4){
			$currentHand = array("5", max($cards));
		}
		else {
			goto straight; 
			return false;
		}
		
		// 2.2.1 Check if "passed three times"
		if (checkIfLastThreeIsPass($r)) {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
		
		// 2.2.2 Chech if the hand is larger than the previous
		if($currentHand[0] > $prevHand[0]) {
			saveNewHand($r, join("-", $currentHand));
			return true;
		}
		else if ($currentHand[0] == $prevHand[0]) {
			if($currentHand[1] > $prevHand[1]){
				saveNewHand($r, join("-", $currentHand));
				return true;
			}
			else {
				goto straight;
				return false;
			}
		}
		else {
			goto straight;
			return false;
		}
	}
	
		// 2.1 For Straight Case
	straight:
	if($prevHand[0] == 4 || $prevHand[0] == "PASS") {
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
			if(checkValidity($cards)){
				saveNewHand($r, join("-", $currentHand));
				return true;
			}
			else {
				return false;
			}
		}
		
		// 2.1.2 Chech if the hand is larger than the previous
		if ($currentHand[1] < $prevHand[1]) return false;
		else {
			if(checkValidity($cards)){
				saveNewHand($r, join("-", $currentHand));
				return true;
			}
			else {
				return false;
			}
		}
	}
}

function fetchLast(){
	$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
	$q = $db -> prepare("SELECT firstLast, secondLast, thirdLast, forthLast FROM game WHERE roomid = ? LIMIT 1");
	$q->execute(array($_REQUEST["roomid"]));
	$r = $q->fetch();

	if (!$r) {
		error_log("Session not found!");
	} else return $r;
}

function saveNewHand($r, $newHand){
	$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
	$q = $db -> prepare("UPDATE game SET firstLast = ?, secondLast = ?, thirdLast = ?, forthLast = ? WHERE roomid = ?");
	$q->execute(array($newHand, $r["firstLast"], $r["secondLast"], $r["thirdLast"], $_REQUEST["roomid"]));
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

function checkValidity($handToCheck){
	$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
	$q = $db -> prepare("SELECT turn FROM game WHERE roomid = ?");
	$q-> execute(array($_REQUEST["roomid"]));
	$user = $q->fetch();
	
	$q2 = $db -> prepare("SELECT ? FROM game WHERE roomid = ?");
	$q2-> execute(array(("card".$user["turn"]) ,$_REQUEST["roomid"]));
	$r2 = $q2->fetch(PDO::FETCH_ASSOC);

	print_r($r2);
	
	$origHand = explode(",", $r2[("cardwest")]);
	// Check if the hand presents in the user's cards
	$checkingArray = array_diff($origHand, $handToCheck);
	if (empty($checkingArray)) return false;
	else {
		$newHand = implode(",",array_diff($origHand, $handToCheck));
		echo $newHand;
		$q3 = $db -> prepare("UPDATE game SET ? = ? WHERE roomid = ?");
		$q3-> execute(array(("card".$user["turn"]) ,$newHand, $_REQUEST["roomid"]));
		return true;
	}
	// if the function return false, then "hand-logic" should return false as the required cards are not held by the user
	// if the function return ture, it means that the submission is valid and that the card(s) have been removed from the fielcd 
}

/* Debugging Section for Arthur */

function test(){
	//while(true){
		//echo "Enter the hand: ";
		//$input = fgets(STDIN);
		//$input = mb_substr($input, 0, -1);
		//$input = "28,24,20,16,12";
		//$input = "50,4,3,2,1";
		//$input = "52,48,43,40,34";
		//$input = "52,48,44,40,36";	
		//$input = "28,27,20,19,18";
		$input = "52,48,44,40,34";
		//$input = "52,48,44,40,32";
		$input = explode(",", $input);
		$_REQUEST["roomid"] = 1;
		if (checkLogic($input)) echo "It is valid.\n";
		else echo "It is invalid.\n";
	//}
}
test();

?>
