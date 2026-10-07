<?php

$allowed_targets = array('info.php?id=1', 'info.php?id=2');
$target = $_GET['redirect'] ?? null;

if (is_string($target) && in_array($target, $allowed_targets, true)) {
	header('Location: ' . $target);
	exit;
}

http_response_code(400);
echo 'Invalid redirect target.';
exit;

?>
