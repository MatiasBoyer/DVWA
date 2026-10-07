<?php

if (isset($_GET['page'])) {
	$allowed_pages = array('include.php', 'file1.php', 'file2.php', 'file3.php');
	if (!in_array($_GET['page'], $allowed_pages, true)) {
		http_response_code(400);
		echo 'ERROR: File not found!';
		exit;
	}
	$file = $_GET['page'];
}

?>
