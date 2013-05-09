<?php

// $cards is an array 

function fetchLast(){
	$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
	$q = $db -> prepare("SELECT firstLast, secondLast, thirdLast, forthLast FROM game WHERE sessionid = ? LIMIT 1");
	$q->execute(array($_REQUEST["sessionid"]));
	$r = $q->fetch();
	if (!$r) {
		error_log("Session not found!")
	} else return $r;
}

function oneCard($cards){
	$r = fetchLast();
	if ($r.firstLast.length != 1) return false;
	else $lastValue = split("-", $r.firstLast);

	if ($cards[0] < $lastValue[1]) return false;
	else return true;
}

function twoCard($cards){
	$r = fetchLast();
	// not implemented yet
}

function threeCard($cards){
	$r = fetchLast();
	// not implemented yet
}

function fiveCard($cards){
	$r = fetchLast();
	// not implemented yet
}

?>