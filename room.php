<?php 
	$roomid = (int)$_REQUEST['roomid'];
function getGamesession(){
	global $roomid;
	if(isset($_COOKIE['gamesession'])){
		$gamesession = $_COOKIE['gamesession'];
	} else {$gamesession = $roomid;}
	return $gamesession;
}
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
		Session: <span id="gamesession"></span>
		<button type="button" onclick="getGamesession()">Refresh</button> 
		<form onsubmit="return showDeck();">
			<input type="radio" name="playerid" value="0">North<br>
			<input type="radio" name="playerid" value="1">South<br>
			<input type="radio" name="playerid" value="2">East<br>
			<input type="radio" name="playerid" value="3">West<br>
			<input type="submit" name="submit" value="Submit" />
		</form>
		<div id="playground">
			<div id="player0">Player North<br></div>
			<div id="player1">Player South<br></div>
			<div id="player2">Player East<br></div>
			<div id="player3">Player West<br></div>
		</div>
	</body>
	
	<script src="incl/jquery.js"></script>
	<script>
	var currentGamesession=<?php echo getGamesession();?>;
	
	function showDeck(){
		var data = $("form").serialize();	
		data+="&roomid=<?php echo $roomid; ?>"+"&gamesession="+currentGamesession+'&action=getHand';
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
	
	function getGamesession(){
		var data;
		data="roomid=<?php echo $roomid; ?>" + '&action=resetSession';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			data: data 
		}).done(function(session){
			currentGamesession = session;
			$("#gamesession").html(currentGamesession);
		});
		return false;
	}
	$("#gamesession").html(currentGamesession);
	</script>
</html>