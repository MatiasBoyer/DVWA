<?php

function encryptMediumToken($claims, $key) {
	$nonce = random_bytes(12);
	$ciphertext = openssl_encrypt(json_encode($claims), 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag);
	if ($ciphertext === false) {
		throw new Exception('Token generation failed');
	}
	return bin2hex($nonce . $tag . $ciphertext);
}

function decryptMediumToken($token, $key) {
	if (!is_string($token) || strlen($token) <= 56 || strlen($token) % 2 !== 0 || !ctype_xdigit($token)) {
		throw new Exception('Token is in wrong format');
	}

	$encoded = hex2bin($token);
	$nonce = substr($encoded, 0, 12);
	$tag = substr($encoded, 12, 16);
	$ciphertext = substr($encoded, 28);
	$cleartext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag);
	if ($cleartext === false) {
		throw new Exception('Token authentication failed');
	}
	return $cleartext;
}

if (!isset($_SESSION['crypto_medium_key']) || strlen($_SESSION['crypto_medium_key']) !== 32) {
	$_SESSION['crypto_medium_key'] = random_bytes(32);
}
$key = $_SESSION['crypto_medium_key'];

$errors = '';
$success = '';
$messages = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	try {
		if (!isset($_POST['token']) || !is_string($_POST['token'])) {
			throw new Exception('No token passed');
		}
		$decrypted = decryptMediumToken(trim($_POST['token']), $key);
		$user = json_decode($decrypted);
		if (!is_object($user) || !isset($user->user, $user->ex, $user->level) ||
			!is_string($user->user) || !is_int($user->ex) || !is_string($user->level)) {
			throw new Exception('Could not decode token claims');
		}

		if ($user->ex <= time()) {
			throw new Exception('Token expired');
		}
		if ($user->user === 'sweep' && $user->level === 'admin') {
			$success = 'Welcome administrator Sweep';
		} else {
			$messages = 'Login successful but not as the right user.';
		}
	} catch (Exception $e) {
		$errors = $e->getMessage();
	}
}

$expired = time() - 3600;
$sootyToken = encryptMediumToken(array('user' => 'sooty', 'ex' => $expired, 'level' => 'admin', 'bio' => 'Sooty'), $key);
$sweepToken = encryptMediumToken(array('user' => 'sweep', 'ex' => $expired, 'level' => 'user', 'bio' => 'Sweep'), $key);
$sooToken = encryptMediumToken(array('user' => 'soo', 'ex' => time() + 3600, 'level' => 'user', 'bio' => 'Soo'), $key);

$html = "
		<p>You have obtained three session tokens from the application:</p>
		<p><strong>Sooty (admin), session expired</strong></p>
		<p><textarea style='width: 600px; height: 56px'>{$sootyToken}</textarea></p>
		<p><strong>Sweep (user), session expired</strong></p>
		<p><textarea style='width: 600px; height: 56px'>{$sweepToken}</textarea></p>
		<p><strong>Soo (user), session valid</strong></p>
		<p><textarea style='width: 600px; height: 56px'>{$sooToken}</textarea></p>
		<p>The tokens contain a user, an expiration time, a level and a bio. Their contents are encrypted and authenticated.</p>
		<hr>
		<p>Submit a token to check whether it grants administrator access as Sweep.</p>
";

if ($errors !== '') {
	$html .= '<div class="warning">' . $errors . '</div>';
}
if ($messages !== '') {
	$html .= '<div class="nearly">' . $messages . '</div>';
}
if ($success !== '') {
	$html .= '<div class="success">' . $success . '</div>';
}

$html .= "
		<form name=\"ecb\" method='post' action=\"" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "\">
			<p>
				<label for='token'>Token:</label><br />
				<textarea style='width: 600px; height: 56px' id='token' name='token'></textarea>
			</p>
			<p><input type=\"submit\" value=\"Submit\"></p>
		</form>
";
?>
