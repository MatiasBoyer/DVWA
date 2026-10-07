<?php

// Only the pages offered by this exercise may be included.
$allowedPages = [ 'include.php', 'file1.php', 'file2.php', 'file3.php' ];
$file = $_GET[ 'page' ] ?? null;

if( !is_string( $file ) || !in_array( $file, $allowedPages, true ) ) {
	http_response_code( 400 );
	exit( 'ERROR: File not found!' );
}

?>
