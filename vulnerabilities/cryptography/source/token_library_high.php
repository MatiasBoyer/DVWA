<?php

const HIGH_TOKEN_ALGORITHM = 'aes-256-gcm';

function high_token_key() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['cryptography_high_key'])) {
        $_SESSION['cryptography_high_key'] = random_bytes(32);
    }
    return $_SESSION['cryptography_high_key'];
}

function create_token($debug = false) {
    $iv = random_bytes(12);
    $ciphertext = openssl_encrypt('userid:2', HIGH_TOKEN_ALGORITHM, high_token_key(), OPENSSL_RAW_DATA, $iv, $tag);
    if ($ciphertext === false) {
        throw new RuntimeException('Token encryption failed');
    }
    return json_encode(array(
        'token' => base64_encode($ciphertext . $tag),
        'iv' => base64_encode($iv),
    ));
}

function check_token($data) {
    $invalid = json_encode(array('status' => 401, 'message' => 'Invalid token'));
    if (!is_string($data)) {
        return $invalid;
    }

    $token = json_decode($data, true);
    if (!is_array($token) || !is_string($token['token'] ?? null) || !is_string($token['iv'] ?? null)) {
        return $invalid;
    }

    $encrypted = base64_decode($token['token'], true);
    $iv = base64_decode($token['iv'], true);
    if ($encrypted === false || $iv === false || strlen($iv) !== 12 || strlen($encrypted) < 17) {
        return $invalid;
    }

    $tag = substr($encrypted, -16);
    $ciphertext = substr($encrypted, 0, -16);
    $plaintext = openssl_decrypt($ciphertext, HIGH_TOKEN_ALGORITHM, high_token_key(), OPENSSL_RAW_DATA, $iv, $tag);
    if ($plaintext === false || !preg_match('/^userid:([1-4])$/D', $plaintext, $matches)) {
        return $invalid;
    }

    $users = array(
        1 => array('name' => 'Geoffery', 'level' => 'admin'),
        2 => array('name' => 'Bungle', 'level' => 'user'),
        3 => array('name' => 'Zippy', 'level' => 'user'),
        4 => array('name' => 'George', 'level' => 'user'),
    );
    $user = $users[(int) $matches[1]];
    return json_encode(array('status' => 200, 'user' => $user['name'], 'level' => $user['level']));
}
