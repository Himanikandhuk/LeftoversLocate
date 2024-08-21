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

// Check if POST data is provided
if (isset($_POST['listing_id'], $_POST['name'], $_POST['email'], $_POST['contact'])) {
    $listingId = $_POST['listing_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    // Perform booking logic and update the database
    $stmt = $conn->prepare("UPDATE food_listings SET status = 'booked', booked_by_name = ?, booked_by_email = ?, booked_by_contact = ?, booked_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $contact, $listingId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false]); // Return failure if required data is not provided
}

$conn->close();
?>
