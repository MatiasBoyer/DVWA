<?php

if (isset($_GET['name']) && is_string($_GET['name']) && $_GET['name'] !== '') {
    $name = htmlspecialchars($_GET['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $html .= "<pre>Hello {$name}</pre>";
}

?>
