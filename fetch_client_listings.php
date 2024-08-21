<?php
session_start();

// Check if client is logged in
if (!isset($_SESSION['client_email'])) {
    echo json_encode([]); // Return empty array if not logged in
    exit();
}

// Database connection parameters
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "leftoverslocate";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in client's email
$client_email = $_SESSION['client_email'];

// Fetch listings for the logged-in client
$stmt = $conn->prepare("SELECT * FROM food_listings WHERE email = ?");
$stmt->bind_param("s", $client_email);
$stmt->execute();
$result = $stmt->get_result();

$listings = [];
while ($row = $result->fetch_assoc()) {
    // Filter out null values
    foreach ($row as $key => $value) {
        if ($value === null) {
            unset($row[$key]);
        }
    }

    // If there's an image, encode it as base64
    if (!empty($row['image']) && file_exists($row['image'])) {
        $row['image'] = base64_encode(file_get_contents($row['image']));
    } else {
        $row['image'] = null; // Set image to null if no valid image found
    }

    $listings[] = $row;
}

$stmt->close();
$conn->close();

// Output the listings as JSON
header('Content-Type: application/json');
echo json_encode($listings);
?>
