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
	$img1 = get_random_camera();
	if( $img1 === NULL ) {
		print( "Alas, there are no cameras.\n" );
		exit;
	}
	$img2 = get_random_camera($img1[0]);
	if( $img2 === NULL ) {
		print( "Alas, there is but one camera. It is the best.\n" );
		exit;
	}
?>

<script>
	function vote(img) {
		$(document.body).append( "<form id='voteform' method='POST'><input type='hidden' name='action' value='vote'><input type='hidden' name='url' id='urlinput'></form>" );
		$("#urlinput").val( $(img).attr( "src" ) );
		$("#voteform").submit();
	}
	function bothsuck() {
		$(document.body).append( "<form id='voteform' method='POST'><input type='hidden' name='action' value='bothsuck'><input type='hidden' name='url1' id='url1input'><input type='hidden' name='url2' id='url2input'></form>" );
		$("#url1input").val( $("#leftimg").attr( "src" ) );
		$("#url2input").val( $("#rightimg").attr( "src" ) );
		$("#voteform").submit();
	}
	function star(which) {
		$(document.body).append( "<form id='starform' method='POST'><input type='hidden' name='action' value='star'><input type='hidden' name='url' id='urlinput'></form>" );
		$("#urlinput").val( $("#"+which).attr( "src" ) );
		$("#starform").submit();
	}
	function nuke(which) {
<?php if( $admin ) { ?>
		$(document.body).append( "<form id='nukeform' method='POST'><input type='hidden' name='action' value='nuke'><input type='hidden' name='url' id='urlinput'></form>" );
<?php } else { ?>
		$(document.body).append( "<form id='nukeform' method='POST'><input type='hidden' name='action' value='report'><input type='hidden' name='url' id='urlinput'></form>" );
<?php } ?>
		$("#urlinput").val( $("#"+which).attr( "src" ) );
		$("#nukeform").submit();
	}
	$(document).ready(function(){
		$(document).keypress(function(ev){
			if( ev.keyCode == 37 ) {
				$("#leftimg").click();
			} else if( ev.keyCode == 39 ) {
				$("#rightimg").click();
			} else if( ev.charCode == 114 ) {
				location.reload();
			} else if( ev.keyCode == 40 ) {
				bothsuck();
			}
		});
	});
</script>

<div style='margin-left: auto; margin-right: auto; text-align: center;'>
	Click on the webcam (or press left or right arrow) that's more interesting/more evil/more naked/more whatever.<br/>
	Press down if they both suck.<br/>
	<div style='width:45%; float: left;'><a href='#' onclick='nuke("leftimg"); return false;'><?php if( $admin ) { print( "Nuke This" ); } else { print( "Broken Image?" ); } ?></a></div>
	<div style='width:45%; float: right;'><a href='#' onclick='nuke("rightimg"); return false;'><?php if( $admin ) { print( "Nuke This" ); } else { print( "Broken Image?" ); } ?></a></div>
	<img id='leftimg' src='<?php print htmlentities( $img1[0] ); ?>' width='45%' onclick='vote(this);'>
	<img id='rightimg' src='<?php print htmlentities( $img2[0] ); ?>' width='45%' onclick='vote(this);'> <br/>
	<div style='width:45%; float: left;'>
<?php
	if( $admin ) {
		print( "<a href='#' onclick='star(\"leftimg\")'><img src='bookmark-3".($img1[2]?"":"-mono").".png'></a>" );
	} else {
		print( "<img id='leftheart' src='bookmark-3".($img1[2]?"":"-mono").".png'>" );
	}
?>
		<?php print htmlentities( $img1[1] ); ?> vote(s)
	</div>
	<div style='width:45%; float: right;'>
<?php
	if( $admin ) {
		print( "<a href='#' onclick='star(\"rightimg\")'><img src='bookmark-3".($img2[2]?"":"-mono").".png'></a>" );
	} else {
		print( "<img id='leftheart' src='bookmark-3".($img2[2]?"":"-mono").".png'>" );
	}
?>
		<?php print htmlentities( $img2[1] ); ?> vote(s)
	</div>
	<div style='text-align: center; clear: both;'><a href='#' onclick='bothsuck();return false;'>Both Suck</a></div>
</div>
