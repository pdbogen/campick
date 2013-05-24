<?php
	global $db;
	$limit = 8;
	$offset = 0;
	if( array_key_exists( "limit", $_GET ) ) {
		$limit = $_GET[ "limit" ];
	}
	if( array_key_exists( "offset", $_GET ) ) {
		$offset = $_GET[ "offset" ];
	}
	if( !($statement = $db->prepare( "SELECT camera_url, camera_votes FROM cameras ORDER BY camera_votes DESC LIMIT ? OFFSET ?")) ) {
		throw new Exception( "failed to prepare statement to pick top cameras: ".$db->error );
	}
	if( !($statement->bind_param( "ii", $limit, $offset )) ) {
		throw new Exception( "failed to bind params to statement to pick top cameras: ".$db->error );
	}
	if( !($statement->execute()) ) {
		throw new Exception( "failed to execute statement to pick top cameras: ".$db->error );
	}
	if( !($statement->bind_result( $ret_url, $ret_votes )) ) {
		throw new Exception( "failed to bind result parameter to statement to pick top cameras: ".$db->error );
	}
	$a_ret = Array();
	while( ($ret = $statement->fetch()) !== NULL ) {
		if( $ret === FALSE ) {
			throw new Exception( "failed to execute fetch on statement to pick a second camera: ".$db->error );
		}
		array_push( $a_ret, Array( $ret_url, $ret_votes ) );
	}
	$statement->free_result();
	$i = 0;
	foreach( $a_ret as $cam ) {
		$i++;
		if( $i > 4 ) {
			print( "<div style='clear: both;'>&nbsp;</div>" );
			$i = 0;
		}
		print( "<div style='width:25%;float: left;'>" );
		print( "<a href='".htmlentities( $cam[0] )."'>" );
		print( "<img src='".htmlentities( $cam[0] )."' style='width:90%;'><br/>" );
		print( "</a>" );
		print( htmlentities( $cam[1] )." votes" );
		print( "</div>" );
	}
	print( "<div style='clear: both;'>&nbsp;</div>" );
	if( $offset > 0 ) {
		print( "<div style='width: 49%; float: left; text-align: right;'><a href='?action=topcams&offset=".htmlentities( urlencode( $offset - 8 ) )."'>&lt; &lt; &lt; Back</a>&nbsp;</div>" );
	}
	print( "<div style='width: 51%; float: right;'> | <a href='?action=topcams&offset=".htmlentities( urlencode( $offset + 8 ) )."'>Next &gt; &gt; &gt;</a></div>" );
?>
