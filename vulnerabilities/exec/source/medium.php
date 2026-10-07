<?php

if( isset( $_POST[ 'Submit' ]  ) ) {
	$target = $_POST[ 'ip' ] ?? '';

	if( !is_string( $target ) || filter_var( $target, FILTER_VALIDATE_IP ) === false ) {
		$html .= '<pre>ERROR: You have entered an invalid IP.</pre>';
	}
	else {
		// An IP address cannot supply command options, and quoting keeps it one argument.
		$pingArgument = escapeshellarg( $target );
		$command = stristr( php_uname( 's' ), 'Windows NT' )
			? 'ping ' . $pingArgument
			: 'ping -c 4 ' . $pingArgument;
		$cmd = shell_exec( $command );
		$html .= '<pre>' . htmlspecialchars( $cmd ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) . '</pre>';
	}
}

?>
