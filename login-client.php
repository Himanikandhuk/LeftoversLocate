<?php
// Start session to store user data
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
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("SELECT * FROM clients WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Fetch user details
        $client = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $client['password'])) {
            // Password is correct, start session and store data
            $_SESSION['client_email'] = $client['email'];
            $_SESSION['client_name'] = $client['name'];

            // Redirect to client dashboard or home page
            header("Location: home-client.html?message=" . urlencode("Login successful!"));
            exit();
        } else {
            // Password incorrect
            $message = "Invalid credentials.";
        }
    } else {
        // No user found with the provided email
        $message = "No client found with this email.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to login page with error message
    header("Location: login-client.html?message=" . urlencode($message));
    exit();
} else {
    // Redirect to login page if not submitted via POST
    header("Location: login-client.html");
    exit();
}
?>
