<?php
// Download the exam file csv   

// Include the database connection file
require('db.php');

session_start();

// Needs to have username 'admin' to access this page
if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    header("Location: index.php");
    exit();
}

// get exam id by GET request
if (isset($_GET['id'])) {
    // Download the exam file
    $id = $_GET['id'];

    // Fetch data from the exames table
    $query = "SELECT * FROM respostas WHERE userId = $id";
    $result = mysqli_query($con, $query);

    // Create the csv file
    $filename = "exame" . $id . ".csv";

    // Open the file in write mode
    $file = fopen($filename, "w");

    if ($file) {
        // Write the CSV header
        $csvHeader = array(
            'userId',
            'perguntaId',
            'resposta_1',
            'resposta_2',
            'resposta_3',
            'resposta_4',
            'tempo_a_responder',
            'estado',
            'created_at',
            'updated_at'
        );
        fputcsv($file, $csvHeader);

        // Write the data rows
        while ($row = mysqli_fetch_assoc($result)) {
            $csvRow = array(
                $row['userId'],
                $row['perguntaId'],
                $row['resposta_1'],
                $row['resposta_2'],
                $row['resposta_3'],
                $row['resposta_4'],
                $row['tempo_a_responder'],
                $row['estado'],
                $row['created_at'],
                $row['updated_at']
            );
            fputcsv($file, $csvRow);
        }

        // Close the file
        fclose($file);

        // Download the file
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv;");
        header("Content-Length: " . filesize($filename));
        readfile($filename);

        // Delete the file after download
        unlink($filename);
        
        // Stop further execution
        exit();
    } else {
        echo "Failed to open the file for writing.";
    }
}
