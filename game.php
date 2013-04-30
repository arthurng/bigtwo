<html>
<body>
<form onsubmit="return getHand();">
Player ID (0-3): <input class="textfield" type="number" name="playerid"><br>
Room ID: <input class="textfield" type="number" name="roomid"><br>
Session: <input class="textfield" type="number" name="gamesession"><br>
<input type="submit" name="submit" value="Submit" />
</form>
<div id="result"></div>
</body>
<script type="text/javascript" src="incl/jquery.js"></script>
<script type="text/javascript">
function getHand(){
	var data = $("form").serialize();	
	data+='&action=getHand';
	$.ajax({
		url: "game-process.php",
		type: 'POST',
		data: data 
	}).done(function(test){
		var t=$("<div>"+test+"</div>");
		$("#result").append(t);
	});
	return false;
}
</script>
</html>