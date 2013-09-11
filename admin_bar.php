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
<script>
	function doLogout() {
		$(document.body).
			append( "<form id='logoutForm' method='POST'><input type='hidden' name='action' value='logout'></form>" );
		$("form#logoutForm").submit();
	}
</script>
<div class='topbar'>
	<a href='<?php print( $_SERVER[ "PHP_SELF" ] ); ?>'>Home</a> |
	<a href='?action=reports'>Broken Cameras</a> |
	<a href='?action=topcams'>Top Cameras</a> |
	<a href='?action=import'>Import</a> |
	<a href='#' onclick='doLogout();return false;'>Logout</a>
</div>
