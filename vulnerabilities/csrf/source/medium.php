<?php

if( isset( $_POST[ 'Change' ] ) ) {
	$submittedToken = $_POST[ 'user_token' ] ?? null;
	checkToken( $submittedToken, $_SESSION[ 'session_token' ] ?? null, 'index.php' );

	$currentPassword = $_POST[ 'password_current' ] ?? null;
	$newPassword = $_POST[ 'password_new' ] ?? null;
	$confirmation = $_POST[ 'password_conf' ] ?? null;

	if( is_string( $currentPassword ) && is_string( $newPassword ) &&
		is_string( $confirmation ) && $newPassword === $confirmation ) {
		$currentUser = dvwaCurrentUser();
		$currentHash = md5( $currentPassword );
		$data = $db->prepare( 'SELECT password FROM users WHERE user = :user AND password = :password LIMIT 1;' );
		$data->bindValue( ':user', $currentUser, PDO::PARAM_STR );
		$data->bindValue( ':password', $currentHash, PDO::PARAM_STR );
		$data->execute();

		if( $data->fetch() ) {
			$newHash = md5( $newPassword );
			$data = $db->prepare( 'UPDATE users SET password = :password WHERE user = :user;' );
			$data->bindValue( ':password', $newHash, PDO::PARAM_STR );
			$data->bindValue( ':user', $currentUser, PDO::PARAM_STR );
			$data->execute();
			$html .= '<pre>Password Changed.</pre>';
		}
		else {
			$html .= '<pre>Passwords did not match or current password incorrect.</pre>';
		}
	}
	else {
		$html .= '<pre>Passwords did not match or current password incorrect.</pre>';
	}
}

$_SESSION[ 'session_token' ] = bin2hex( random_bytes( 32 ) );

?>
