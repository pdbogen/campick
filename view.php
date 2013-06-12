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
<?php
	global $admin;
?>
<html>
<head>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<style>
		body {
			margin: 0;
			padding: 0;
		}
		div.topbar {
			border-bottom: 1px solid black;
			background-color: #C0C0C0;
			text-align: right;
			padding: .1em .1em .1em .1em;
		}
	</style>
</head>
<body>
	<?php include( ($admin?"admin_bar.php":"user_bar.php") ); ?>
	<div id='content'>
	<?php include( "view_".$view.".php" ); ?>
	</div>
</body>
</html>
