<?php
session_start();
require "./config.php";

if (!isset($_SESSION['logged_in'])) {
    die("Unauthorized access.");
}

// Determine file type: homework or submission
$type = isset($_GET['type']) ? $_GET['type'] : 'homework'; // Default to homework
$file = isset($_GET['file']) ? $_GET['file'] : '';

if (!$file) {
    die("No file specified.");
}

// Set base directory based on file type
if ($type === "submission") {
    $baseDir = realpath("./uploads/submissions/") . "/";
} else {
    $baseDir = realpath("./uploads/homework/") . "/";
}

// Ensure only the filename is used (prevents directory traversal)
$filename = basename($file);
$filePath = realpath($baseDir . $filename);

// Normalize paths (Windows compatibility)
$normalizedFilePath = str_replace("\\", "/", $filePath);
$normalizedBaseDir = str_replace("\\", "/", $baseDir);

// Validate file path
if (!$normalizedFilePath || strpos($normalizedFilePath, $normalizedBaseDir) !== 0) {
    die("Invalid file path.");
}

// Check if file exists
if (file_exists($filePath)) {
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . basename($filePath) . "\"");
    readfile($filePath);
    exit();
} else {
    die("File not found.");
}
?>
