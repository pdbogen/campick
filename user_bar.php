<?php /*
Copyright (c) 2013 Patrick Bogen

This file is part of campick.

campick is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

campick is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with campick.  If not, see <http://www.gnu.org/licenses/>.
*/ ?>
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
	<a href='<?php print( htmlentities( $_SERVER[ "PHP_SELF" ] ) ); ?>'>Home</a> |
	<a href='?action=topcams'>Top Cameras</a> |
	<a href='#' onclick='showLogin(this);return false;'>Login</a>
</div>
<div id='loginBox' class='hidden' style='display:none;'><form method='POST'><input type='hidden' name='action' value='login'><input type='password' name='password' placeholder='Admin Password'><input type='submit' value='&gt;'></form></div>
