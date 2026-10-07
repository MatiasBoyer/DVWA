<?php

$allowedPages = [
	'include.php',
	'file1.php',
	'file2.php',
	'file3.php',
];

if (!array_key_exists('page', $_GET)) {
	return;
}

$pageName = $_GET['page'];
if (!is_string($pageName) || !in_array($pageName, $allowedPages, true)) {
	echo 'ERROR: File not found!';
	exit;
}

$file = $pageName;
