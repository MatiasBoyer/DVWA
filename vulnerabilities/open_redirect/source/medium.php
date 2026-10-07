<?php

$target = $_GET['redirect'] ?? null;

if ($target === null || $target === '') {
	http_response_code(500);
	exit('<p>Missing redirect target.</p>');
}

if (is_string($target) && preg_match('/https?:\/\//i', $target)) {
	http_response_code(500);
	exit('<p>Absolute URLs not allowed.</p>');
}

if (!(is_string($target) && preg_match('/\Ainfo\.php\?id=[0-9]+\z/D', $target) === 1)) {
	http_response_code(400);
	exit('Unknown redirect target.');
}

header('Location: ' . $target, true, 302);
exit;
