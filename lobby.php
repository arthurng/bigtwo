<html>
<link rel="stylesheet" type="text/css" href="lobby.css">
<body>
	<div id="heading">BIG TWO - GAME LOBBY</div>
	<div id="roomList"></div>
	<div id="queue"></div>
	<div id="newRoomButton"><span id="label">Create new room</span></div>
</body>
<!-- Mask for mozilla --><svg><defs><clipPath id="clipping"><circle cx="25px" cy="25px" r="20px" /></clipPath></defs></svg>
<script type="text/javascript" src="incl/jquery.js"></script>
<script type="text/javascript">
	// disableScroll();

	// Run before anything else -> SYNC request
	$.ajax({
		url: "lobby-process.php",
		type: "POST",
		async: false,
		data: {action: "joinQueue"} 
	});

	var currentRoom="0";

	window.onbeforeunload = function(){
		quitQueue();
	}

	var periodicReload = setInterval(function(){
		refreshRoomList();
		refreshQueue();
		if ($("#queue").children(".userBox").length >= 4){
			var hashedName = hashCode(currentRoom);
			setTimeout(function(){
				$.ajax({
					url: "room-process.php",
					type: "POST",
					async: false,
					data: {action: "createRoom", roomid: hashedName}
				});
				quitQueue();
				$(window).off('beforeunload');
				window.location.href = "room.php?roomid="+hashedName;
			}, 2000);
		}
	}, 2000);

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
					var t=$("<div onclick='viewRoom(this.innerHTML);console.log(\"viewed room \"+this.innerHTML)'>"+list[room].roomid+"</div>")
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
			if (list["userlist"].length != 0){
				var join = document.createElement("span");
					join.id = "joinRoomButton";
					if (list["inside"])join.innerHTML = "Waiting for more user...";
					else join.innerHTML = "Join this room!";
				d.appendChild(join);
			}
			for (player in list["userlist"]){
				var user = getUsername(list["userlist"][player].userid);
				var t = document.createElement("div");
					t.className = "userBox";
				var n = document.createElement("span");
					n.innerHTML = user.username;
				var p = document.createElement("img");
					p.className = "profilePicture";
					p.src = user.picture+"?width=180&height=180";
				t.appendChild(p);
				t.appendChild(n);
				d.appendChild(t);
			}

			// to prevent reload of the page even
			// when nothing has been changed.
			if (d.innerHTML != $("#queue").html()){
				$("#queue").html(d.innerHTML);
				$("#joinRoomButton").on("click", function(){
					console.log("joined room");
					joinRoom();
				});
			}
		})
	}

	function getUsername(userid){
		var r = $.ajax({
			url: "lobby-process.php",
			type: "POST",
			async: false,
			data: {action: "getUsername", userid: userid}
		}).responseText;
		r = $.parseJSON(r)[0];
		return r;
	}

		$("#newRoomButton").on("click", function(){createRoom();});

	function createRoom(){
		$("#newRoomButton").off("click");
		$inputNameBox = $(document.createElement('input'))
			.attr("id", "inputNameBox")
			.attr("placeholder", "Input name here.")
			.attr("type", "text")
			.keyup(function(event){
				if(event.keyCode == 13){
					$("#submitNameBox").click();
			}});
		$submitNameBox = $(document.createElement('span'))
			.attr("id", "submitNameBox")
			.on("click", function(){
				console.log($("#inputNameBox").val());
				$.ajax({
					url: "lobby-process.php",
					type: "POST",
					data: {action: "createRoom", name: $("#inputNameBox").val()} 
				}).done(function(result){
					if (result == "name_ok") {
						currentRoom = $("#inputNameBox").val();
						joinRoom();
						// return the button to the original state
						$("#newRoomButton")
							.on("click", function(){createRoom();})
							.html("<span id='label'>Create new room</span>");
					} else if (result == "name_taken"){
						alert("Sorry, the name has already beem take. Please pick another name.")
					} else {
						alert("Darling, please input a name for the room.")
					}
				});				
			})
			.html("<img src='ui/check.png' />");
		$("#newRoomButton").html("").append($inputNameBox).append($submitNameBox);
		$("#inputNameBox").focus();
	}

	function viewRoom(roomNumber){
		currentRoom = roomNumber;
	}

	function joinRoom(){
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			data: {action: "joinRoom", roomid: currentRoom} 
		}).done(function(){
			$("#joinRoomButton").html("Waiting for more user...");
		});
	}

	// ----- MISC FUNCTION ----- //

	function hashCode(str){
	    var hash = 0;
	    if (str.length == 0) return hash;
	    for (i = 0; i < str.length; i++) {
	        char = str.charCodeAt(i);
	        hash = ((hash<<5)-hash)+char;
	        hash = hash & hash; // Convert to 32bit integer
	    }
	    return hash;
	}

	function disableScroll(){
	window.onmousewheel = document.onmousewheel = function(e) {
		e = e || window.event;
		if (e.preventDefault)
			e.preventDefault();
		e.returnValue = false;
	};
}
</script>
</html>