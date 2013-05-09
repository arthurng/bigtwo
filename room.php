<?php 
	$roomid = (int)$_REQUEST['roomid'];
?>
<html>
	<head>
		<style>
			/*0,1,2,3 in north south east west order*/
			#playground{
				border:0px solid purple;
			}
			#player0{
				border:5px solid blue;
				/*width:500px;
				margin:0 auto;
				top:200px;
				-webkit-transform: rotate(180deg); 
				-moz-transform: rotate(180deg);*/
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
			
			.cards{
				width:100px;
			}
		</style>
	</head>
	<body>
		Choose to show the player's card<br>
		<form onsubmit="return getSeat();">
			<input type="radio" name="playerid" value="0">North<br>
			<input type="radio" name="playerid" value="1">South<br>
			<input type="radio" name="playerid" value="2">East<br>
			<input type="radio" name="playerid" value="3">West<br>
			<input type="submit" name="submit" value="Submit" />
		</form>
		<button type="button" onclick="leaveSeat();">Stand up</button> 
		<button type="button" onclick="showDeck();">Get cards</button> 
		<div id="playground">
			<div id="player0">Player North<span id="user0"></span><br></div>
			<div id="player1">Player South<span id="user1"></span><br></div>
			<div id="player2">Player East<span id="user2"></span><br></div>
			<div id="player3">Player West<span id="user3"></span><br></div>
		</div>
	</body>
	
	<script src="incl/jquery.js"></script>
	<script>
	var currentGamesession; var playerid;
	
	function showDeck(){
		data="playerid="+playerid+"&roomid=<?php echo $roomid; ?>" + '&action=getHand';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			data: data 
		}).done(function(deck){
			var show = deck;
			// Get index
			tmp = data.split("&");
			index = tmp[0].replace("playerid=","");
			// Print cards
			for(var i=0; i < show.length; i++){
				var img = $("<img class=cards id=player"+index+"card"+i+" src=cardsInNumber/"+show[i]+".png>");
				$("#player"+index).append(img);
			}
		});
		return false;
	}
	
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
			playerid = playerId;
			checkSeats();
		});
		return false;
	}
	
	function leaveSeat(){
		var data;
		data="roomid=<?php echo $roomid; ?>" + '&action=leaveSeat';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			async: false,
			data: data 
		}).done(function(result){
			checkSeats();
			$("#player0 > img").remove();
			$("#player1 > img").remove();
			$("#player2 > img").remove();
			$("#player3 > img").remove();
		});
		return false;
	}
	
	function checkSeats(){
		var data;
		data="roomid=<?php echo $roomid; ?>" + '&action=checkSeats';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			data: data 
		}).done(function(result){			
			for(i=0;i<result.length;i++){
				$("#user"+i).html(' - '+result[i]);
			}
		});
		return false;
	}checkSeats();
	
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
	</script>
</html>