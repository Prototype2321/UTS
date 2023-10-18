<?php
// Include your database connection code here
$db = new mysqli("localhost", "root", "", "task_tracker");

include "db_connection.php"; // Replace with the actual filename

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user registration data from the form
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $en_pass = md5($password);

    echo $en_pass;

    // Validate user input (you can add more validation as needed)
    if (empty($name) || empty($email) || empty($password)) {
        // Handle validation errors (e.g., display an error message)
        echo "All fields are required.";
    } else {
        // Insert user data into the database (use prepared statements for security)
        $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssss", $name, $email, $password, $en_pass);

        // if ($stmt->execute()) {
            // Registration successful, redirect to login page
            // header("Location: login.php");
            // exit;
        // } else {
            // Handle database insertion error
            // echo "Error: " . $stmt->error;
        // }

        // Close the prepared statement
        $stmt->close();
    }
}

// Close the database connection
$db->close();
?>