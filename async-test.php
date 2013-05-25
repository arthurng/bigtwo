<input type="text" id="action" placeholder="longpoll/checking" value="checking"></input>
<input type="text" id="roomid" placeholder="roomid" value="1"></input>
<input type="text" id="hand" placeholder="hand" value="1"></input>
<input type="text" id="player" placeholder="player" value="west"></input>
<input type="button" onclick="fire();" value="Fire"></input>
<div id="result" style="border: 1px black solid"></div>
<script type="text/javascript" src="incl/jquery.js"></script>
<script type="text/javascript">
	function fire(){
		$.ajax({
			url: "game-server.php",
			type: "POST",
			data: {
				action: $("#action").val(),
				roomid: $("#roomid").val(),
				hand: $("#hand").val(),
				player: $("#player").val(),
			}
		}).done(function(e){
			$("#result").html(e);
			console.log(e);
		});
	}
</script>
