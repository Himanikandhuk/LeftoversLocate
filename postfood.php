<?php
// Start session if needed for any future use
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
    $description = htmlspecialchars($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $expiration = $_POST['expiration'];
    $location = htmlspecialchars($_POST['location']);
    $contact = htmlspecialchars($_POST['contact']);
    $email = htmlspecialchars($_POST['email']);
    $imagePath = "";

    // Handle file upload
    if (!empty($_FILES["images"]["name"])) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["images"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["images"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["images"]["tmp_name"], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                header("Location: postfood.html?message=Sorry, there was an error uploading your file.");
                exit();
            }
        } else {
            header("Location: postfood.html?message=File is not an image.");
            exit();
        }
    }

    // Insert food posting into the database
    $stmt = $conn->prepare("INSERT INTO food_listings (description, quantity, expiration, location, contact, email, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sisssss", $description, $quantity, $expiration, $location, $contact, $email, $imagePath);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: postfood.html?message=Food posted successfully!");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: postfood.html?message=Failed to post food, please try again.");
        exit();
    }
} else {
    header("Location: postfood.html");
    exit();
}
?>
