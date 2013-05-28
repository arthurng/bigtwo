function getResults(roomid){
	$.ajax({
		url: "result-process.php",
		type: "POST",
		data: {action: "getResults", roomid: roomid}
	}).done(function(e){
		for (var i in e){c(e[i]);}
		updateDisplay(e);
	});
}

function updateDisplay(e){
	arr = ["first", "second", "third", "forth"];
	for (j=0; j<4; j++){
		i = arr[j];
		$("#"+i+" .picture").html("<img src='"+e[e[i][0]][0]["picture"]+"' />");
		$("#"+i+" .username").html(e[e[i][0]][0]["username"]);
		$("#"+i+" .remainingCards").html(e["count"+e[i]]);
	}

	$("#back").click(function(){
		c("hi");	
		parent.window.location = "lobby.php";
	});

	// Animate the change of the score counter
	$(function () {
	    var ctr = $("#scoreChange"), clr = null;
		var curr = parseInt(e[e["position"]][0]["score"]);
	    function addition() {
	        ctr.html(curr += 1);
	        if (curr == parseInt(e[e["position"]][0]["score"]) + parseInt(e["score"])) {
	            return;
	        }
	        clr = setTimeout(addition, 1);
	    } addition();
	});

	switch (e["rank"]){
		case 1: $("#comment").html("Awesome! You earned 500 points"); break;
		case 2: $("#comment").html("Great! You earned 250 points"); break;
		case 3: $("#comment").html("Not bad! You earned 100 points"); break;
		case 1: $("#comment").html("Aw snap! Better luck next time"); break;
	}

	// Toggle the display after the AJAX is completed
	$("#waitingScreen").hide(); $("#resultScreen").show();
}

function c(e){
	console.log(e);
}