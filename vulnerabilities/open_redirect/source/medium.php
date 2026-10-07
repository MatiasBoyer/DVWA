<?php

$allowedTargets = [ 'info.php?id=1', 'info.php?id=2' ];
$target = $_GET[ 'redirect' ] ?? null;

if( $target === null || $target === '' ) {
	http_response_code( 500 );
	exit( '<p>Missing redirect target.</p>' );
}

if( is_string( $target ) && preg_match( '/https?:\/\//i', $target ) ) {
	http_response_code( 500 );
	exit( '<p>Absolute URLs not allowed.</p>' );
}

if( !is_string( $target ) || !in_array( $target, $allowedTargets, true ) ) {
	http_response_code( 400 );
	exit( 'Unknown redirect target.' );
}

header( 'Location: ' . $target, true, 302 );
exit;
?>
