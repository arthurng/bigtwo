<html>
<body>
<div class="fb-login-button" data-show-faces="true" data-width="200" data-max-rows="1"></div>
<div> <input id="logout" type="button" value="logout"> </input></div>
<div id="fb-root"></div>
</body>
</html>

<script>
// Additional JS functions here
window.fbAsyncInit = function() {
	FB.init({
		appId      : '123059651225050', // App ID
		channelUrl : '//WWW.YOUR_DOMAIN.COM/channel.html', // Channel File
		status     : true, // check login status
		cookie     : true, // enable cookies to allow the server to access the session
		xfbml      : true  // parse XFBML
	});

// Additional init code here
	FB.getLoginStatus(function(response) {
	if (response.status === 'connected') {
		// connected
		console.log('Welcome!  Fetching your information.... ');	
		FB.api('/me', function(response) {
		console.log("Name: "+ response.name + " ID: "+response.id);
		var img_link = "http://graph.facebook.com/"+response.id+"/picture"
		console.log(img_link);
		});		
	} else if (response.status === 'not_authorized') {
		// not_authorized
		console.log('User cancelled login or did not fully authorize.');
		login();
	} else {
		// not_logged_in
		console.log('User cancelled login or did not fully authorize.');
		login();
	}
	});
	
	function login() {
		FB.login(function(response) {
		if (response.authResponse) {
			console.log('Welcome!  Fetching your information.... ');
			FB.api('/me', function(response) {
			console.log("Name: "+ response.name + " ID: "+response.id);
			var img_link = "http://graph.facebook.com/"+response.id+"/picture"
			console.log(img_link);
			window.location.reload();
			});
		} else {
			console.log('User cancelled login or did not fully authorize.');
		}
		});
	}
};
	document.getElementById('logout').onclick = logout;
	
	function logout() {
		if (FB.getAuthResponse()) {
			FB.logout(function(response) {
				window.location.reload();
				// user is now logged out
			});
		}
	}

// Load the SDK Asynchronously
(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
	}(document));
</script>
