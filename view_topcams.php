<?php
	global $admin;
	global $db;
	$limit = 8;
	$offset = 0;
	if( array_key_exists( "limit", $_GET ) ) {
		$limit = $_GET[ "limit" ];
	}
	if( array_key_exists( "offset", $_GET ) ) {
		$offset = $_GET[ "offset" ];
	}
	if( array_key_exists( "bottom", $_GET ) ) {
		if( !($statement = $db->prepare( "SELECT camera_url, camera_votes FROM cameras ORDER BY camera_votes ASC LIMIT ? OFFSET ?")) ) {
			throw new Exception( "failed to prepare statement to pick top cameras: ".$db->error );
		}
	} else {
		if( !($statement = $db->prepare( "SELECT camera_url, camera_votes FROM cameras ORDER BY camera_votes DESC LIMIT ? OFFSET ?")) ) {
			throw new Exception( "failed to prepare statement to pick top cameras: ".$db->error );
		}
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
?>
<script>
	$(document).ready(function(){
		$(document).keypress(function(ev){
			if( ev.keyCode == 37 ) {
				if( $("#backlink" ).length > 0 ) {
					document.location = $("#backlink").attr( "href" );
				}
			} else if( ev.keyCode == 39 ) {
				document.location = $("#nextlink").attr( "href" );
			}
		});
	});
	function nuke( imgnum ) {
		$(document.body).append( "<form id='nukeform' method='POST'><?php
if( $admin ) {
		print( "<input type='hidden' name='action' value='nuke'>" );
} else {
		print( "<input type='hidden' name='action' value='report'>" );
}
print( "<input type='hidden' name='url' id='urlinput'>" );
if( array_key_exists( "bottom", $_GET ) ) {
		print( "<input type='hidden' name='back' value='bottomcams'>" );
} else {
		print( "<input type='hidden' name='back' value='topcams'>" );
} ?></form>" );
		$("#urlinput").val( $("#"+imgnum).attr( "src" ) );
		$("#nukeform").submit();
	}
</script>
<div style='text-align: center;'>Left and Right arrows can be used to browse this list.</div>
<?php if( array_key_exists( "bottom", $_GET ) ) { ?>
<div style='text-align: center;'>View <a href='?action=topcams'>top cameras</a> instead.</div>
<?php } else { ?>
<div style='text-align: center;'>View <a href='?action=topcams&bottom'>bottom cameras</a> instead.</div>
<?php
	}
	$i = 0; $j = 0;
	foreach( $a_ret as $cam ) {
		$i++; $j++;
		if( $i > 4 ) {
			print( "<div style='clear: both;'>&nbsp;</div>" );
			$i = 0;
		}
		print( "<div style='width:25%;float: left;'>" );
		print( "<a href='".htmlentities( $cam[0] )."'>" );
		print( "<img id='img".$j."' src='".htmlentities( $cam[0] )."' style='width:90%;'><br/>" );
		print( "</a>" );
		print( htmlentities( $cam[1] )." votes" );
		if( $admin ) {?>
			<a href='#' onclick='nuke( "img<?php print $j; ?>" );'>nuke this<a>
<?php	} else { ?>
			<a href='#' onclick='nuke( "img<?php print $j; ?>" );'>report broken<a>
<?php	}
		print( "</div>" );
	}
	print( "<div style='clear: both;'>&nbsp;</div>" );
	$_SESSION[ "offset" ] = $offset;
	if( $offset > 0 ) {
		print( "<div style='width: 49%; float: left; text-align: right;'><a href='?action=topcams" );
		if( array_key_exists( "bottom", $_GET ) ) {
			print( "&bottom" );
		}
		print( "&offset=".htmlentities( urlencode( $offset - 8 ) )."' id='backlink'>&lt; &lt; &lt; Back</a>&nbsp;</div>" );
	}
	print( "<div style='width: 51%; float: right;'> | <a href='?action=topcams" );
	if( array_key_exists( "bottom", $_GET ) ) {
		print( "&bottom" );
	}
	print( "&offset=".htmlentities( urlencode( $offset + 8 ) )."' id='nextlink'>Next &gt; &gt; &gt;</a></div>" );
?>
