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
<form action='<?php print htmlentities( $_SERVER[ "REQUEST_URI" ] ); ?>' method='POST'>
<input type='hidden' name='action' value='import'>
<textarea placeholder='Paste a list of camera image URLs here' style='width:100%; height: 90%;' name="import"></textarea>
<input type='submit' value='shipit'>
</form>
