function endGame(roomid){
	window.roomid = roomid;
	disableScroll();
	createBackground();
	var $body = $("body");
	$iframe = $(document.createElement('iframe'));
	$iframe.attr('src', 'result.php');
	$iframe.attr('id', 'result');
	leftSpacing = ($body.innerWidth()-800)/2+"px";
	$iframe.css({
		'height': '70%', 
		'width': '800px', 
		'z-index': '1000', 
		'position': 'fixed', 
		'top': '15%', 
		'left': leftSpacing,
		'background-color': 'white',
		'border-radius': '10px',
		'box-shadow': '3px 3px 5px 0px #000',
	});
	$body.append($iframe);
}

function createBackground(){
	var $body = $("body");
	var $background = $(document.createElement('div'));
	$background.css({
		'height': '100%', 
		'width': '100%', 
		'z-index': '999', 
		'position': 'fixed', 
		'top': '0', 
		'left': '0', 		
		'background-color': 'black',
		'opacity': '0.6',
	});
	$body.append($background);
}

function passUserid(){
	return roomid;
}

function disableScroll(){
	window.onmousewheel = document.onmousewheel = function(e) {
		e = e || window.event;
		if (e.preventDefault)
			e.preventDefault();
		e.returnValue = false;
	};
}