<?php
// save_answers.php

// Include the database connection file
require('db.php');

// Retrieve the JSON data sent from the JavaScript code
$data = json_decode(file_get_contents('php://input'), true);

// Extract the question number and answer items from the JSON data
$question = $data['question'];
$items = $data['items'];
$userId = $data['userId'];

// Check if all answer items have values
if (!empty($items[0]) && !empty($items[1]) && !empty($items[2]) && !empty($items[3])) {
    // Prepare and execute the SQL query to save or update the answers
    $sql = "INSERT INTO respostas (userId, perguntaId, resposta_1, resposta_2, resposta_3, resposta_4, tempo_a_responder, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            resposta_1 = VALUES(resposta_1),
            resposta_2 = VALUES(resposta_2),
            resposta_3 = VALUES(resposta_3),
            resposta_4 = VALUES(resposta_4),
            estado = VALUES(estado)";

    $stmt = $con->prepare($sql);

    if (!$stmt) {
        http_response_code(500); // Set HTTP response code to indicate server error
        echo "Failed to prepare the SQL statement: " . $con->error;
        exit();
    }

    $resposta1 = $items[0];
    $resposta2 = $items[1];
    $resposta3 = $items[2];
    $resposta4 = $items[3];
    $tempoAResponder = ""; // Provide the value for "tempo_a_responder" if needed
    $estado = "respondida";

    $stmt->bind_param('iissssss', $userId, $question, $resposta1, $resposta2, $resposta3, $resposta4, $tempoAResponder, $estado);
    $stmt->execute();

    // Check if Exam is completed if question == 24 and every answer is filled
    if ($question == 24) {

        // Check if all respostas from the user have estado == 'respondida'
        $sql = "SELECT * FROM respostas WHERE userId = ? AND estado = 'respondida'";
        $stmt = $con->prepare($sql);

        if (!$stmt) {
            http_response_code(500); // Set HTTP response code to indicate server error
            echo "Failed to prepare the SQL statement: " . $con->error;
            exit();
        }

        $stmt->bind_param('i', $userId);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 24) {
            $sql = "UPDATE exames SET estado = 'concluido' WHERE userId = ?";
            $stmt = $con->prepare($sql);

            if (!$stmt) {
                http_response_code(500); // Set HTTP response code to indicate server error
                echo "Failed to prepare the SQL statement: " . $con->error;
                exit();
            }

            $stmt->bind_param('i', $userId);
            $stmt->execute();

            // Provide a response indicating the number of remaining questions
            $remainingQuestions = 0;
            echo "Respostas guardadas com sucesso. O exame foi concluído.";
        } else {
            // Provide a response indicating the number of remaining questions
            $remainingQuestions = 24 - $result->num_rows;
            echo "Existem " . $remainingQuestions . " perguntas por responder. Só será possível concluir o exame quando todas as perguntas estiverem respondidas.";
        }
    }

    // Provide a response indicating error
    if ($stmt->error) {
        http_response_code(500); // Set HTTP response code to indicate server error
        echo "Failed to execute the SQL statement: " . $stmt->error;
        exit();
    }

    // Close the database connection
    $stmt->close();
} else {
    echo "Por favor, responda a todas as perguntas.";
}

// Close the database connection
$con->close();
