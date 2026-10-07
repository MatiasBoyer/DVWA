<?php

function encrypt_message($message, $key) {
	$nonce = random_bytes(12);
	$tag = '';
	$ciphertext = openssl_encrypt($message, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag);
	if ($ciphertext === false) {
		throw new RuntimeException('Encryption failed');
	}
	return base64_encode($nonce . $tag . $ciphertext);
}

function decrypt_message($encoded, $key) {
	$payload = base64_decode($encoded, true);
	if ($payload === false || strlen($payload) < 28) {
		throw new RuntimeException('Invalid encrypted message');
	}
	$message = openssl_decrypt(
		substr($payload, 28),
		'aes-256-gcm',
		$key,
		OPENSSL_RAW_DATA,
		substr($payload, 0, 12),
		substr($payload, 12, 16)
	);
	if ($message === false) {
		throw new RuntimeException('Invalid encrypted message');
	}
	return $message;
}

function verify_current_user_password($password, $db) {
	$data = $db->prepare('SELECT password FROM users WHERE user = :user LIMIT 1');
	$data->execute(array(':user' => dvwaCurrentUser()));
	$stored_hash = $data->fetchColumn();
	if (!is_string($stored_hash)) {
		return false;
	}
	if (password_verify($password, $stored_hash)) {
		return true;
	}
	return preg_match('/^[0-9a-f]{32}$/i', $stored_hash) &&
		hash_equals(strtolower($stored_hash), md5($password));
}

if (!isset($_SESSION['low_crypto_key'])) {
	$_SESSION['low_crypto_key'] = random_bytes(32);
}
$key = $_SESSION['low_crypto_key'];
$errors = '';
$success = '';
$encoded = null;
$message = '';
$direction = 'encode';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['message']) && is_string($_POST['message'])) {
		$message = $_POST['message'];
		$direction = ($_POST['direction'] ?? '') === 'decode' ? 'decode' : 'encode';
		try {
			$encoded = $direction === 'decode'
				? decrypt_message($message, $key)
				: encrypt_message($message, $key);
		} catch (RuntimeException $e) {
			$errors = 'Invalid encrypted message.';
		}
	}
	if (isset($_POST['password']) && is_string($_POST['password'])) {
		if (verify_current_user_password($_POST['password'], $db)) {
			$success = 'Welcome back user';
		} else {
			$errors = 'Login Failed';
		}
	}
}

$safe_message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
$encode_checked = $direction === 'encode' ? ' checked' : '';
$decode_checked = $direction === 'decode' ? ' checked' : '';

$html = "
	<p>Encode and decode messages within this session.</p>
	<form name='messages' method='post' action=''>
		<p><label for='message'>Message:</label><br />
		<textarea style='width: 600px; height: 56px' id='message' name='message'>{$safe_message}</textarea></p>
		<p>
			<input type='radio' value='encode' name='direction' id='direction_encode'{$encode_checked}><label for='direction_encode'>Encode</label> or
			<input type='radio' value='decode' name='direction' id='direction_decode'{$decode_checked}><label for='direction_decode'>Decode</label>
		</p>
		<p><input type='submit' value='Submit'></p>
	</form>
";

if ($encoded !== null) {
	$safe_encoded = htmlspecialchars($encoded, ENT_QUOTES, 'UTF-8');
	$html .= "<p><label for='encoded'>Result:</label><br />
		<textarea readonly style='width: 600px; height: 56px' id='encoded'>{$safe_encoded}</textarea></p>";
}

if ($errors !== '') {
	$html .= '<div class="warning">' . htmlspecialchars($errors, ENT_QUOTES, 'UTF-8') . '</div>';
}
if ($success !== '') {
	$html .= '<div class="success">' . htmlspecialchars($success, ENT_QUOTES, 'UTF-8') . '</div>';
}

$html .= "
	<form name='login' method='post' action=''>
		<p>Enter your DVWA account password to sign in.</p>
		<p><label for='password'>Password:</label><br />
		<input type='password' id='password' name='password'></p>
		<p><input type='submit' value='Login'></p>
	</form>
";

?>
