<?php

$allowedLanguages = [ 'English', 'French', 'Spanish', 'German' ];
$default = $_GET[ 'default' ] ?? 'English';

if( !is_string( $default ) || !in_array( $default, $allowedLanguages, true ) ) {
	$default = 'English';
}

?>
