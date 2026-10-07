<?php

if (isset($_POST['Login'], $_POST['username'], $_POST['password'])) {
	$user = $_POST['username'];
	$pass = md5($_POST['password']);
	$max_failed_logins = 3;
	$lockout_seconds = 15 * 60;

	$data = $db->prepare('SELECT password, avatar, failed_login, last_login FROM users WHERE user = :user LIMIT 1');
	$data->execute(array(':user' => $user));
	$row = $data->fetch(PDO::FETCH_ASSOC);

	$last_failure = $row && $row['last_login'] ? strtotime($row['last_login']) : 0;
	$recent_failure = $last_failure && time() - $last_failure < $lockout_seconds;
	$failed_logins = $row && $recent_failure ? (int) $row['failed_login'] : 0;
	$locked = $failed_logins >= $max_failed_logins;

	if ($row && !$locked && hash_equals($row['password'], $pass)) {
		$reset = $db->prepare('UPDATE users SET failed_login = 0 WHERE user = :user');
		$reset->execute(array(':user' => $user));

		$safe_user = htmlspecialchars($user, ENT_QUOTES, 'UTF-8');
		$safe_avatar = htmlspecialchars($row['avatar'], ENT_QUOTES, 'UTF-8');
		$html .= "<p>Welcome to the password protected area {$safe_user}</p>";
		$html .= "<img src=\"{$safe_avatar}\" />";
	} else {
		if ($row && !$locked) {
			$record = $db->prepare('UPDATE users SET failed_login = :failed, last_login = :attempted WHERE user = :user');
			$record->execute(array(
				':failed' => $failed_logins + 1,
				':attempted' => date('Y-m-d H:i:s'),
				':user' => $user,
			));
		}
		$html .= '<pre><br />Username and/or password incorrect. Please try again later.</pre>';
	}
}

?>
