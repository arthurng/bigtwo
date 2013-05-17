<html>
<style type="text/css">
@font-face{font-family:'Kite One';font-style:normal;font-weight:400;src:local('Kite One'),local('KiteOne-Regular'),url("font/kiteone.woff") format('woff')}
body {font-family: 'Kite One';}
#roomList > div {border: black solid 1px; margin: 2px; width: 50px; text-align: center;}
#profilePicture {clip-path: url(#clipping); -webkit-clip-path: circle(50%, 50%, 20px); vertical-align: middle;}
</style>
<body>
	Welcome to the waiting Room of the game.<br>This is our queue now.<br><br>
	Room List (click to select a room):<br><br><div id="roomList"></div><br>
	<input type="button" id="newRoomButton" value="Create New Room"></input><br><br>
	Friends in this room.
	<div id="queue"></div>
</body>
<!-- Mask for mozilla --><svg><defs><clipPath id="clipping"><circle cx="25px" cy="25px" r="20px" /></clipPath></defs></svg>
<script type="text/javascript" src="incl/jquery.js"></script>
<script type="text/javascript">
	
	// Run before anything else -> SYNC request
	$.ajax({
		url: "lobby-process.php",
		type: "POST",
		async: false,
		data: {action: "joinQueue"} 
	});

	var currentRoom=0;

	window.onbeforeunload = function(){
		quitQueue();
	}

	var periodicReload = setInterval(function(){
		refreshRoomList();
		refreshQueue();
		if ($("#queue").children().length >= 4){
			setTimeout(function(){
				$.ajax({
					url: "room-process.php",
					type: "POST",
					async: false,
					data: {action: "createRoom", roomid: currentRoom} 
				});
				quitQueue();
				$(window).off('beforeunload');				
				window.location.href = "room.php?roomid="+currentRoom;
			}, 2000);
		}
	}, 1000);

	$("#newRoomButton").on("click", function(){createRoom();});

	function quitQueue(){
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			async: false,
			data: {action: "removeFromQueue"} 
		});
		return "You have left the game lobby.";
	};

	function refreshRoomList(){
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			data: {action: "getRoomList"} 
		}).done(function(list){
			$("#roomList").html("");
			for (room in list){
				if (list[room].roomid != null){
					var t=$("<div onclick='joinRoom(this.innerHTML);console.log(\"joined room \"+this.innerHTML)'>"+list[room].roomid+"</div>")
					$("#roomList").append(t);					
				}
			}
		})
	}

	function refreshQueue(){
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			data: {action: "getCurrentQueue", roomid: currentRoom} 
		}).done(function(list){
			var d = document.createElement("div");
			for (player in list){
				var user = getUsername(list[player].userid);
				var t = document.createElement("div");
				var n = document.createElement("span");
					n.innerHTML = user.username;
				var p = document.createElement("img");
					p.id = "profilePicture";
					p.src = user.picture;
				t.appendChild(p);
				t.appendChild(n);
				d.appendChild(t);
			}

			// to prevent reload of the page even
			// when nothing has been changed.
			if (d.innerHTML != $("#queue").html()){
				$("#queue").html(d.innerHTML);
			}
		})
	}

	function getUsername(){
		var r = $.ajax({
			url: "lobby-process.php",
			type: "POST",
			async: false,
			data: {action: "getUsername"}
		}).responseText;
		r = $.parseJSON(r)[0];
		return r;
	}

	function createRoom(){
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			data: {action: "createRoom"} 
		}).done(function(createdRoom){
			joinRoom(createdRoom);
		});
	}

	function joinRoom(roomNumber){
		currentRoom = roomNumber;
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			data: {action: "joinRoom", roomid: roomNumber} 
		});		
	}		
</script>
</html>