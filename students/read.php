<?php
// JSON Headers
header('Content-Type: application/json'); // define content type as JSON
header('Access-Control-Allow-Origin: *'); // allow access from any origin
header('Access-Control-Allow-Methods: GET'); // allow only GET method and not POST, PUT, DELETE, OPTIONS
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Methods, Access-Control-Allow-Headers, Authorization, X-Requested-With'); // allow specific headers  

// Verify that the request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $response = [
        'status'=> http_response_code(405), // Method Not Allowed
        'message'=> 'Method Not Allowed. Use GET.',
    ];
    echo json_encode($response);
    exit();
}

// Require database connection
require_once '../inc/pdo.php';

// Use if to fetch either all students or a specific student by userId
if(isset($_GET['userId']) && is_numeric($_GET['userId']) && $_GET['userId'] != '') {
    // Fetch specific student
    $userId = intval($_GET['userId']);

    try {
        $sql = "SELECT * FROM users WHERE userId = :userId"; // Qurery to fetch student by userId
        $stmt = $pdo->prepare($sql); // prepare the statement
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT); // bind the userId parameter
        $stmt->execute(); // execute the statement

        $rowCount = $stmt->rowCount(); // get the number of rows returned
        if($rowCount > 0) {
            // proceed to fetch a single student's data
            $studentData = $stmt->fetch(PDO::FETCH_ASSOC); // fetch the student data using associative array
            $response = [
                'status'=> http_response_code(200), // OK
                'message'=> 'Single student fetched successfully.',
                'data'=> $studentData // student data
            ];
            print json_encode($response);
            exit();
        } else {
            $response = [
                'status'=> http_response_code(404), // Not Found
                'message'=> 'Count is ' . $rowCount . '. Student not found.',
            ];
            print json_encode($response);
            exit();
        }
    } catch (PDOException $e) {
        $response = [
            'status'=> http_response_code(500), // Internal Server Error
            'message'=> 'Internal Server Error. ' . $e->getMessage(),
        ];
        echo json_encode($response);
        exit();
    }
} else {
    // Fetch all students
    try {
            $sql = "SELECT * FROM users"; // Query to fetch all students
            $stmt = $pdo->prepare($sql); // prepare the statement
            $stmt->execute(); // execute the statement

            $rowCount = $stmt->rowCount(); // get the number of rows returned
            if($rowCount > 0) {
                // proceed to fetch all students
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetch all student data using associative array

                $response = [
                    'status'=> http_response_code(200), // OK
                    'message'=> 'All students fetched successfully.',
                    'data'=> $students // all students data
                ];
                print json_encode($response);
                exit();
            } else {
                $response = [
                    'status'=> http_response_code(404), // Not Found
                    'message'=> 'No students found.',
                ];
                print json_encode($response);
                exit();
            }
        } catch (PDOException $e) {
            $response = [
                'status'=> http_response_code(500), // Internal Server Error
                'message'=> 'Internal Server Error. ' . $e->getMessage(),
            ];
            echo json_encode($response);
            exit();
        }
}