<html>
<link rel="stylesheet" type="text/css" href="lobby.css">
<!--<audio autoplay loop><source src="ui/background.mp3"></audio>-->
<body>
	<div id="heading">BIG TWO - GAME LOBBY</div>
	<div id="roomList"></div>
	<div id="queue"></div>
	<div id="newRoomButton">
		<span id="label">Create new room</span>
	</div>
	<div id="fb-root"></div>
</body>
<!-- Mask for mozilla --><svg><defs><clipPath id="clipping"><circle cx="25px" cy="25px" r="20px" /></clipPath></defs></svg>
<script type="text/javascript" src="incl/jquery.js"></script>
<script type="text/javascript" src="incl/og.js"></script>
<script type="text/javascript" src="incl/loading.js"></script>
<script type="text/javascript">
	// "currentroom"(string) is a global variable indicating where the user is
	var currentRoom="0";
	disableScroll();

	// Run before anything else -> SYNC request
	$.ajax({
		url: "lobby-process.php",
		type: "POST",
		async: false,
		data: {action: "joinQueue"} 
	});

	window.onbeforeunload = function(){
		quitQueue();
	}

// General reload function

	var periodicReload = setInterval(function(){
		refreshRoomList();
		refreshQueue();
		if ($("#queue").children(".userBox").length == 4){
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

// Function called when a user leaves the page

	function quitQueue(){
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			async: false,
			data: {action: "removeFromQueue"} 
		});
		return "You have left the game lobby.";
	};

// Function for Room list management (called periodically)

	function refreshRoomList(){
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			data: {action: "getRoomList"} 
		}).done(function(list){
			$("#roomList").html("");
			for (room in list){
				if (list[room].roomid != null){
					var t = $("<div onclick='viewRoom(this.innerHTML);'>"+list[room].roomid+"</div>")
					$("#roomList").append(t);					
				}
			}
		})
	}

// Function to reload the users in a room (called periodically)

	al.show();
	function refreshQueue(){
		$.ajax({
			url: "lobby-process.php",
			type: "POST",
			data: {action: "getCurrentQueue", roomid: currentRoom} 
		}).done(function(list){
			var $room = $(document.createElement("div"));
			// --------------------------------------------------- Create the "Join room"/"Waiting" button
			if (list["userlist"].length != 0){
				var $button = $(document.createElement("span"))
						.attr("id", "joinRoomButton");
					$label = $(document.createElement("span"))
						.attr("id", "label");
					if (list["inside"]) $label.text("Waiting for more user...");
					else $label.text("Join this room!");
				$button.append($label);
				$room.append($button);
			}
			// --------------------------------------------------- Append the user list of a room
			for (player in list["userlist"]){
				var user = getUsername(list["userlist"][player].userid);
				var $box = $(document.createElement("div"))
						.attr("class", "userBox");
				var $name = $(document.createElement("span"))
						.text(user.username);
				var $pic = $(document.createElement("img"))
						.attr("class", "profilePicture")
						.attr("src", ""+user.picture+"?width=180&height=180");
				$box.append($pic);
				$box.append($name);
				$room.append($box);
			}
			// to prevent reload of the page even when nothing is changed.
			if ($room.html() != $("#queue").html()){		
				$("#queue").html($room.html());
				$("#joinRoomButton").click(function(){
					joinRoom();
				});
			}
			al.hide();
		})
	}

// Function to create and handle the "New Room Button"

	$("#newRoomButton").click(function(){createRoom();});

	function createRoom(){
		var timeoutForReset = "undef";
		$("#newRoomButton").off("click");
		$inputNameBox = $(document.createElement('input'))
			.attr("type", "text")
			.attr("id", "inputNameBox")
			.attr("placeholder", "Input name here.")
			.focusout(function(){
				// Delay to check is "submit" is pressed
				timeoutForReset = setTimeout(revertNewRoomButton, 1000);
			})
			.keyup(function(event){
				if(event.keyCode == 13){
					timeoutForReset = setTimeout(revertNewRoomButton, 1000);
					$("#submitNameBox").click();
				}
			});
		$submitNameBox = $(document.createElement('span'))
			.attr("id", "submitNameBox")
			.html("<img src='ui/check.png' />")
			.click(function(){
				clearTimeout(timeoutForReset); // Prevent the focusout reset
				al.show();
				$.ajax({
					url: "lobby-process.php",
					type: "POST",
					data: {action: "createRoom", name: $("#inputNameBox").val()} 
				}).done(function(result){
					if (result == "name_ok") {
						currentRoom = $("#inputNameBox").val().toUpperCase();
						joinRoom(); // Auto join the room after creation
						revertNewRoomButton();
						// Facebook OG function to publish news of "Created a Room"
						FB.api(
							'me/cscibigtwo:create',
							'post',
							{
								room: "https://secure.shop151.ierg4210.org/bigtwo/ogobjects/createroom.php?name="+currentRoom
							},
							function(response) {
								if (!response) {
									console.error('Open Graph: Error occurred, response not received.');
								} else if (response.error) {
									console.error('Open Graph: Error occurred, ' + response.error.message);
								} else {
									console.log('Open Graph: Story created successfully. View here: %o', 'https://www.facebook.com/me/activity/'+response.id);
								}
							}
						);
						// End of the Facebook plugin							
					} else if (result == "name_taken"){
						alert("Sorry, the name has already beem take. Please pick another name.");
					} else {
						alert("Sweetheart, please input a name for the room.");
					}
					al.hide();
				});				
			});
		$("#newRoomButton").html("").append($inputNameBox).append($submitNameBox);
		$("#inputNameBox").focus();
	}

	function revertNewRoomButton(){
		$("#newRoomButton").click(function(){createRoom();}).html("<span id='label'>Create new room</span>");
	}

// Function to "enter" a room

	function viewRoom(roomNumber){
		currentRoom = roomNumber;
		refreshQueue();
	}

	function joinRoom(){
		$.ajax({
			url: "lobby-process.php?label",
			type: "POST",
			data: {action: "joinRoom", roomid: currentRoom} 
		}).done(function(){
			$("#joinRoomButton").children("#label").text("Waiting for more user...");
			refreshQueue();
		});
	}

	// ----- MISC FUNCTION ----- //

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