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
