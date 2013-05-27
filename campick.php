<?php
	include_once( "functions.php" );
	$db = load_or_create_db( );

	init_session();
	check_admin();

	route_request();

	check_if_configured();
	do_index();

?>
