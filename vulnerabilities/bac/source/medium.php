<?php
if (!defined('DVWA_WEB_PAGE_TO_ROOT')) {
    define('DVWA_WEB_PAGE_TO_ROOT', '../../../');
}

// Resolve the account from the authenticated session, not from request data.
$current_user_id = 0;
$current_user = dvwaCurrentUser();
$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT user_id FROM users WHERE user = ? LIMIT 1");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $current_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $current_user_id = (int) $row['user_id'];
    }
    mysqli_stmt_close($stmt);
}

$html = "";
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    if (!is_string($_GET['user_id']) || !preg_match('/^\d+$/', $_GET['user_id'])) {
        $html .= "<p>Invalid user ID format. Please enter a number.</p>";
    } else {
        $id = (int) $_GET['user_id'];
        $user_exists = false;
        
        // Check if user exists first
        $check_query = "SELECT user_id FROM users WHERE user_id = '$id'";
        $check_result = mysqli_query($GLOBALS["___mysqli_ston"], $check_query);
        $user_exists = ($check_result && mysqli_num_rows($check_result) > 0);
        
        if ($current_user_id > 0 && $id === $current_user_id) {
            if ($user_exists) {
                $query = "SELECT first_name, last_name, user_id, avatar FROM users WHERE user_id = '$id';";
                $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $html .= "
                        <div class=\"profile-info\">
                            <h3>User Profile</h3>
                            <p>User ID: " . htmlspecialchars($row['user_id'], ENT_QUOTES, 'UTF-8') . "</p>
                            <p>Name: " . htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8') . "</p>
                            <p>Avatar: " . htmlspecialchars($row['avatar'], ENT_QUOTES, 'UTF-8') . "</p>
                        </div>";
                }
            } else {
                $html .= "<p>No user found with ID: {$id}</p>";
            }
        } else {
            $html .= "<p>Access denied. You can only view your own profile.</p>";
        }
        
        // Log access attempts
        try {
            // First check if the bac_log table exists
            $check_table = "SHOW TABLES LIKE 'bac_log'";
            $table_exists = mysqli_query($GLOBALS["___mysqli_ston"], $check_table);
            
            if ($table_exists && mysqli_num_rows($table_exists) == 0) {
                // Create the table if it doesn't exist
                $create_table = "CREATE TABLE IF NOT EXISTS bac_log (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(6) NULL,
                    target_id INT(6) NULL,
                    ip_address VARCHAR(50) NULL,
                    action VARCHAR(50) NULL,
                    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                mysqli_query($GLOBALS["___mysqli_ston"], $create_table);
            }
            
            // Log the access attempt - only log numeric target_id
            $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
            $target_id = $user_exists ? $id : 0; // Use 0 for non-existent users
            $log_stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "INSERT INTO bac_log (user_id, target_id, ip_address) VALUES (?, ?, ?)");
            if ($log_stmt) {
                mysqli_stmt_bind_param($log_stmt, "iis", $current_user_id, $target_id, $ip);
                mysqli_stmt_execute($log_stmt);
                mysqli_stmt_close($log_stmt);
            }
        } catch (Exception $e) {
            // Silently fail if logging doesn't work
        }
    }
}
?>
