<?php
// define database connection constants
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','alex');
define('DB_NAME','ant');
define('DB_PORT','3306');

// create a new PDO instance
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME, DB_USER, DB_PASS);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // print "Connected Successfully!! ;-)";
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}