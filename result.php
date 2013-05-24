<html>
<link rel="stylesheet" type="text/css" href="ui/loading.css">
<style type="text/css">
	@font-face {
		font-family: 'Lobster13Regular';
		src: local('Lobster'), url('font/Lobster_1.4.woff') format('woff');
		font-weight: normal;
		font-style: normal;
	}

	body {
		overflow: hidden;
		height: calc(100% - 4px);
		margin: 0px;
		font-family: "Trebuchet MS", Verdana, sans-serif;
		color: white;
		border: #c8c8c8 solid 2px;
		border-radius: 5px;
		background-color: 	rgba(0, 0, 0, .5);
		-moz-box-shadow:    inset 0 0 20px #000000;
		-webkit-box-shadow: inset 0 0 20px #000000;
		box-shadow:         inset 0 0 20px #000000;	
	}

	#waitingScreen {
		position: fixed;
		top: 0px;
		left: 0px;
		width: 100%;
		height: 100%;
	}

	#waitingScreen > .loading {
		position: fixed;
		width: 400px;
		height: 20px;
		top: 60%;
		left: calc(50% - 200px);
	}

	#waitingScreen > span {
		position: fixed;
		top: 30%;
		width: calc(100% - 4px);
		height: 40%;
		text-align: center;
	}

	#heading {
		position: fixed;
		top: 20px;
		width: 100%;
		text-align: center;
		font-size: 60px;
		line-height: 60px;
		font-family: 'Lobster13Regular', Helvetica, sans-serif;
		font-weight: bold;
		text-align: center;
		text-shadow: rgba(0, 0, 0, .2) 3px 3px 3px;
		-webkit-text-stroke: 1px rgba(0, 0, 0, 0.2);
	}

	#subheading {
		position: fixed;
		top: 100px;
		width: 100%;
		text-align: center;
		font-size: 18px;
		line-height: 18px;
		text-align: center;
		text-shadow: rgba(0, 0, 0, .2) 3px 3px 3px;
		-webkit-text-stroke: 1px rgba(0, 0, 0, 0.2);
	}

	.picture 			{position: inherit; left: 50px; vertical-align: middle;}
	.username 			{position: inherit; left: 130px; margin-top: 15.5px;}
	.remainingCards 	{position: inherit; left: 250px; width: 120px; text-align: center; margin-top: 15.5px;}
	#title 				{position: fixed; top: 130px}
	#first 				{position: fixed; top: 180px}
	#second 			{position: fixed; top: 240px}
	#third 				{position: fixed; top: 300px}
	#forth 				{position: fixed; top: 360px}
	#scoreChange		{position: fixed; top: 180px; left: 500px; font-size: 72}
	#comment			{position: fixed; top: 280px; left: 500px;}
	#back				{
		position: fixed; 
		top: 320px; 
		left: 500px; 
		border: 1px solid #c8c8c8;
		width: 238px;
		text-align: center;
		padding-top: 10px;
		padding-bottom: 10px;
		border-radius: 5px;
		cursor: pointer;
	}
</style>
<body>
	<div id="waitingScreen">
		<span>
			Great game!<br><br>Please wait while we have your score calculated...
		</span>
		<div class="loading"><span></span></div>
	</div>
	<div id="resultScreen" style="display:none">
		<div id="heading">Big Two</div><div id="subheading">Score card</div>
		<div id="title">
			<span class="picture"></span>
			<span class="username">Player</span>
			<span class="remainingCards">Remaining</span>
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

