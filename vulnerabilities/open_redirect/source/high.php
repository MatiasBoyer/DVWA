<?php

$target = $_GET['redirect'] ?? null;

if (is_string($target) && preg_match('/\Ainfo\.php\?id=[0-9]+\z/D', $target) === 1) {
	header('Location: ' . $target, true, 302);
	exit;
}

http_response_code(400);
echo 'Invalid redirect target.';
exit;
