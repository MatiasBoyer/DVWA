<?php

$html = '';
$current_user = dvwaCurrentUser();
$user_query = $db->prepare('SELECT user_id, role FROM users WHERE user = :user LIMIT 1');
$user_query->execute(array(':user' => $current_user));
$current = $user_query->fetch(PDO::FETCH_ASSOC);
$current_id = $current ? (int) $current['user_id'] : 0;

if (isset($_GET['action'], $_GET['user_id'])) {
	$requested_id = $_GET['user_id'];
	if (!is_string($requested_id) || !ctype_digit($requested_id) || (int) $requested_id < 1) {
		$html .= '<p>Invalid user ID format. Please enter a number.</p>';
	} else {
		$id = (int) $requested_id;
		if (!$current || $id !== $current_id) {
			$html .= '<p>Access denied. You can only view your own profile.</p>';
		} else {
			$profile_query = $db->prepare(
				"SELECT profile.first_name, profile.last_name, profile.user_id, profile.avatar
				 FROM users AS profile
				 JOIN users AS viewer ON viewer.user = :viewer
				 WHERE profile.user_id = :id
				   AND profile.user_id = viewer.user_id
				 LIMIT 1"
			);
			$profile_query->execute(array(':viewer' => $current_user, ':id' => $id));
			$profile = $profile_query->fetch(PDO::FETCH_ASSOC);

			if ($profile) {
				$first = htmlspecialchars($profile['first_name'], ENT_QUOTES, 'UTF-8');
				$last = htmlspecialchars($profile['last_name'], ENT_QUOTES, 'UTF-8');
				$avatar = htmlspecialchars($profile['avatar'], ENT_QUOTES, 'UTF-8');
				$html .= "<div class=\"profile-info\"><h3>User Profile</h3>";
				$html .= "<p>User ID: {$id}</p><p>Name: {$first} {$last}</p>";
				$html .= "<p>Avatar: {$avatar}</p></div>";
			} else {
				$html .= "<p>No user found with ID: {$id}</p>";
			}
		}

		if ($current) {
			$ip = substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 50);
			$log = $db->prepare('INSERT INTO bac_log (user_id, target_id, ip_address) VALUES (:user, :target, :ip)');
			$log->execute(array(':user' => $current_id, ':target' => $id, ':ip' => $ip));
		}
	}
}

$role = $current ? $current['role'] : 'unknown';
$safe_role = htmlspecialchars($role, ENT_QUOTES, 'UTF-8');
$html .= "<div class='info-banner'>Current Role: {$safe_role}</div>";

?>
