<form action='<?php print $_SERVER[ "REQUEST_URI" ]; ?>' method='POST'>
<input type='hidden' name='action' value='import'>
<textarea placeholder='Paste a list of camera image URLs here' style='width:100%; height: 90%;' name="import"></textarea>
<input type='submit' value='shipit'>
</form>
