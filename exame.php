<?php
require('db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>
<html>

<head>
    <meta charset="utf-8">
    <title>DISC | Bitsapiens</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/script.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https: //cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">

    <style>
        #questions-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .question-card {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .question-card .card-body {
            flex-basis: 50%;
        }

        .question-card h5.card-title {
            margin-bottom: 10px;
        }

        .drag-items {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
        }

        .drag-item {
            margin-bottom: 5px;
        }

        .drop-area {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-top: 10px;
        }

        .drop-area-card {
            width: calc(50% - 10px);
            min-height: 100px;
            border: 2px solid;
            margin-bottom: 10px;
            box-sizing: border-box;
            min-width: 100px;
        }

        .question-nav {
            flex-basis: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .prev-btn {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .next-btn {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        @media (max-width: 768px) {
            .question-card .card-body {
                flex-basis: 100%;
            }

            .drag-items {
                flex-direction: column;
                flex-wrap: wrap;
                justify-content: center;
            }

            .drag-item {
                margin-right: 10px;
            }

            .drop-area-card {
                width: calc(50% - 10px);
            }
        }

        @media (max-width: 576px) {
            .question-card {
                flex-direction: column;
            }

            .questions-container {
                flex-direction: column;
                align-items: center;
            }

            .drag-items {
                flex-direction: column;
                align-items: center;
            }

            .drag-item {
                margin-right: 0;
                margin-bottom: 5px;
            }

            .drop-area-card {
                width: 100%;
            }
        }

        .hidden {
            display: none;
        }

        .badge.badge-danger {
            background-color: #dc3545;
        }

        .badge.badge-success {
            background-color: #198754;
        }
    </style>
</head>

<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
        <div class="header_img"> <img src="images/user_avatar.png" alt=""> </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div> <a href="index.php" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span class="nav_logo-name">DISC</span> </a>
                <!-- if user is admin -->
                <div class="nav_list"> <a href="index.php" class="nav_link"> <i class='bx bx-grid-alt nav_icon'></i> <span class="nav_name">Dashboard</span> </a> <?php
                                                                                                                                                                    if ($_SESSION['username'] == 'admin') { ?><a href="users.php" class="nav_link"> <i class='bx bx-user nav_icon'></i> <span class="nav_name">Utilizadores</span> </a>
                        <a href="exames.php" class="nav_link active"> <i class='bx bx-message-square-detail nav_icon'></i> <span class="nav_name">Exames</span> </a>
                    <?php } ?>
                </div>
            </div> <a href="logout.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Sair</span> </a>
        </nav>
    </div>
    <!--Container Main start-->
    <!-- Container Main start -->
    <!-- Container Main start -->
    <div class="height-100">
        <div class="container-fluid" style="margin-top: 80px;">

            <!-- IF EXAM IS 'CONCLUIDO' -->
            <?php
            // check if user has already completed the exam
            $sql = "SELECT * FROM exames WHERE userId = (SELECT id FROM users WHERE username = ?)";
            $stmt = $con->prepare($sql);

            if (!$stmt) {
                http_response_code(500); // Set HTTP response code to indicate server error
                echo "Failed to prepare the SQL statement: " . $con->error;
                exit();
            }

            $stmt->bind_param('s', $_SESSION['username']);

            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                if ($row['estado'] == 'concluido') {
                    echo '<div class="alert alert-danger" role="alert">Já realizou este exame! O exame foi concluído em ' . $row['updated_at'] . '</div>';
                    exit();
                }
            }
            ?>

            <?php
            require('db.php');

            // Get Exame by link
            if (isset($_GET['link'])) {
                $link = $_GET['link'];

                // Check if the link exists and the exam.userId is the same as the logged in user by comparing the username
                $query = "SELECT * FROM exames WHERE link = '$link' AND (estado = 'por_iniciar' OR estado = 'pendente') AND userId = (SELECT id FROM users WHERE username = '" . $_SESSION['username'] . "')";
                $result = mysqli_query($con, $query) or die(mysqli_error($con));

                // Update exam state to pendente
                $query = "UPDATE exames SET estado = 'pendente' WHERE link = '$link'";
                $resultUpdate = mysqli_query($con, $query) or die(mysqli_error($con));

                // If exam doesn't exist, show error
                $count = mysqli_num_rows($result);

                if ($count == 0) {
                    echo "<h1>Exame não encontrado</h1>";
                    exit();
                }

                if (isset($_GET['question'])) {
                    $questionCounter = $_GET['question'];
                } else {
                    $questionCounter = 1;
                }

                // Get the pergunta by id
                $perguntaQuery = "SELECT * FROM perguntas WHERE id = '$questionCounter'";
                $perguntaResult = mysqli_query($con, $perguntaQuery) or die(mysqli_error($con));
                $pergunta = mysqli_fetch_assoc($perguntaResult);


                // Get the 'estado' of the 'resposta' of the current question
                $resposta = mysqli_query($con, "SELECT * FROM respostas WHERE perguntaId = $questionCounter AND userId = (SELECT id FROM users WHERE username = '" . $_SESSION['username'] . "')") or die(mysqli_error($con));
                $respostaEstado = mysqli_fetch_assoc($resposta)['estado'];
                $badgeClass = $respostaEstado == 'respondida' ? '<i class="badge badge-success">Respondida</i>' : '<i class="badge badge-danger">Não respondida</i>';

                echo "<div class='card mb-3 question-card' style='max-width: 100%; width: 100%;'>";
                echo "<div class='card-body' id='questions-container'>";
                echo "<h5 class='card-title'>Pergunta: " . $questionCounter . " " . $badgeClass . "</h5>";
                echo "<div class='d-flex justify-content-between align-items-center'>";
                echo "<div class='drag-items' style='display: flex; align-items: left; flex-direction: column; text-align: left;'>";
                echo "<div class='drag-item' draggable='true'>" . $pergunta['item_1'] . "</div>";
                echo "<div class='drag-item' draggable='true'>" . $pergunta['item_2'] . "</div>";
                echo "<div class='drag-item' draggable='true'>" . $pergunta['item_3'] . "</div>";
                echo "<div class='drag-item' draggable='true'>" . $pergunta['item_4'] . "</div>";
                echo "</div>";
                echo "<div class='drop-area'>";
                echo "<div class='drop-area-card drop-area-card-1' style='background-color: #f1f1f1;'>";
                echo "<span style='font-size: 24px;'>1</span>";
                echo "</div>";

                echo "<div class='drop-area-card drop-area-card-2' style='background-color: #e1e1e1;'>";
                echo "<span style='font-size: 24px;'>2</span>";
                echo "</div>";

                echo "<div class='drop-area-card drop-area-card-3' style='background-color: #d1d1d1;'>";
                echo "<span style='font-size: 24px;'>3</span>";
                echo "</div>";

                echo "<div class='drop-area-card drop-area-card-4' style='background-color: #c1c1c1;'>";
                echo "<span style='font-size: 24px;'>4</span>";
                echo "</div>";
                echo "<input type='hidden' id='resposta_1' name='resposta_1' value=''>";
                echo "<input type='hidden' id='resposta_2' name='resposta_2' value=''>";
                echo "<input type='hidden' id='resposta_3' name='resposta_3' value=''>";
                echo "<input type='hidden' id='resposta_4' name='resposta_4' value=''>";
                echo "</div>";
                echo "</div>";
                echo "<div class='question-nav' style='display: flex; justify-content: space-between; width: 100%;'>";
                // only show previous button if question is not the first
                if ($questionCounter != 1) {
                    echo "<button class='btn btn-primary prev-btn' data-question='$questionCounter'>Anterior</button>"; // Previous button
                }

                // only show submit button if question is the last
                if ($questionCounter == 24) {
                    echo "<button class='btn btn-primary next-btn' data-question='$questionCounter'>Finalizar Exame</button>"; // Finish Exam button
                } else {
                    echo "<button class='btn btn-primary next-btn' data-question='$questionCounter'>Próxima</button>"; // Next button
                }
                echo "</div>";
                echo "</div>";
                echo "</div>";
            } else {
                // If link doesn't exist, show error
                echo "<h1>Exame não encontrado</h1>";
                exit();
            }
            ?>
            <!-- Rest of your code here -->
        </div>
    </div>

    <script src="js/drag_and_drop.js"></script>
    <script>
        // JavaScript code for handling the Next and Previous buttons
        function handleNextButtonClick(questionCounter) {
            // Get the drag items and drop areas for the current question
            var dragItems = document.querySelectorAll('.question-card[data-question="' + questionCounter + '"] .drag-item');
            var dropAreas = document.querySelectorAll('.question-card[data-question="' + questionCounter + '"] .drop-area-card');

            // Check if all drop areas have a drag item
            var isAnswered = true;
            dropAreas.forEach(function(dropArea) {
                if (!dropArea.firstChild) {
                    isAnswered = false;
                }
            });

            // If all drop areas have a drag item, submit the answer and move to the next question
            if (isAnswered) {
                // Prepare the answer data to be submitted
                var answerData = {
                    question: questionCounter,
                    items: []
                };

                var resposta_1 = document.getElementById("resposta_1").value;
                var resposta_2 = document.getElementById("resposta_2").value;
                var resposta_3 = document.getElementById("resposta_3").value;
                var resposta_4 = document.getElementById("resposta_4").value;

                answerData.items.push(resposta_1);
                answerData.items.push(resposta_2);
                answerData.items.push(resposta_3);
                answerData.items.push(resposta_4);

                // pass my userId to the answerData get userId from username in session
                answerData.userId = <?php require_once('db.php');
                                    $username = $_SESSION['username'];
                                    $query = "SELECT id FROM users WHERE username = '$username'";
                                    $result = mysqli_query($con, $query) or die(mysqli_error($con));
                                    $row = mysqli_fetch_assoc($result);
                                    echo $row['id'];
                                    ?>;

                // Make an AJAX request to submit the answer
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // Success, move to the next question if not the last question (24 is the last question)
                            if (questionCounter < 24) {
                                var nextQuestion = questionCounter + 1;
                                window.location.href = 'exame.php?link=<?php echo $link; ?>&question=' + nextQuestion;
                            } else {
                                alert(xhr.responseText);
                            }
                        } else {
                            // Error occurred while submitting the answer
                            console.log('Error:', xhr.responseText);
                            console.error(xhr);
                            console.error(xhr.status);
                        }
                    }
                };
                xhr.open('POST', 'save_answers.php', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(JSON.stringify(answerData));
            } else {
                // Show an alert or message to indicate that all answers must be provided
                alert('Please provide answers for all items before moving to the next question.');
            }
        }

        // Function to handle the Previous button click event
        function handlePreviousButtonClick(questionCounter) {
            // Move to the previous question
            var previousQuestion = questionCounter - 1;
            window.location.href = 'exame.php?link=<?php echo $link; ?>&question=' + previousQuestion;
        }

        // Get the current question number from the Next button data attribute
        var nextButtons = document.querySelectorAll('.next-btn');
        nextButtons.forEach(function(nextButton) {
            nextButton.addEventListener('click', function() {
                var questionCounter = parseInt(this.getAttribute('data-question'));
                handleNextButtonClick(questionCounter);
            });
        });

        // Get the current question number from the Previous button data attribute
        var prevButtons = document.querySelectorAll('.prev-btn');
        prevButtons.forEach(function(prevButton) {
            prevButton.addEventListener('click', function() {
                var questionCounter = parseInt(this.getAttribute('data-question'));
                handlePreviousButtonClick(questionCounter);
            });
        });
    </script>
    </div>
</body>

</html>