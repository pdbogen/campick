<?php
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
</script>

<div style='margin-left: auto; margin-right: auto; text-align: center;'>
	Click on the webcam that's better.<br/>
	<img src='<?php print htmlentities( $img1[0] ); ?>' width='45%' onclick='vote(this);'>
	<img src='<?php print htmlentities( $img2[0] ); ?>' width='45%' onclick='vote(this);'> <br/>
	<div style='width:45%; float: left;'><?php print htmlentities( $img1[1] ); ?> vote(s)</div>
	<div style='width:45%; float: right;'><?php print htmlentities( $img2[1] ); ?> vote(s)</div>
</div>
