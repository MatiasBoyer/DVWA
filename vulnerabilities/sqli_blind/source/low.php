<?php

if (isset($_GET['Submit'], $_GET['id'])) {
	$rawId = $_GET['id'];
	$id = is_string($rawId)
		? filter_var($rawId, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))
		: false;
	$exists = false;

	if ($id !== false) {
		switch ($_DVWA['SQLI_DB']) {
			case MYSQL:
				$data = $db->prepare('SELECT 1 FROM users WHERE user_id = :id LIMIT 1');
				$data->bindValue(':id', $id, PDO::PARAM_INT);
				$data->execute();
				$exists = $data->fetchColumn() !== false;
				break;
			case SQLITE:
				global $sqlite_db_connection;
				$data = $sqlite_db_connection->prepare('SELECT 1 FROM users WHERE user_id = :id LIMIT 1');
				$data->bindValue(':id', $id, SQLITE3_INTEGER);
				$result = $data->execute();
				$exists = $result && $result->fetchArray(SQLITE3_NUM) !== false;
				break;
		}
	}

	if ($exists) {
		$html .= '<pre>User ID exists in the database.</pre>';
	} else {
		if ($id !== false) {
			http_response_code(404);
		}
		$html .= '<pre>User ID is MISSING from the database.</pre>';
	}
}

?>
