// This is boilerplate code that is used to initialize the Facebook
// JS SDK.  You would normally set your App ID in this code.

// Additional JS functions here
window.fbAsyncInit = function() {
  FB.init({
    appId      : 123059651225050,   // App ID
    status     : true,              // check login status
    cookie     : true,              // enable cookies to allow the server to access the session
    xfbml      : true               // parse page for xfbml or html5 social plugins like login button below
  });

  // Put additional init code here
};

// Load the SDK Asynchronously
(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/all.js";
    // js.src = "//connect.facebook.net/en_US/all/debug.js";
    fjs.parentNode.insertBefore(js, fjs);
 }(document, 'script', 'facebook-jssdk'));