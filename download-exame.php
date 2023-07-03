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

    // Loop through the result and display data in rows
    $respostas = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $respostas[] = $row;
    }

    // Create the csv file
    $filename = "exame" . $id . ".csv";

    // Open the file in write mode
    $file = fopen($filename, "w");

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
    foreach ($respostas as $resposta) {
        $row = array(
            $resposta['userId'],
            $resposta['perguntaId'],
            $resposta['resposta_1'],
            $resposta['resposta_2'],
            $resposta['resposta_3'],
            $resposta['resposta_4'],
            $resposta['tempo_a_responder'],
            $resposta['estado'],
            $resposta['created_at'],
            $resposta['updated_at']
        );
        fputcsv($file, $row);
    }

    // Close the file
    fclose($file);

    // Download the file
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Type: application/csv;");
    readfile($filename);

    // Delete the file after download
    unlink($filename);
}
