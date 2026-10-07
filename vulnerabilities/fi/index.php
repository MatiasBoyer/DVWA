<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated' ) );

$page = dvwaPageNewGrab();
$page[ 'title' ]   = 'Vulnerability: File Inclusion' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'fi';
$page[ 'help_button' ]   = 'fi';
$page[ 'source_button' ] = 'fi';

dvwaDatabaseConnect();

$vulnerabilityFile = '';
switch( dvwaSecurityLevelGet() ) {
	case 'low':
		$vulnerabilityFile = 'low.php';
		break;
	case 'medium':
		$vulnerabilityFile = 'medium.php';
		break;
	case 'high':
		$vulnerabilityFile = 'high.php';
		break;
	default:
		$vulnerabilityFile = 'impossible.php';
		break;
}

require_once DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/fi/source/{$vulnerabilityFile}";

if( !isset( $file ) ) {
	header( 'Location:?page=include.php' );
	exit;
}

// Select the included page with fixed server-side paths.
switch( true ) {
	case $file === 'include.php':
		include __DIR__ . '/include.php';
		break;
	case $file === 'file1.php':
		include __DIR__ . '/file1.php';
		break;
	case $file === 'file2.php':
		include __DIR__ . '/file2.php';
		break;
	case $file === 'file3.php':
		include __DIR__ . '/file3.php';
		break;
	default:
		echo 'ERROR: File not found!';
		exit;
}

dvwaHtmlEcho( $page );

?>
