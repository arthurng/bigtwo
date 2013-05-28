<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/* -------------------------------------------------*/ printToLog("Called game-server");
// include the hand-logic
require 'hand-logic.php';
// hand roomid player
$db = new PDO('mysql:host=ec2-54-251-38-11.ap-southeast-1.compute.amazonaws.com;dbname=bigtwo', "bigtwoadmin", "csci4140");
$roomid = $_REQUEST['roomid'];

// get the current user
$q = $db -> prepare("SELECT turn FROM game WHERE roomid = ?");
$q->execute(array($roomid));
$r = $q->fetch();
$current = $r['turn'];

// client submit his name
$instance = $_REQUEST['player'];

////function collections////
/*
function confirm(){
	$hand = $_REQUEST['hand'];//$hand is a string
	if(checkLogic($hand)==true){
		return 1;
	}//return true for ajax
	else{return -1;}
}

function pass(){
	$r = fetchLast();
	saveNewHand($r, 'PASS');
	return 1;
}
*/
function printToLog($str){
	// Comment out to disable
	error_log($str);
}

function checking(){
	global $current, $instance, $roomid;
	/* -------------------------------------------------*/ printToLog("running checking function");
	/* -------------------------------------------------*/ printToLog("sending the hand: " . $_REQUEST["hand"]);
	if ($current != $instance) return 'false';
	$hand = $_REQUEST['hand'];
	// $hand = explode(',', $_REQUEST['hand']);

	// Call checkLogic in hand-logic.php
	$validity  = checkLogic($hand);
	/* -------------------------------------------------*/ printToLog("checking finished, return: " . $validity);

	// Handle two cases for validity
	if ($validity){
		setFlag("done", 1);
		setFlag("hand", $_REQUEST["hand"]);
		/* -------------------------------------------------*/ printToLog("returning true to call");
		return 'true';
	} else {
		setFlag("hand", null);
		/* -------------------------------------------------*/ printToLog("returning false to call");
		return 'false';
	}
}

function pass(){
	global $current, $instance, $roomid;
	/* -------------------------------------------------*/ printToLog("running pass function");
	if ($current != $instance) return 'false';
	
	$r = fetchLast();
	$newHand = "PASS";
	saveNewHand($r, $newHand);

	setFlag("done", 1);
	return 'true';
}

function longpoll(){
	global $current, $instance, $roomid;
	printToLog("The current is: ".$current." and the instance is from: ".$instance);
	if ($current == $instance){
		$temp = longpoll_master();
		return $temp;
	}
	else {	
		$temp = longpoll_slave();
		return $temp;
	}
}

function longpoll_master(){
	/* -------------------------------------------------*/ printToLog("MASTER: longpoll_master called");
	global $current, $instance, $roomid, $db;

	/* -------------------------------------------------*/ printToLog("MASTER: Resetting variables");
	// Save the beginning time of the Master poll & reset the session hand
	$startTime = time();
	$curtime = 0;
	setFlag("timer", $startTime);
	setFlag("hand", null);
	setFlag("done", 0);
	setFlag("ready", 1);
	error_log("-------The ready flag is now 1");
	$increment = 0;

	/* -------------------------------------------------*/ printToLog("MASTER: Start loop to wait for TO or done");
	// Loop to check the 'done' parameter (TOflag = Timeout flag)
	error_log("Ready Session = ".getFlag("ready"));
	do {
		usleep(100000);
		clearstatcache();

		//testing flag -- display in error log
		if ($curtime != time()){
			printToLog("TIME: " . $increment++);
			$curtime = time();
		}
		//testing flag -- display in error log

		if ($startTime+20 <= time()) pass();
		$e = getFlag("done");
	} while ($e != 1) ;

	/* -------------------------------------------------*/ printToLog("MASTER: Loop ended, update current user and preparing to end");
	switch ($current) {
		case 'north':
			$current = 'east';
			break;
		case 'east':
			$current = 'south';
			break;
		case 'south':
			$current = 'west';
			break;
		case 'west':
			$current = 'north';
			break;
	}
	$q = $db -> prepare("UPDATE game SET turn = ? WHERE roomid = ?");
	$q->execute(array($current, $roomid));

	// Read the session hand before return
	$returnHand = getFlag("hand");
	$gameEnded = checkGameEnd();
	setFlag("ready", 0);
	error_log("-------The ready flag is now 0");

	/* -------------------------------------------------*/ printToLog("MASTER: Close connection and return hand");
	/* -------------------------------------------------*/ // printToLog(print_r($returnHand, 1));
	return array('status' => 'mastered', 'hand' => $returnHand, 'ended' => $gameEnded);	
}

function longpoll_slave(){
	/* -------------------------------------------------*/ printToLog("SLAVE: longpoll_slave called");
	global $current, $instance, $roomid;
	
	/* -------------------------------------------------*/ printToLog("SLAVE: Loop to wait for the ready flag");
	do {
		error_log("loop 1 of instance: ".$instance);
		usleep(200000);
		clearstatcache();		
		$e = getFlag("ready");
	} while ($e != 1);

	/* -------------------------------------------------*/ printToLog("SLAVE: Start loop to wait for done: READY= 1");
	do {
		error_log("loop 2 of instance: ".$instance);		
		usleep(100000);
		clearstatcache();
		$e = getFlag("done");
	} while ($e != 1) ;

	/* -------------------------------------------------*/ printToLog("SLAVE: Loop ended, preparing to end");
	// Read the session hand before return
	$returnHand = getFlag("hand");
	$gameEnded = checkGameEnd();
	/* -------------------------------------------------*/ printToLog("SLAVE: Close connection and return hand");
	// return 'true';
	return array('status' => 'slaved', 'hand' => $returnHand, 'ended' => $gameEnded);	
}

function getFlag($name){
	global $roomid, $db;
	switch ($name){
		case "ready":
			$q = $db -> prepare("SELECT ready FROM session WHERE roomid = ?");
			break;
		case "timer";
			$q = $db -> prepare("SELECT timer FROM session WHERE roomid = ?");
			break;
		case "hand";
			$q = $db -> prepare("SELECT hand FROM session WHERE roomid = ?");
			break;
		case "done";
			$q = $db -> prepare("SELECT done FROM session WHERE roomid = ?");
			break;
	}
	$q->execute(array($roomid));
	$r = $q->fetch();
	return $r[0];
}

function setFlag($name, $value){
	global $roomid, $db;
	switch ($name){
		case "ready":
			$q = $db -> prepare("UPDATE session SET ready = ? WHERE roomid = ?");
			break;
		case "timer";
			$q = $db -> prepare("UPDATE session SET timer = ? WHERE roomid = ?");
			break;
		case "hand";
			$q = $db -> prepare("UPDATE session SET hand = ? WHERE roomid = ?");
			break;
		case "done";
			$q = $db -> prepare("UPDATE session SET done = ? WHERE roomid = ?");
			break;
	}
	$q->execute(array($value, $roomid));
	return true;
}

// Check whether the game is ended
function checkGameEnd(){
	global $roomid, $db;
	$q = $db -> prepare("SELECT * FROM game WHERE roomid = ? LIMIT 1");
	$q->execute(array($roomid));
	$r = $q->fetch();
	// When one of the players played all cards
	if($r['cardnorth'] == null || $r['cardeast'] == null || $r['cardsouth'] == null || $r['cardwest'] == null){
		return '1';
	}
	return '0';
}

header('Content-Type: application/json');
$return = json_encode(call_user_func($_REQUEST['action']));
error_log(print_r($return, 1));
error_log("Client responsible: ".$instance);
echo $return;
exit(0);
