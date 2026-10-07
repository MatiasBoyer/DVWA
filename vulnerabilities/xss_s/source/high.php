<?php

if (isset($_POST['btnSign'], $_POST['mtxMessage'], $_POST['txtName']) &&
    is_string($_POST['mtxMessage']) && is_string($_POST['txtName'])) {
    $message = trim($_POST['mtxMessage']);
    $name = trim($_POST['txtName']);

    $insert = $db->prepare('INSERT INTO guestbook (comment, name) VALUES (:message, :name)');
    $insert->execute(array(':message' => $message, ':name' => $name));
}

?>
