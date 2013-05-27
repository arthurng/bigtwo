<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/* -------------------------------------------------*/ printToLog("arrived");
// include the hand-logic
require 'hand-logic.php';
// hand roomid player
$roomid = $_REQUEST['roomid'];
$roomSessId = 'GAMESESSION'.$roomid;
session_name($roomSessId);
setSession("ready", 0);

// get the current user
$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
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
		setSession("done", 1);
		setSession("hand", $_REQUEST["hand"]);
		/* -------------------------------------------------*/ printToLog("returning true to call");
		return 'true';
	} else {
		setSession("hand", null);
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

	setSession("done", 1);
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
		//error_log("The slave call has been return to the main");
		//error_log(print_r($temp, 1));
		return $temp;
	}
}

function longpoll_master(){
	/* -------------------------------------------------*/ printToLog("MASTER: longpoll_master called");
	global $current, $instance, $roomid;

	/* -------------------------------------------------*/ printToLog("MASTER: Resetting variables");
	// Save the beginning time of the Master poll & reset the session hand
	$TOflag = 0;
	$startTime = time();
	$curtime = 0;
	setSession("timer", $startTime);
	setSession("hand", null);
	setSession("done", 0);
	setSession("ready", 1);
	$increment = 0;

	/* -------------------------------------------------*/ printToLog("MASTER: Start loop to wait for TO or done");
	// Loop to check the 'done' parameter (TOflag = Timeout flag)
	error_log("Ready Session = ".getSession("ready"));
	do {
		usleep(100000);
		clearstatcache();

		//testing flag -- display in error log
		if ($curtime != time()){
			printToLog("TIME: " . $increment++);
			$curtime = time();
		}
		//testing flag -- display in error log

		if ($startTime+5 <= time()) $TOflag = 1;
		if ($TOflag) setSession("done", 1);
		$e = getSession("done");
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
	$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
	$q = $db -> prepare("UPDATE game SET turn = ? WHERE roomid = ?");
	$q->execute(array($current, $roomid));

	// Read the session hand before return
	$returnHand = getSession("hand");
	setSession("ready", 0);

	/* -------------------------------------------------*/ printToLog("MASTER: Close connection and return hand");
	/* -------------------------------------------------*/ // printToLog(print_r($returnHand, 1));
	return array('status' => 'mastered', 'hand' => $returnHand);	
}

function longpoll_slave(){
	/* -------------------------------------------------*/ printToLog("SLAVE: longpoll_slave called");
	global $current, $instance, $roomid;
	$roomSessId = 'GAMESESSION'.$roomid;
	session_name($roomSessId);
	
	/* -------------------------------------------------*/ printToLog("SLAVE: Loop to wait for the ready flag");
	do {
		error_log("loop 1 of instance: ".$instance);
		usleep(200000);
		clearstatcache();		
		$e = getSession("ready");
	} while ($e != 1);

	/* -------------------------------------------------*/ printToLog("SLAVE: Start loop to wait for done: READY= 1");
	do {
		error_log("loop 2 of instance: ".$instance);		
		usleep(100000);
		clearstatcache();
		$e = getSession("done");
	} while ($e != 1) ;

	/* -------------------------------------------------*/ printToLog("SLAVE: Loop ended, preparing to end");
	// Read the session hand before return
	$returnHand = getSession("hand");

	/* -------------------------------------------------*/ printToLog("SLAVE: Close connection and return hand");
	// return 'true';
	return array('status' => 'slaved', 'hand' => $returnHand);	
}

function getSession($name){
	session_start();
	$s = $_SESSION[$name];
	session_write_close();
	return $s;
}

function setSession($name, $value){
	session_start();
	$_SESSION[$name] = $value;
	session_write_close();
	return true;
}

header('Content-Type: application/json');
$return = json_encode(call_user_func($_REQUEST['action']));
error_log(print_r($return, 1));
error_log("Client responsible: ".$instance);
echo $return;
exit(0);
