<?php

if (isset($_SESSION['id'])) {
    $id = filter_var($_SESSION['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    if ($id === false) {
        $html .= '<pre>Invalid user ID.</pre>';
    } else {
        $row = false;
        switch ($_DVWA['SQLI_DB']) {
            case MYSQL:
                $query = $db->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1');
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();
                $row = $query->fetch(PDO::FETCH_ASSOC);
                break;
            case SQLITE:
                global $sqlite_db_connection;
                $query = $sqlite_db_connection->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1');
                $query->bindValue(':id', $id, SQLITE3_INTEGER);
                $result = $query->execute();
                if ($result !== false) {
                    $row = $result->fetchArray(SQLITE3_ASSOC);
                }
                break;
        }

        if ($row !== false) {
            $first = htmlspecialchars($row['first_name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $last = htmlspecialchars($row['last_name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
        }
    }
}

?>
