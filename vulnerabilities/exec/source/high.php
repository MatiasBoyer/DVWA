<?php

if (isset($_POST['Submit'])) {
    $target = $_POST['ip'] ?? null;

    // Only an IPv4 address may become an argument to ping.
    if (!is_string($target) || filter_var(trim($target), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
        $html .= '<pre>ERROR: You have entered an invalid IP.</pre>';
    } else {
        $target = trim($target);
        if (stristr(php_uname('s'), 'Windows NT')) {
            $command = 'ping ' . escapeshellarg($target);
        } else {
            $command = 'ping -c 4 ' . escapeshellarg($target);
        }

        $output = shell_exec($command);
        $html .= '<pre>' . htmlspecialchars($output ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
    }
}

?>
