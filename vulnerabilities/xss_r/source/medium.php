<?php

// Is there any input?
if( isset( $_GET[ 'name' ] ) && is_string( $_GET[ 'name' ] ) && $_GET[ 'name' ] !== '' ) {
	// Get input
	$name = htmlspecialchars( $_GET[ 'name' ], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

	// Feedback for end user
	$html .= "<pre>Hello {$name}</pre>";
}

?>
