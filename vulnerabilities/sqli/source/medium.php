<?php

if( isset( $_POST[ 'Submit' ] ) ) {
	$rawId = $_POST[ 'id' ] ?? null;
	$id = is_string( $rawId )
		? filter_var( $rawId, FILTER_VALIDATE_INT, [ 'options' => [ 'min_range' => 1 ] ] )
		: false;
	$row = false;

	if( $id !== false ) {
		switch( $_DVWA[ 'SQLI_DB' ] ) {
			case MYSQL:
				$data = $db->prepare( 'SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1;' );
				$data->bindValue( ':id', $id, PDO::PARAM_INT );
				$data->execute();
				$row = $data->fetch( PDO::FETCH_ASSOC );
				break;
			case SQLITE:
				global $sqlite_db_connection;
				$data = $sqlite_db_connection->prepare( 'SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1;' );
				$data->bindValue( ':id', $id, SQLITE3_INTEGER );
				$result = $data->execute();
				if( $result !== false ) {
					$row = $result->fetchArray( SQLITE3_ASSOC );
					$result->finalize();
				}
				break;
		}
	}

	if( $row ) {
		$first = htmlspecialchars( $row[ 'first_name' ], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
		$last = htmlspecialchars( $row[ 'last_name' ], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
		$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
	}
}

// The medium-level form needs the number of users to render its ID selector.
$result = mysqli_query( $GLOBALS[ '___mysqli_ston' ], 'SELECT COUNT(*) FROM users;' );
$number_of_rows = mysqli_fetch_row( $result )[0];
mysqli_close( $GLOBALS[ '___mysqli_ston' ] );

?>
