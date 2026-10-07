<?php

$change = false;
$request_type = 'html';
$return_message = 'Request Failed';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content_type = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0]));
    if ($content_type === 'application/json') {
        $request_type = 'json';
        $data = json_decode(file_get_contents('php://input'), true);
        if (is_array($data) && isset($data['password_current'], $data['password_new'], $data['password_conf'], $data['Change'])) {
            $token = $_SERVER['HTTP_USER_TOKEN'] ?? null;
            $pass_current = $data['password_current'];
            $pass_new = $data['password_new'];
            $pass_conf = $data['password_conf'];
            $change = true;
        }
    } elseif (isset($_POST['user_token'], $_POST['password_current'], $_POST['password_new'], $_POST['password_conf'], $_POST['Change'])) {
        $token = $_POST['user_token'];
        $pass_current = $_POST['password_current'];
        $pass_new = $_POST['password_new'];
        $pass_conf = $_POST['password_conf'];
        $change = true;
    }
}

if ($change) {
    checkToken($token, $_SESSION['session_token'] ?? null, 'index.php');

    if (is_string($pass_current) && is_string($pass_new) && is_string($pass_conf) && $pass_new === $pass_conf) {
        $current_user = dvwaCurrentUser();
        $query = $db->prepare('SELECT password FROM users WHERE user = :user LIMIT 1');
        $query->execute(array(':user' => $current_user));
        $stored_password = $query->fetchColumn();

        if (is_string($stored_password) && hash_equals($stored_password, md5($pass_current))) {
            $update = $db->prepare('UPDATE users SET password = :password WHERE user = :user');
            $update->execute(array(':password' => md5($pass_new), ':user' => $current_user));
            $return_message = 'Password Changed.';
        } else {
            $return_message = 'Current password incorrect.';
        }
    } else {
        $return_message = 'Passwords did not match.';
    }
}

generateSessionToken();

if ($request_type === 'json') {
    header('Content-Type: application/json');
    print json_encode(array('Message' => $return_message));
    exit;
}

if ($change) {
    $html .= '<pre>' . $return_message . '</pre>';
}

?>
