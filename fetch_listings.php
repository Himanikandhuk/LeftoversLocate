<?php
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

// Initialize an empty array to hold the listings
$listings = [];

if (isset($_GET['location']) && !empty($_GET['location'])) {
    $location = htmlspecialchars($_GET['location']);
    $stmt = $conn->prepare("SELECT * FROM food_listings WHERE location LIKE ?");
    $searchTerm = "%".$location."%";
    $stmt->bind_param("s", $searchTerm);
} else {
    $stmt = $conn->prepare("SELECT * FROM food_listings");
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // If there's an image path, encode the image for JSON output
    if (!empty($row['image']) && file_exists($row['image'])) {
        $imageData = base64_encode(file_get_contents($row['image']));
        $row['image'] = $imageData;
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
