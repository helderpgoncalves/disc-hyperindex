<?php
// Check if the user ID is provided in the URL

require('db.php');
if (isset($_GET['id'])) {
    // Get the user ID from the URL
    $id = $_GET['id'];

    // Prepare the delete statement
    $query = "DELETE FROM users WHERE id = $id";

    // Execute the delete statement
    if (mysqli_query($con, $query)) {
        // Delete successful
        echo "Utilizador eliminado com sucesso.";
    } else {
        // Delete failed
        echo "Error deleting user: " . mysqli_error($con);
    }

    // Close the database connection
    mysqli_close($cnn);
} else {
    // User ID is not provided
    echo "User ID not specified.";
}
