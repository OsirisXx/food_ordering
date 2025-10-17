<?php
// Lightweight activity logger
// Usage: include this file after connect.php, then call logActivity($con, 'Your message');

if (!function_exists('logActivity')) {
    function logActivity(mysqli $con, string $action, ?string $explicitRole = null): void {
        $role = $explicitRole ?? ($_SESSION['role'] ?? 'Admin');
        $stmt = mysqli_prepare($con, "INSERT INTO activity_logs (user_role, action, date) VALUES (?, ?, NOW())");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $role, $action);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}
?>


