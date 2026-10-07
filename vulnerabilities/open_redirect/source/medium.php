<?php

$target = $_GET['redirect'] ?? null;

if ($target === 'info.php?id=1') {
	header('Location: info.php?id=1', true, 302);
	exit;
}

if ($target === 'info.php?id=2') {
	header('Location: info.php?id=2', true, 302);
	exit;
}

if ($target === null || $target === '') {
	http_response_code(500);
	echo '<p>Missing redirect target.</p>';
	exit;
}

if (is_string($target) && preg_match('/https?:\/\//i', $target)) {
	echo '<p>Absolute URLs not allowed.</p>';
	exit;
}

echo '<p>Unknown redirect target.</p>';
exit;
