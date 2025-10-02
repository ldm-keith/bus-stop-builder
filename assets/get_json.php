<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cross-Origin-Resource-Policy: cross-origin'); //[LDM] added

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200); // OK
    exit;
}

// Ensure the script is only accessible via POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}

// Check if the filename is provided in the request
if (!isset($_POST['filename'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'No filename provided']);
    exit;
}

$filename = $_POST['filename'];

// Validate the filename to ensure it has a .json extension
if (pathinfo($filename, PATHINFO_EXTENSION) !== 'json') {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Filename must have a .json extension']);
    exit;
}

// Define the file path where JSON file is stored (relative path to uploads folder)
$jsonPath = __DIR__ . '/../../../uploads/designs/' . $filename;

// Check if the file exists
if (!file_exists($jsonPath)) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'File not found']);
    exit;
}

// Read the file content
$jsonData = file_get_contents($jsonPath);
if ($jsonData === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error reading file content']);
    exit;
}

// Return the JSON data
header('Content-Type: application/json');
echo $jsonData;
?>
