<?php
// Start session to ensure user is logged in
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    // Redirect to login page if not logged in
    header("Location: login-user.html");
    exit();
}

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

// Fetch bookings for the logged-in user
$user_email = $_SESSION['user_email'];

$stmt = $conn->prepare("SELECT * FROM food_listings WHERE booked_by_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    // If there's an image, encode it for display
    if (!empty($row['image'])) {
        $row['image'] = base64_encode($row['image']);
    }

    $bookings[] = $row;
}

$stmt->close();
$conn->close();

// Return bookings as JSON response
header('Content-Type: application/json');
echo json_encode($bookings);
?>
