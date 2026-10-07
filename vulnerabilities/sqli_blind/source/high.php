<?php

if (isset($_COOKIE['id'])) {
    $id = filter_var($_COOKIE['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    $exists = false;

    if ($id !== false) {
        switch ($_DVWA['SQLI_DB']) {
            case MYSQL:
                $query = $db->prepare('SELECT 1 FROM users WHERE user_id = :id LIMIT 1');
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();
                $exists = $query->fetchColumn() !== false;
                break;
            case SQLITE:
                global $sqlite_db_connection;
                $query = $sqlite_db_connection->prepare('SELECT 1 FROM users WHERE user_id = :id LIMIT 1');
                $query->bindValue(':id', $id, SQLITE3_INTEGER);
                $result = $query->execute();
                $exists = $result !== false && $result->fetchArray(SQLITE3_NUM) !== false;
                break;
        }
    }

    if ($exists) {
        $html .= '<pre>User ID exists in the database.</pre>';
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        $html .= '<pre>User ID is MISSING from the database.</pre>';
    }
}

?>
