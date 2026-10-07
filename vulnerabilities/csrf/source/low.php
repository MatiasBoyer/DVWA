<?php

if (isset($_POST['Change'], $_POST['password_new'], $_POST['password_conf'])) {
	checkToken($_POST['user_token'] ?? null, $_SESSION['session_token'] ?? null, 'index.php');

	if ($_POST['password_new'] === $_POST['password_conf']) {
		$pass_new = md5($_POST['password_new']);
		$current_user = dvwaCurrentUser();
		$data = $db->prepare('UPDATE users SET password = :password WHERE user = :user');
		$data->execute(array(':password' => $pass_new, ':user' => $current_user));
		$html .= '<pre>Password Changed.</pre>';
	} else {
		$html .= '<pre>Passwords did not match.</pre>';
	}
}

generateSessionToken();

?>
