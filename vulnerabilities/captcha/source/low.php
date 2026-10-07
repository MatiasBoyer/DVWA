<?php

if (isset($_POST['Change'])) {
	if (($_POST['step'] ?? null) !== '1' ||
		!isset($_POST['password_new'], $_POST['password_conf'], $_POST['g-recaptcha-response'])) {
		$html .= '<pre>The CAPTCHA was incorrect. Please try again.</pre>';
	} elseif (!recaptcha_check_answer($_DVWA['recaptcha_private_key'], $_POST['g-recaptcha-response'])) {
		$html .= '<pre>The CAPTCHA was incorrect. Please try again.</pre>';
	} elseif ($_POST['password_new'] !== $_POST['password_conf']) {
		$html .= '<pre>Both passwords must match.</pre>';
	} else {
		$pass_new = md5($_POST['password_new']);
		$current_user = dvwaCurrentUser();
		$data = $db->prepare('UPDATE users SET password = :password WHERE user = :user');
		$data->execute(array(':password' => $pass_new, ':user' => $current_user));
		$html .= '<pre>Password Changed.</pre>';
		$hide_form = true;
	}
}

?>
