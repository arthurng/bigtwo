function AjaxLoading(){}
var al = new AjaxLoading();

AjaxLoading.prototype.show = function(){
		var $dialog, $label, $image;
		$dialog = $(document.createElement("div"))
			.attr('id', 'loading')
			.css({
				'position': 'fixed',
				'z-index': '2000',
				'width': '300px',
				'height': '100px',
				'top': window.innerHeight/2-50+'px',
				'left': window.innerWidth/2-100+'px',
				'background-color': 'rgba(0,0,0,0.7)',
				'border-radius': '10px'
			});
		$label = $(document.createElement("span"))
			.text("Loading")
			.css({
				'position': 'absolute',
				'width': '100px',
				'top': '20px',
				'left': '100px',
				'text-align': 'center',
			});
		$image = $(document.createElement("img"))
			.attr("src", "http://images.wikia.com/tibia/en/images/6/6c/Minimap_Loading.gif")
			.css({
				'position': 'absolute',
				'width': '500px',
				'left': '-100px',
				'top': '-120px',
			});
		$dialog.append($label).append($image);
		$('body').append($dialog);
		return true;
};

AjaxLoading.prototype.hide = function(){
	$('#loading').remove();
	return true;	
};