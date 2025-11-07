<?php
// JSON Headers
header('Content-Type: application/json'); // define content type as JSON
header('Access-Control-Allow-Origin: *'); // allow access from any origin
header('Access-Control-Allow-Methods: POST'); // allow only POST method and not GET, PUT, DELETE, OPTIONS
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Methods, Access-Control-Allow-Headers, Authorization, X-Requested-With'); // allow specific headers  

// Verify that the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = [
        'status'=> http_response_code(405), // Method Not Allowed
        'message'=> 'Method Not Allowed. Use POST.',
    ];
    echo json_encode($response);
    exit();
}

// Require database connection
require_once '../inc/pdo.php';

// Get the raw POST data
$requestBody = file_get_contents("php://input"); // for JSON data

// Decode the JSON data
$JSONData = json_decode($requestBody, true); // associative array

// Retrieve form data
$FORMData = [
    'fullname' => trim($_POST['fullname'] ?? ''),
    'email' => trim($_POST['email'] ?? '')
];

// Determine which data to use: JSON or Form Data
if(!empty($JSONData)) {
    $requestData = $JSONData;
} else {
    $requestData = $FORMData;
}

// Validate required fields
if(empty($requestData['fullname']) || empty($requestData['email'])) {
    $response = [
        'status'=> http_response_code(400), // Bad Request
        'message'=> 'Missing required fields: fullname and email are required.',
    ];
    echo json_encode($response);
    exit();
}

// prepare an insert statement
try {
    $sql = "INSERT INTO users (fullname, email) VALUES (:fullname, :email)";
    $stmt = $pdo->prepare($sql);

    // bind parameters
    $stmt->bindParam(':fullname', $requestData['fullname'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $requestData['email'], PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // success response
    $response = [
        'status'=> http_response_code(201), // Created
        'message'=> 'Student created successfully.',
        'StudentData' =>[
            'userId' => $pdo->lastInsertId(),
            'fullname' => $requestData['fullname'],
            'email' => $requestData['email']
        ]
    ];
    echo json_encode($response);
} catch (PDOException $e) {
    // error response
    $response = [
        'status'=> http_response_code(500), // Internal Server Error
        'message'=> 'Internal Server Error. ' . $e->getMessage(),
    ];
    echo json_encode($response);
    exit();
}