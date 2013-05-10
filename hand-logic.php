<?php

function test(){
	$input = fgets(STDIN);
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
	$firstLastValue = split("-", $r["firstLast"]);

	// 1. Check if previous hand consist of n cards
	if ($firstLastValue[0] != 1 && $firstLastValue[0] != "PASS") return false;

	// 2. Validate and calculate the current hand
	$currentHand = array("1", $cards[0]);

	// 3. Check if "passed three times"
	if (checkIfLastThreeIsPass()) {
		saveNewHand(join("-", $currentHand));
		return true;
	}

	// 4. Chech if the hand is larger than the previous
	if ($currentHand[1] < $firstLastValue[1]) return false;
	else {
		saveNewHand(join("-", $currentHand));
		return true;
	}
}

function twoCard($cards){
	$r = fetchLast();
	$firstLastValue = split("-", $r["firstLast"]);

	// 1. Check if previous hand consist of n cards
	if ($firstLastValue[0] != 2 && $firstLastValue[0] != "PASS") return false;

	// 2. Validate and calculate the current hand
	if ($cards[0]%4 != $cards[1]%4) return false;
	else {
		$currentHand = array("2", max($cards));
	}

	// 3. Check if "passed three times"
	if (checkIfLastThreeIsPass()) {
		saveNewHand(join("-", $currentHand));
		return true;
	}

	// 4. Chech if the hand is larger than the previous
	if ($currentHand[1] < $firstLastValue[1]) return false;
	else {
		saveNewHand(join("-", $currentHand));
		return true;
	}
}

function threeCard($cards){
	$r = fetchLast();
	// not implemented yet
}

function fiveCard($cards){
	$r = fetchLast();
	// not implemented yet
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

function saveNewHand($newHand){
	$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
	$q = $db -> prepare("SELECT firstLast, secondLast, thirdLast, forthLast FROM game WHERE sessionid = ? LIMIT 1");
	$q->execute(array($_REQUEST["sessionid"]));
	$r = $q->fetch();
	if (!$r) {
		error_log("Session not found!");
	} else {
		$q = $db -> prepare("UPDATE game SET firstLast = ?, secondLast = ?, thirdLast = ?, forthLast = ? WHERE sessionid = ?");
		$q->execute(array($newHand, $r["firstLast"], $r["secondLast"], $r["thirdLast"], $_REQUEST["sessionid"]));
	}
}

function checkIfLastThreeIsPass(){
	$r = fetchLast();
	if (($r["firstLast"]=="PASS") && ($r["secondLast"]=="PASS") && ($r["thirdLast"]=="PASS")) return true;
	else return false;
}

?>
