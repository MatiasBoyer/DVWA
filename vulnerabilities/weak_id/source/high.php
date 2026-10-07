<?php

$html = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cookie_value = bin2hex(random_bytes(32));
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    setcookie('dvwaSession', $cookie_value, array(
        'expires' => time() + 3600,
        'path' => '/vulnerabilities/weak_id/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Strict',
    ));
}

?>
