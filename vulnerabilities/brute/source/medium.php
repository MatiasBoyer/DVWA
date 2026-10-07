<?php

if (isset($_POST['Login'], $_POST['username'], $_POST['password']) &&
	is_string($_POST['username']) && is_string($_POST['password'])) {
	$user = $_POST['username'];
	$pass_hash = md5($_POST['password']);
	$connection = $GLOBALS['___mysqli_ston'];
	$max_failures = 3;
	$lockout_seconds = 15 * 60;
	$authenticated = false;
	$avatar = '';

	try {
		mysqli_begin_transaction($connection);
		$stmt = mysqli_prepare($connection, 'SELECT password, avatar, failed_login, last_login FROM users WHERE user = ? LIMIT 1 FOR UPDATE');
		mysqli_stmt_bind_param($stmt, 's', $user);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : null;
		mysqli_stmt_close($stmt);

		if ($row) {
			$failures = (int) $row['failed_login'];
			$last_failure = !empty($row['last_login']) ? strtotime($row['last_login']) : false;
			$locked = $failures >= $max_failures && $last_failure !== false &&
				$last_failure + $lockout_seconds > time();

			if (!$locked && hash_equals((string) $row['password'], $pass_hash)) {
				$authenticated = true;
				$avatar = $row['avatar'];
				$stmt = mysqli_prepare($connection, 'UPDATE users SET failed_login = 0, last_login = NOW() WHERE user = ?');
				mysqli_stmt_bind_param($stmt, 's', $user);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_close($stmt);
			} elseif (!$locked) {
				if ($failures >= $max_failures) {
					$failures = 0;
				}
				$failures++;
				$stmt = mysqli_prepare($connection, 'UPDATE users SET failed_login = ?, last_login = NOW() WHERE user = ?');
				mysqli_stmt_bind_param($stmt, 'is', $failures, $user);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_close($stmt);
			}
		}
		mysqli_commit($connection);
	} catch (Throwable $e) {
		mysqli_rollback($connection);
		$authenticated = false;
	}

	if ($authenticated) {
		$safe_user = htmlspecialchars($user, ENT_QUOTES, 'UTF-8');
		$safe_avatar = htmlspecialchars((string) $avatar, ENT_QUOTES, 'UTF-8');
		$html .= "<p>Welcome to the password protected area {$safe_user}</p>";
		$html .= "<img src=\"{$safe_avatar}\" />";
	} else {
		sleep(2);
		$html .= '<pre><br />Username and/or password incorrect.</pre>';
	}
}

?>
