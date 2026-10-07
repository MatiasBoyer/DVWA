<?php

// Only the pages offered by this exercise may be included.
$allowedPages = [ 'include.php', 'file1.php', 'file2.php', 'file3.php' ];
if( !isset( $_GET[ 'page' ] ) ) {
	return;
}
$file = $_GET[ 'page' ];

if( !is_string( $file ) || !in_array( $file, $allowedPages, true ) ) {
	exit( 'ERROR: File not found!' );
}

?>
