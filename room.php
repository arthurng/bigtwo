<?php 
	$roomid = (int)$_REQUEST['roomid'];
?>
<html>	
	<head>
		<title>BIG TWO - Game room</title>
		<link rel="stylesheet" type="text/css" href="general.css">
		<style>
			#seatForm{
				float: left;
				display: block;
			}
			
			/*0,1,2,3 in north south east west order*/
			#playground #centre{
				height: 520px;
				width: 520px;
				margin-top: 170px;
				margin-left: 250px;	
				position: absolute;
				border: 5px solid purple;
				border-radius: 5px;
				background-color: 	rgba(0, 0, 0, .5);
				-moz-box-shadow:    inset 0 0 20px #000000;
				-webkit-box-shadow: inset 0 0 20px #000000;
				box-shadow:         inset 0 0 20px #000000;	
			}
			#player0{
				border:5px solid blue;

			}
			#player1{
				border:5px solid green;

			}
			#player2{
				border:5px solid yellow;

			}
			#player3{
				border:5px solid red;

			}
			.playat{
				height: 160px;
				width: 520px; 
				position: absolute;
				border: #c8c8c8 solid 1px;
				border-radius: 5px;
				background-color: 	rgba(0, 0, 0, .5);
				-moz-box-shadow:    inset 0 0 20px #000000;
				-webkit-box-shadow: inset 0 0 20px #000000;
				box-shadow:         inset 0 0 20px #000000;	
			}
			.playat.top{
				margin-left: 250px;			
			}
			.playat.bottom{
				margin-left: 250px;
				margin-top: 700px;
			}
			.playat.right{
				margin-top: 350px;
				margin-left: 600px;
				transform: rotate(270deg);
				-ms-transform: rotate(270deg); /* IE 9 */
				-webkit-transform: rotate(270deg); /* Safari and Chrome */
			}
			.playat.left{
				margin-top: 350px;
				margin-left: -100px;
				transform: rotate(90deg);
				-ms-transform: rotate(90deg); /* IE 9 */
				-webkit-transform: rotate(90deg); /* Safari and Chrome */
			}			
			.playat.bottom #cardcorrection{
				margin-left: 100px;
			}
			
			.cards{
				width:100px;
				position:relative;
				margin-left: -70px;
				z-index: 2;
			}

			.cardsCenter{
				width:100px;
				position:fixed;
				left: 10px;
				top: 10px;
				z-index: 2;
			}

			.currentPlayer{
				color: yellow;
			}

		</style>
	</head>
	<body>
		<form id="seatForm" onsubmit="return getSeat();">
			Please get a seat:<br>
			<span></span><input type="radio" name="playerid" value="0">North<br>
			<span></span><input type="radio" name="playerid" value="1">South<br>
			<span></span><input type="radio" name="playerid" value="2">East<br>
			<span></span><input type="radio" name="playerid" value="3">West<br>
			<input type="submit" name="submit" value="Get seat" />
		</form>
		<!-- Debugging --
		<button type="button" onclick="leaveSeat();">Stand up</button> 
		<button type="button" onclick="showDeck();">Get cards</button>
		<!-- End of Debugging -->
		<div id="playground">
			<div class="playat top" id="player0">
				&nbsp;Player North<span id="user0"></span><br>
				<span id="cardcorrection"></span>
			</div>
			<div class="playat bottom" id="player1">
				&nbsp;Player South<span id="user1"></span><br>
				<span id="cardcorrection"></span>
			</div>
			<div class="playat left" id="player2">
				&nbsp;Player East<span id="user2"></span><br>
				<span id="cardcorrection"></span>
			</div>
			<div class="playat right" id="player3">
				&nbsp;Player West<span id="user3"></span><br>
				<span id="cardcorrection"></span>
			</div>
			<div id="centre">
				<span id="systemMessage"></span>
			</div>
		</div>
	</body>
	
	<script src="incl/jquery.js"></script>
	<script type="text/javascript" src="endgame.js"></script>
	<script>
	var playerid;
	var checkEnd;
	var hand = new Array();
	var currentPlayer, myPosition;
	
	// Print player's cards
	function showDeck(){
		console.log("showDeck");
		data="roomid=<?php echo $roomid; ?>" + '&action=getHand';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			data: data 
		}).done(function(deck){		
			// Error handling
			if(typeof deck != 'object'){
				// When there is an empty seat
				if(deck == 'Failed: wait'){
					check4players = setTimeout(function(){
						showDeck();
					}
					, 2000);
				}
				else {
					// Display error message
					alert(deck);
				}
				return false;
			}
			
			var show = deck;
			// Get index
			index = playerid;
			
			// Redraw player UI
			spectatorGUI();
			playerGUI(playerid, 'READY');
			
			// Print cards
			for(var i=0; i < show.length; i++){
				var img = $("<img class='cards card"+show[i]+"' id=player"+index+"card"+i+" src=cardsInNumber/"+show[i]+".png>");
				$("#player"+index).append(img);
				$(img).click(function(e){select(e);});
			}


			// push the confirm and pass button
			confirm = $('<br><button type="button">Confirm</button>');
			pass = $('<button type="button">Pass</button>');
			$(confirm).attr('onclick', 'fire_checking();');
			$(pass).attr('onclick', 'pass();');
			$('.bottom').append(confirm);
			$('.bottom').append(pass);
			
			fire_longpoll();


			// Check game End
			/*
			checkEnd = setInterval(function(){
				checkGameEnd();
			}
			, 2000);
			*/
		});
		return false;
	}
	
/*
	function confirmFire(){
		joinedHand = hand.join(',');
		player = id2player(index);
		data="roomid=<?php echo $roomid; ?>" + '&action=confirm' + '&hand=' + joinedHand + '&player=' + player;
		$.ajax({
			url: "game-server.php",
			type: 'POST',
			data: data
		}).done(function(validity){
			if(validity==1){
				$('#systemMessage').text('Okay. Next user.');
				for(var i=0; i<hand.length; i++)
					//alert(eval("\"img[src$=\'cardsInNumber/"+hand[i]+".png\'][class=\'cards\']\""));
					$(eval("\"img[src$=\'cardsInNumber/"+hand[i]+".png\'][class=\'cards\']\"")).remove();//remove img by pathname
			}else{
				$('#systemMessage').text('Your hand has some problem. Please choose again.');
			}
			
			//initiate longpoll(slave)
		});
	}

	function passFire(){
		data="roomid=<?php echo $roomid; ?>" + '&action=pass';
		$.ajax({
			url: "game-server.php",
			type: 'POST',
			data: data 
		}).done(function(validity){
			if(validity==1){
				$('#systemMessage').text('Okay you passed. Next user.');
			}
			//initiate longpoll(slave)
		});
	}
*/
	function select(e){
		e.stopPropagation();
		// console.log(e);
		$(e.target).off("click");
		$(e.target).animate({"top": "-=50px"}, "fast", null, function(){});
		$(e.target).click(function(e){unselect(e);});
		// choose cards
		chosenCard = e.target.src.replace(/^.*[\\\/]/, '').replace(/\.[^.]*$/,'');
		hand.push(chosenCard);
		console.log(hand);
	}

	function unselect(e){
		e.stopPropagation();
		// console.log(e);
		$(e.target).off("click");
		$(e.target).animate({"top": "+=50px"}, "fast", null, function(){});
		$(e.target).click(function(e){select(e);});
		// unchoose cards
		unchosenCard = e.target.src.replace(/^.*[\\\/]/, '').replace(/\.[^.]*$/,'');
		hand.splice(hand.indexOf(unchosenCard), 1);
		console.log(hand);
	}

	// Add player to a seat
	function getSeat(){
		leaveSeat();
		var data = $("form").serialize();
		data+="&roomid=<?php echo $roomid; ?>" + '&action=getSeat';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			async: false,
			data: data 
		}).done(function(playerId){
			if(playerId == null){
				alert('It is chosen');
				return false;
			}
			// Store playerid
			playerid = playerId;
			updateSeats();
		});
		return false;
	}
	
	// Remove player from a seat
	function leaveSeat(){
		var data;
		data="roomid=<?php echo $roomid; ?>" + '&action=leaveSeat';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			async: false,
			data: data 
		}).done(function(result){
			// Disable checking game end
			clearInterval(checkEnd);
			
			updateSeats();			
			// Redraw player UI
			spectatorGUI();
				// Redraw game play area
				$("#player0").attr('class', 'playat top');
				$("#player1").attr('class', 'playat bottom');
				$("#player2").attr('class', 'playat right');
				$("#player3").attr('class', 'playat left');
		});
		return false;
	}
	
	// Update the status of each seat
	function updateSeats(){
		console.log("updateSeats");
		var data;
		data="roomid=<?php echo $roomid; ?>" + '&action=updateSeats';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			data: data 
		}).done(function(result){	
			if(typeof result == 'object'){
				// Print all player names
				for(i=0;i<4;i++){
					$("#user"+i).html(' - '+result[i]);
				}
				// Store playerid
				playerid = result[4];
				
				//myPosition = $('#seatForm :checked').val(); // Arthur's
				myPosition = playerid;
				myPosition = id2player(myPosition).toLowerCase(); // Arthur's				
				getCurrentPlayer();

				// Redraw game play area
				playerGUI(playerid, 'NOTREADY');
				$("#player"+playerid).attr('class', 'playat bottom');
				switch(playerid){
					case 0:
						$("#player1").attr('class', 'playat top');
						$("#player2").attr('class', 'playat left');
						$("#player3").attr('class', 'playat right');
						addHighlight();
						break;
					case 1:
						$("#player0").attr('class', 'playat top');
						$("#player2").attr('class', 'playat right');
						$("#player3").attr('class', 'playat left');
						addHighlight();
						break;
					case 2:
						$("#player0").attr('class', 'playat right');
						$("#player1").attr('class', 'playat left');
						$("#player3").attr('class', 'playat top');
						addHighlight();
						break;
					case 3:
						$("#player0").attr('class', 'playat left');
						$("#player1").attr('class', 'playat right');
						$("#player2").attr('class', 'playat top');
						addHighlight();
						break;
					default:
						spectatorGUI();
						break;
				}				
			}
			else{
				// Redraw game play area
				$("#player0").attr('class', 'playat top');
				$("#player1").attr('class', 'playat bottom');
				$("#player2").attr('class', 'playat right');
				$("#player3").attr('class', 'playat left');
			}
		});
		return false;
	}updateSeats();
	
	function checkGameEnd(){
		var data;
		data="roomid=<?php echo $roomid; ?>" + '&action=checkGameEnd';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			data: data 
		}).done(function(result){
			if (result) endGame("<?php echo $roomid; ?>");
			console.log('Still playing...');
		});
		return false;
	}
	
	// Renew the game session, which should be called after each game
	function renewSession(){
		var data;
		data="roomid=<?php echo $roomid; ?>" + '&action=resetSession';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			data: data 
		});
		return false;
	}
	
	// Draw a gameplay UI for spectators
	function spectatorGUI(){
		$("#player0 > img").remove();
		$("#player1 > img").remove();
		$("#player2 > img").remove();
		$("#player3 > img").remove();
		
		$("#user0 > button").remove();
		$("#user1 > button").remove();
		$("#user2 > button").remove();
		$("#user3 > button").remove();
		
		$("#seatForm").css("display", "block");
		
		return true;
	}
	
	// Draw a gameplay UI for players
	function playerGUI(playerid, status){
		$("#seatForm").css("display", "none");
		if(status == 'NOTREADY'){
			$("#user"+playerid).append('&nbsp;<button type="button" onclick="showDeck();">Ready</button>');
			$("#user"+playerid).append('&nbsp;<button type="button" onclick="leaveSeat();">Stand up</button> ');
		} 
		else if(status == 'READY'){
			$("#user"+playerid).append('&nbsp;<button type="button" onclick="leaveSeat();">Stand up</button>');
		}
		
		return true;
	}
	
	// Translate player id to player name
	function id2player(id){
		id = parseInt(id);
		switch(id){
			case 0:
				player = 'North';
				break;
			case 1:
				player = 'South';
				break;
			case 2:
				player = 'East';
				break;
			case 3:
				player = 'West';
				break;
			default:
				player = 'Invalid';
				break;
		}
		return player;
	}

	function player2id(player){
		switch(player){
			case "north":
				player = '0';
				break;
			case "south":
				player = '1';
				break;
			case "east":
				player = '2';
				break;
			case "west":
				player = '3';
				break;
			default:
				player = 'Invalid';
				break;
		}
		return player;
	}


	// Arthur's ---------------------below---------------------
	// This function should be called first
	function getCurrentPlayer(){
		$.ajax({
			url: "room-process.php",
			type: "POST",
			async: false,
			data: {
				action: "getCurrentPlayer",
				roomid: '<?php echo $roomid; ?>'
			}
		}).done(function(e){
			currentPlayer = e;
		});
	}

	function addHighlight(){
		var playerid = player2id(currentPlayer);
		$("#player"+playerid).addClass("currentPlayer");
		console.log("#player"+playerid+" adding highlight");
	}

	function removeHighlight(){
		playerid = player2id(currentPlayer);
		$("#player"+playerid).removeClass("currentPlayer");
	}

	function updateCards(cards){
		for (ind in cards){
			$(".card"+cards[ind]).removeClass('cards').addClass('cardsCenter');
		}
	}

	function fire_checking(){
		joinedHand = hand.join(',');
		player = id2player(index);
		$.ajax({
			url: "game-server.php",
			type: 'POST',
			data: {
				action: "checking",
				roomid: '<?php echo $roomid; ?>',
				hand: joinedHand,
				player: myPosition
			}
		}).done(function(validity){
			if(validity == true){
				console.log("the hand is valid");
			}else{
				console.log("the hand is NOT valid");
			}
			
			//initiate longpoll(slave)
		});
	}

	function pass(){
		$.ajax({
			url: "game-server.php",
			type: "POST",
			data: {
				action: 'pass',
				roomid: '<?php echo $roomid; ?>',
				player: myPosition,
			}
		});		
	}

	function fire_longpoll(){
		console.log("longpoll " + myPosition + " -> started");
		$.ajax({
			url: "game-server.php",
			type: "POST",
			data: {
				action: 'longpoll',
				roomid: '<?php echo $roomid; ?>',
				player: myPosition
			}
		}).always(function(e){
			console.log("longpoll " + myPosition + " -> terminated");
			console.log(e);
			removeHighlight();
			currentPlayer = switchPlayer(currentPlayer);
			updateCards(e["hand"]);
			addHighlight();
			fire_longpoll();
		});
	}

	function switchPlayer(curr){
		switch(curr){
			case "north":	return "east";
			case "east":	return "south";
			case "south":	return "west";
			case "west":	return "north";
		}
	}

	</script>
</html>
