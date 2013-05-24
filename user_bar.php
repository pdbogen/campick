<script type='text/javascript'>
	function showLogin( anchor ) {
		var loc = $(anchor).offset();
		loc.left = loc.left + $(anchor).outerWidth() - $('#loginBox').outerWidth();
		$('#loginBox')
			.css( 'position', 'absolute' )
			.css( 'border', '1px solid black' )
			.css( 'padding', '1em 1em 0 1em' )
			.css( 'background-color', '#C0C0C0' )
			.css( 'left', loc.left + "px" )
			.css( 'top', loc.top + "px" )
			.show();
		$('#loginBox input[name="password"]')
			.keypress( function(ev) { if( ev.keyCode == 27 ) { $('#loginBox').hide(); } } )
			.focus();
	}
</script>
<div class='topbar'>
	<a href='<?php print( $_SERVER[ "PHP_SELF" ] ); ?>'>Home</a> |
	<a href='?action=topcams'>Top Cameras</a> |
	<a href='#' onclick='showLogin(this);return false;'>Login</a>
</div>
<div id='loginBox' class='hidden' style='display:none;'><form method='POST'><input type='hidden' name='action' value='login'><input type='password' name='password' placeholder='Admin Password'><input type='submit' value='&gt;'></form></div>
