<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/connect.php';

try {
    if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] != session_id()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    // Resolve backups directory
    $backupDir = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'backups';
    if (!is_dir($backupDir)) {
        if (!mkdir($backupDir, 0755, true)) {
            throw new Exception('Unable to create backups directory');
        }
    }

    // Get current database name
    $dbRes = mysqli_query($con, 'SELECT DATABASE() AS db');
    if (!$dbRes) { throw new Exception('Cannot determine database: ' . mysqli_error($con)); }
    $dbRow = mysqli_fetch_assoc($dbRes);
    $dbName = $dbRow['db'];

    $filename = 'backup_' . date('Ymd_His') . '.sql';
    $filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;

    $fh = fopen($filePath, 'w');
    if (!$fh) { throw new Exception('Unable to create backup file'); }

    fwrite($fh, "-- Database backup for {$dbName}\n");
    fwrite($fh, "-- Generated at " . date('c') . "\n\n");
    fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n\n");

    // List tables
    $tablesRes = mysqli_query($con, 'SHOW TABLES');
    if (!$tablesRes) { throw new Exception('SHOW TABLES failed: ' . mysqli_error($con)); }

    while ($t = mysqli_fetch_array($tablesRes)) {
        $table = $t[0];

        // DDL
        $createRes = mysqli_query($con, "SHOW CREATE TABLE `{$table}`");
        if ($createRes) {
            $row = mysqli_fetch_array($createRes);
            fwrite($fh, "--\n-- Table structure for table `{$table}`\n--\n\n");
            fwrite($fh, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($fh, $row[1] . ";\n\n");
            mysqli_free_result($createRes);
        }

        // Data
        $dataRes = mysqli_query($con, "SELECT * FROM `{$table}`");
        if ($dataRes) {
            $fields = mysqli_fetch_fields($dataRes);
            $colNames = array_map(function($f){ return '`' . $f->name . '`'; }, $fields);
            $colList = implode(',', $colNames);
            fwrite($fh, "--\n-- Dumping data for table `{$table}`\n--\n\n");

            while ($row = mysqli_fetch_array($dataRes, MYSQLI_NUM)) {
                $values = [];
                foreach ($row as $idx => $val) {
                    if (is_null($val)) { $values[] = 'NULL'; continue; }
                    $type = $fields[$idx]->type; // numeric code
                    // BLOB/TEXT types: use hex for blobs, escape for text
                    $isBlob = in_array($type, [
                        MYSQLI_TYPE_TINY_BLOB,
                        MYSQLI_TYPE_MEDIUM_BLOB,
                        MYSQLI_TYPE_BLOB,
                        MYSQLI_TYPE_LONG_BLOB
                    ]);
                    if ($isBlob) {
                        $values[] = '0x' . bin2hex($val);
                    } else if (in_array($type, [MYSQLI_TYPE_LONG, MYSQLI_TYPE_LONGLONG, MYSQLI_TYPE_DECIMAL, MYSQLI_TYPE_NEWDECIMAL, MYSQLI_TYPE_INT24, MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_DOUBLE, MYSQLI_TYPE_FLOAT])) {
                        $values[] = is_numeric($val) ? $val : ('"' . mysqli_real_escape_string($con, $val) . '"');
                    } else {
                        $values[] = '\'' . mysqli_real_escape_string($con, $val) . '\'';
                    }
                }
                $valuesList = implode(',', $values);
                fwrite($fh, "INSERT INTO `{$table}` ({$colList}) VALUES ({$valuesList});\n");
            }
            fwrite($fh, "\n");
            mysqli_free_result($dataRes);
        }
    }

    fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
    fclose($fh);

    echo json_encode(['success' => true, 'message' => 'Backup created', 'file' => $filename]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>


