<?php

if (isset($_POST['Change'], $_POST['step']) && $_POST['step'] === '1') {
	unset($_SESSION['captcha_medium_verified']);
	$hide_form = true;

	$pass_new = $_POST['password_new'] ?? null;
	$pass_conf = $_POST['password_conf'] ?? null;
	if (!is_string($pass_new) || !is_string($pass_conf)) {
		$html .= '<pre>Both passwords must match.</pre>';
		$hide_form = false;
		return;
	}

	$resp = recaptcha_check_answer(
		$_DVWA['recaptcha_private_key'],
		$_POST['g-recaptcha-response'] ?? ''
	);

	if (!$resp) {
		$html .= '<pre><br />The CAPTCHA was incorrect. Please try again.</pre>';
		$hide_form = false;
		return;
	}
	if ($pass_new !== $pass_conf) {
		$html .= '<pre>Both passwords must match.</pre>';
		$hide_form = false;
		return;
	}

	$captcha_token = bin2hex(random_bytes(16));
	$_SESSION['captcha_medium_verified'] = array(
		'user' => dvwaCurrentUser(),
		'password_hash' => md5($pass_new),
		'token' => $captcha_token,
		'expires' => time() + 300
	);
	$html .= "
		<pre><br />You passed the CAPTCHA! Click the button to confirm your changes.<br /></pre>
		<form action=\"#\" method=\"POST\">
			<input type=\"hidden\" name=\"step\" value=\"2\" />
			<input type=\"hidden\" name=\"captcha_token\" value=\"{$captcha_token}\" />
			<input type=\"submit\" name=\"Change\" value=\"Change\" />
		</form>";
}

if (isset($_POST['Change'], $_POST['step']) && $_POST['step'] === '2') {
	$hide_form = true;
	$verification = $_SESSION['captcha_medium_verified'] ?? null;
	unset($_SESSION['captcha_medium_verified']);

	$captcha_token = $_POST['captcha_token'] ?? null;
	if (!is_array($verification) ||
		$verification['user'] !== dvwaCurrentUser() ||
		$verification['expires'] < time() ||
		!is_string($captcha_token) ||
		!hash_equals($verification['token'], $captcha_token)) {
		$html .= '<pre><br />You have not passed the CAPTCHA for this password.</pre>';
		$hide_form = false;
		return;
	}

	$pass_hash = $verification['password_hash'];
	$current_user = dvwaCurrentUser();
	$stmt = mysqli_prepare($GLOBALS['___mysqli_ston'], 'UPDATE users SET password = ? WHERE user = ?');
	if (!$stmt) {
		$html .= '<pre>Password could not be changed.</pre>';
		$hide_form = false;
		return;
	}
	mysqli_stmt_bind_param($stmt, 'ss', $pass_hash, $current_user);
	$changed = mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	$html .= $changed ? '<pre>Password Changed.</pre>' : '<pre>Password could not be changed.</pre>';
	if (!$changed) {
		$hide_form = false;
	}
}

?>
