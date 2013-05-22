<html>
<style type="text/css">
.picture 			{position: inherit; left: 30px; vertical-align: middle;}
.username 			{position: inherit; left: 90px;}
.remainingCards 	{position: inherit; left: 280px; width: 120px; text-align: center}
#title 				{position: fixed; top: 10px}
#first 				{position: fixed; top: 50px}
#second 			{position: fixed; top: 110px}
#third 				{position: fixed; top: 170px}
#forth 				{position: fixed; top: 230px}
#scoreChange		{position: fixed; top: 10px; left: 500px; font-size: 72}
#comment			{position: fixed; top: 100px; left: 500px;}
#back				{position: fixed; top: 130px; left: 500px; border: 1px solid black}
</style>
<body>
	<div id="waitingScreen">
		<center><br><br><br>
			<img src='ui/loading.gif' /><br><br><br><br>
			Great game!<br>Please wait while we have your score calculated...
		</center>
	</div>
	<div id="resultScreen" style="display:none">
		<div id="title">
			<span class="picture">Player</span>
			<span class="username"></span>
			<span class="remainingCards">Remaining cards</span>
		</div>
		<div id="first">
			<span class="picture"></span>
			<span class="username"></span>
			<span class="remainingCards"></span>
		</div>
		<div id="second">
			<span class="picture"></span>
			<span class="username"></span>
			<span class="remainingCards"></span>
		</div>
		<div id="third">
			<span class="picture"></span>
			<span class="username"></span>
			<span class="remainingCards"></span>
		</div>
		<div id="forth">
			<span class="picture"></span>
			<span class="username"></span>
			<span class="remainingCards"></span>
		</div>
		<div id="scoreChange"></div>
		<div id="comment"></div>
		<div id="back">Return to the lobby</div>
	</div>
</body>
<script type="text/javascript" src="incl/jquery.js"></script>
<script type="text/javascript" src="result.js"></script>
<script type="text/javascript">
	roomid = parent.passUserid();
	getResults(roomid);
</script>
</html>	

