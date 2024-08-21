<?php
// fetch_listing_details.php

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

// Check if listing_id is provided
if (isset($_GET['listing_id'])) {
    $listingId = $_GET['listing_id'];

    // Prepare and execute SQL statement to fetch listing details
    $stmt = $conn->prepare("SELECT * FROM food_listings WHERE id = ?");
    $stmt->bind_param("i", $listingId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $listing = $result->fetch_assoc();
        // Convert image to base64 if needed
        // $listing['image'] = base64_encode($listing['image']);
        echo json_encode($listing);
    } else {
        echo json_encode(null); // Return null if listing not found
    }

    $stmt->close();
} else {
    echo json_encode(null); // Return null if listing_id is not provided
}

$conn->close();
?>
