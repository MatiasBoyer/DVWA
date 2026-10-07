<?php

if( isset( $_POST[ 'btnSign' ] ) ) {
	$message = $_POST[ 'mtxMessage' ] ?? null;
	$name = $_POST[ 'txtName' ] ?? null;

	if( is_string( $message ) && is_string( $name ) ) {
		$message = trim( $message );
		$name = trim( $name );
		$data = $db->prepare( 'INSERT INTO guestbook ( comment, name ) VALUES ( :message, :name );' );
		$data->bindValue( ':message', $message, PDO::PARAM_STR );
		$data->bindValue( ':name', $name, PDO::PARAM_STR );
		$data->execute();
	}
}

?>
