<?php
// Check if the user ID is provided in the URL
if (isset($_GET['id'])) {
    // Connect to the database
    $conn = mysqli_connect("localhost", "root", "", "disc");

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    // Get the user ID from the URL
    $id = $_GET['id'];

    // Prepare the delete statement
    $query = "DELETE FROM users WHERE id = $id";

    // Execute the delete statement
    if (mysqli_query($conn, $query)) {
        // Delete successful
        echo "Utilizador eliminado com sucesso.";
    } else {
        // Delete failed
        echo "Error deleting user: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    // User ID is not provided
    echo "User ID not specified.";
}
