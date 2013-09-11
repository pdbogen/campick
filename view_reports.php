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
	function nuke( anchor ) {
		$(document.body).append( "<form id='nukeform' method='POST'><input type='hidden' name='action' value='nuke'><input type='hidden' name='url' id='urlinput'><input type='hidden' name='back' value='reports'></form>" );
		$("#urlinput").val( $("#img"+(anchor.name)).attr( "src" ) );
		$("#nukeform").submit();
	}
	function clear_reports( anchor ) {
		$(document.body).append( "<form id='clear_reportsform' method='POST'><input type='hidden' name='action' value='clear_reports'><input type='hidden' name='url' id='urlinput'><input type='hidden' name='back' value='reports'></form>" );
		$("#urlinput").val( $("#img"+(anchor.name)).attr( "src" ) );
		$("#clear_reportsform").submit();
	}
</script>
<div style='text-align: center;'>Left and Right arrows can be used to browse this list.</div>
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
	if( !($statement = $db->prepare( "SELECT url, reports FROM reports ORDER BY reports DESC LIMIT ? OFFSET ?")) ) {
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
	$i = 0; $j = 0;
	foreach( $a_ret as $cam ) {
		$i++; $j++;
		if( $i > 3 ) {
			print( "<div style='clear: both;'>&nbsp;</div>" );
			$i = 0;
		}
		print( "<div style='width:33%;float: left; text-align: center;'>" );
		print( "<a href='".htmlentities( $cam[0] )."'>" );
		print( "<img id='img".$j."' src='".htmlentities( $cam[0] )."' style='width:90%;'>" );
		print( "</a><br/>" );
		print( "<a href='#' name='".$j."' onclick='clear_reports( this ); return false;'>'sok</a> " );
		print( htmlentities( $cam[1] )." reports " );
		print( "<a href='#' name='".$j."' onclick='nuke( this ); return false;'>nuke it!</a>" );
		print( "</div>\n" );
	}
	print( "<div style='clear: both;'>&nbsp;</div>" );
	$_SESSION[ "offset" ] = $offset;
	if( $offset > 0 ) {
		print( "<div style='width: 49%; float: left; text-align: right;'><a href='?action=topcams&offset=".htmlentities( urlencode( $offset - 8 ) )."' id='backlink'>&lt; &lt; &lt; Back</a>&nbsp;</div>" );
	}
	print( "<div style='width: 51%; float: right;'> | <a href='?action=topcams&offset=".htmlentities( urlencode( $offset + 8 ) )."' id='nextlink'>Next &gt; &gt; &gt;</a></div>" );
?>
