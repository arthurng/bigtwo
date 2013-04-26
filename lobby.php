<?php
	$db = new PDO('mysql:host=www.shop151.ierg4210.org;dbname=bigtwo', "bigtwoadmin", "csci4140");
	$q = $db -> prepare("SELECT * FROM queue WHERE userid = ?");
	$q->execute(array($_REQUEST["userid"]));
	$r = $q->fetch();
	if (!$r) {
		$q = $db -> prepare("INSERT INTO queue (userid) VALUES (?)");
		$q->execute(array($_REQUEST["userid"]));
	}
?>
<html>
<body>
	Welcome to the waiting Room of the game.<br>This is our queue now.<br><br>
	<div id="queue"></div>
</body>
<script type="text/javascript" src="incl/jquery.js"></script>
<script type="text/javascript">
	var periodicReload = setInterval(function(){
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			data: {action: "getCurrentQueue"} 
		}).done(function(list){
			$("#queue").html("");
			for (player in list){
				var t=$("<div>"+list[player].userid+"</div>")
				$("#queue").append(t);
			}
		})
		if ($("#queue").children().length >= 4){
			setTimeout(function(){
				$.ajax({
					url: "lobby-process.php",
					type: "POST",
					data: {action: "removeFromQueue", userid: "<?php echo $_REQUEST['userid']; ?>"} 
				}).done(function(){
					clearInterval(periodicReload);
					console.log("enough people!");
				});
			}, 2000)
		}
	}, 1000);

	$(window).on('beforeunload', function(){
		var x = quitQueue();
		return x;
	});

	function quitQueue(){
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			data: {action: "removeFromQueue", userid: "<?php echo $_REQUEST['userid']; ?>"} 
		});
		return "You have left the queue.";
	};
</script>
</html>