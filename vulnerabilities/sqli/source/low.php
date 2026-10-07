<?php

if (isset($_REQUEST['Submit'], $_REQUEST['id'])) {
	$id = filter_var($_REQUEST['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));

	if ($id !== false) {
		$rows = array();
		switch ($_DVWA['SQLI_DB']) {
			case MYSQL:
				$data = $db->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id');
				$data->bindValue(':id', $id, PDO::PARAM_INT);
				$data->execute();
				$rows = $data->fetchAll(PDO::FETCH_ASSOC);
				break;
			case SQLITE:
				global $sqlite_db_connection;
				$data = $sqlite_db_connection->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id');
				$data->bindValue(':id', $id, SQLITE3_INTEGER);
				$result = $data->execute();
				while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
					$rows[] = $row;
				}
				break;
		}

		foreach ($rows as $row) {
			$first = htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8');
			$last = htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8');
			$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
		}
	}
}

?>
