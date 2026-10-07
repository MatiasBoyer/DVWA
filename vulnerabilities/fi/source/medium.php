<?php

$pages = [
	'include.php' => __DIR__ . '/../include.php',
	'file1.php' => __DIR__ . '/../file1.php',
	'file2.php' => __DIR__ . '/../file2.php',
	'file3.php' => __DIR__ . '/../file3.php',
	'file4.php' => __DIR__ . '/../file4.php',
];

if (!array_key_exists('page', $_GET)) {
	return;
}

$pageName = $_GET['page'];
if (!is_string($pageName) || !isset($pages[$pageName])) {
	http_response_code(404);
	echo 'ERROR: File not found!';
	exit;
}

$file = $pages[$pageName];

?>
