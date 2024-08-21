<?php
// Start session to store messages
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "localhost:3306";
$username = "root";
$password = ""; // Your database password
$dbname = "leftoverslocate";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $clientname = htmlspecialchars($_POST['clientname']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO clients (clientname, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $clientname, $email, $hashed_password);

    // Execute the statement
    if ($stmt->execute()) {
        // Registration successful
        $stmt->close();
        $conn->close();
        header("Location: register-client.html?message=Registration successful!");
        exit();
    } else {
        // Registration failed
        $stmt->close();
        $conn->close();
        header("Location: register-client.html?message=Registration failed, please try again.");
        exit();
    }
} else {
    // Redirect to registration form if not submitted via POST
    header("Location: register-client.html");
    exit();
}
?>
