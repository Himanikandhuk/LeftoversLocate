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
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Fetch user details
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start session and store data
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect to user dashboard or home page
            header("Location: home-user.html");
            exit();
        } else {
            // Password incorrect
            $message = "Invalid credentials.";
        }
    } else {
        // No user found with the provided email
        $message = "No user found with this email.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to login page with error message
    header("Location: login-user.html?message=" . urlencode($message));
    exit();
} else {
    // Redirect to login page if not submitted via POST
    header("Location: login-user.html");
    exit();
}
?>
