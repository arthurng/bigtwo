Roomid:
<input type="text" id="roomid" placeholder="roomid" value=""></input>
<input type="button" id="flp" value="Fire Longpoll"></input>
hand:
<input type="text" id="hand" placeholder="hand" value="1"></input>
player:
<input type="text" id="player" placeholder="player" value=""></input>
<input type="button" onclick="fire_check();" value="Fire Check"></input>
<br><br>
Instruction:<br>
1. Open Console (remember to disable the debugging in hand-logic)<br>
2. Enter roomid and press "Fire Longpoll"<br>
3. Enter the hand you want to place and the player you want to be<br>
4. If the card check is successful: you will see "checkingpoll result: true" and the longpolls would terminate<br>
5. If the card check is no successful: you will see "checkingpoll result: false" and the longpolls would NOT terminate<br>
6. The longpolls would terminate after 20 seconds.<br>
7. Start again by by repeating from STEP 2.<br>

<script type="text/javascript" src="incl/jquery.js"></script>
<script type="text/javascript">
	$("#flp")
		.click(function(){
			fire_longpoll("north");
		})
		.click(function(){
			fire_longpoll("east");
		})
		.click(function(){
			fire_longpoll("south");
		})
		.click(function(){
			fire_longpoll("west");
		});

	function fire_longpoll(player){
		console.log("longpoll " + player + " -> started");
		$.ajax({
			url: "game-server.php",
			type: "POST",
			data: {
				action: 'longpoll',
				roomid: $("#roomid").val(),
				player: player,
			}
		}).done(function(e){
			console.log("longpoll " + player + " -> terminated");
			console.log(e);
		});
	}

	function fire_check(){
		var currentPlayer=$("#player").val();
		console.log("checkingpoll -> started");
		$.ajax({
			url: "game-server.php",
			type: "POST",
			data: {
				action: 'checking',
				roomid: $("#roomid").val(),
				hand: $("#hand").val(),
				player: currentPlayer,
			}
		}).done(function(e){
			console.log("checkingpoll -> terminated");
			console.log("checkingpoll result: " + e);
		});
	}
</script>
