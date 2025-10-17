<?php
session_start();
header('Content-Type: application/json');

try {
    if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] != session_id()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $name = isset($_POST['name']) ? $_POST['name'] : '';
    if ($name === '' || strpos($name, '..') !== false || !preg_match('/^backup_\d{8}_\d{6}\.sql$/', $name)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid file name']);
        exit;
    }

    $backupDir = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'backups';
    $filePath = $backupDir . DIRECTORY_SEPARATOR . $name;
    if (!is_file($filePath)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'File not found']);
        exit;
    }

    if (!unlink($filePath)) {
        throw new Exception('Failed to delete file');
    }

    echo json_encode(['success' => true, 'message' => 'Backup deleted']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>


