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
		<form onsubmit="return showDeck();">
			Room ID: <input class="textfield" type="number" name="roomid"><br>
			Session: <input class="textfield" type="number" name="gamesession"><br>
			<input type="checkbox" name="playerid[]" value="0">North<br>
			<input type="checkbox" name="playerid[]" value="1">South<br>
			<input type="checkbox" name="playerid[]" value="2">East<br>
			<input type="checkbox" name="playerid[]" value="3">West<br>
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
	function showDeck(){
		var data = $("form").serialize();	
		data+='&action=getHand';
		$.ajax({
			url: "room-process.php",
			type: 'POST',
			data: data 
		}).done(function(deck){
			var show = eval(deck);
			show.forEach(function(element, index, array){
				var text=$("<div>"+element+"</div>");
				//$("#player"+index).append(text);
				//console.log(array);
				var count=0;
				array[index].forEach(function(el, ind, arr){
					var img = $("<img class=cards id=player"+index+"card"+count+" src=cardsInNumber/"+el+".png>");
					$("#player"+index).append(img);
				});
			});
			//var t=$("<div>"+deck+"</div>");
			//$("#playground").append(t);
		});
		return false;
	}
	</script>
</html>