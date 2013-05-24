<?php
// include the hand-logic
require 'hand-logic.php';

// hand roomid player
$roomid = $_REQUEST['roomid'];

// get the current user
$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
$q = $db -> prepare("SELECT turn FROM game WHERE roomid = ?");
$q->execute(array($roomid));
$r = $q->fetch();
$current = $r['turn'];

// client submit his name
$instance = $_REQUEST['player'];

function checking(){
	global $current, $instance, $roomid;
	if ($current != $instance) return false;
	$hand = explode(',', $_REQUEST['hand']);

	// Call checkLogic in hand-logic.php
	$validity  = checkLogic($hand);

	$roomSessId = 'GAMESESSION'.$roomid;
	session_name($roomSessId);

	// Handle two cases for validity
	if ($validity){
		session_start();
			$_SESSION['done']=1;
			$_SESSION['hand']=$_REQUEST['hand'];
		session_write_close();
		return 'true';
	} else {
		session_start();
			$_SESSION['hand']=null;
		session_write_close();
		return 'false';
	}
}

function longpoll(){
	global $current, $instance, $roomid;
	if ($current == $instance) longpoll_master();
	else longpoll_slave();
}

function longpoll_master(){
	global $current, $instance, $roomid;
	$roomSessId = 'GAMESESSION'.$roomid;
	session_name($roomSessId);

	// Save the beginning time of the Master poll & reset the session hand
	session_start();
		$t = time();
		$_SESSION['timer'] = $t;
		$_SESSION['hand'] = null;
	session_write_close();
	
	// Loop to check the 'done' parameter (TOflag = Timeout flag)
	$TOflag = 0;
	do {
		usleep(100000);
		clearstatcache();
		if ($t+10 <= time()) $TOflag = 1;
		session_start();
			if ($TOflag) $_SESSION['done'] = 1;
			$e = $_SESSION['done'];
		session_write_close();
	} while ($e != 1) ;

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
	session_start();
		$returnHand = $_SESSION['hand'];
	session_write_close();

	return array('status' => 'proceed', 'hand' => $returnHand);	
}

function longpoll_slave(){
	global $current, $instance, $roomid;
	$roomSessId = 'GAMESESSION'.$_REQUEST['roomid'];
	session_name($roomSessId);
	
	do {
		usleep(100000);
		clearstatcache();
		session_start();
			$e = $_SESSION['done'];
		session_write_close();
	} while ($e != 1) ;


	// Read the session hand before return
	session_start();
		$returnHand = $_SESSION['hand'];
	session_write_close();

	return array('status' => 'proceed', 'hand' => $returnHand);	
}


header('Content-Type: application/json');
echo json_encode(call_user_func($_REQUEST['action']));
