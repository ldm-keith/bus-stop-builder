<?php

// Ensure the script is only accessible via POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log errors to a file
$log_file = __DIR__ . '/../../../uploads/upload_error_log.txt';

function log_error($message) {
    global $log_file;
    error_log($message . PHP_EOL, 3, $log_file);
}

// Check if the file is provided in the request
if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    log_error('No file uploaded');
    exit;
}

// Check if the filename is provided in the request
if (!isset($_POST['filename'])) {
    http_response_code(400); 
    echo json_encode(['error' => 'No filename provided']);
    log_error('No filename provided');
    exit;
}

$filename = $_POST['filename'];

// Validate the filename to ensure it has a .json extension
if (pathinfo($filename, PATHINFO_EXTENSION) !== 'json') {
    http_response_code(400); 
    echo json_encode(['error' => 'Filename must have a .json extension']);
    log_error('Filename must have a .json extension');
    exit;
}

// Get the file information
$file = $_FILES['file'];
$fileTmpPath = $file['tmp_name'];
$fileError = $file['error'];

// Check for upload errors
if ($fileError !== UPLOAD_ERR_OK) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'File upload error']);
    log_error('File upload error: ' . $fileError);
    exit;
}

// Read the file content
$blobData = file_get_contents($fileTmpPath);
if ($blobData === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error reading file content']);
    log_error('Error reading file content');
    exit;
}

// Create a JSON structure from the BLOB (assuming the BLOB is in JSON format)
$jsonData = json_decode($blobData, true);
if ($jsonData === null) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid JSON']);
    log_error('Invalid JSON');
    exit;
}

// Define the file path where JSON file will be saved (relative path to uploads folder)
$savePath = __DIR__ . '/../../../uploads/designs/' . $filename;

// Ensure the directory exists
if (!is_dir(dirname($savePath))) {
    mkdir(dirname($savePath), 0777, true);
}

// Save the JSON data to the file
if (file_put_contents($savePath, json_encode($jsonData, JSON_PRETTY_PRINT)) === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error saving JSON file']);
    log_error('Error saving JSON file');
    exit;
}

// Return success response
http_response_code(200); // OK
echo json_encode(['success' => true, 'file' => basename($savePath)]);
?>
