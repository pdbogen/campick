<script>
	function doLogout() {
		$(document.body).
			append( "<form id='logoutForm' method='POST'><input type='hidden' name='action' value='logout'></form>" );
		$("form#logoutForm").submit();
	}
</script>
<div class='topbar'>
	<a href='<?php print( $_SERVER[ "PHP_SELF" ] ); ?>'>Home</a> |
	<a href='?action=topcams'>Top Cameras</a> |
	<a href='?action=import'>Import</a> |
	<a href='#' onclick='doLogout();return false;'>Logout</a>
</div>
