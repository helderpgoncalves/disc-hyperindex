<?php
require('db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<html>

<head>
    <meta charset="utf-8">
    <title>DISC | Bitsapiens</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/script.js"></script>
    <style>
        .card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-text {
            font-size: 24px;
            color: #333333;
            margin-bottom: 0;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
</head>

<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
        <div class="header_img"> <img src="images/user_avatar.png" alt=""> </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div> <a href="#" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span class="nav_logo-name">DISC</span> </a>
                <!-- if user is admin -->
                <div class="nav_list"> <a href="#" class="nav_link active"> <i class='bx bx-grid-alt nav_icon'></i> <span class="nav_name">Dashboard</span> </a> <?php
                                                                                                                                                                    if ($_SESSION['username'] == 'admin') { ?><a href="users.php" class="nav_link"> <i class='bx bx-user nav_icon'></i> <span class="nav_name">Utilizadores</span> </a> <a href="exames.php" class="nav_link"> <i class='bx bx-message-square-detail nav_icon'></i> <span class="nav_name">Exames</span> </a>
                    <?php } ?>
                </div>
            </div> <a href="logout.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Sair</span> </a>
        </nav>
    </div>
    <!--Container Main start-->
    <!--Container Main start-->
    <div class="height-100">
        <div class="container-fluid" style="margin-top: 80px;">
            <?php
            require('db.php');

            if ($_SESSION['username'] == 'admin') {
                // Admin content - 3 info cards
                $query = "SELECT COUNT(*) AS total_users FROM users";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);
                $total_users = $row['total_users'];

                $query = "SELECT COUNT(*) AS total_exams FROM exames";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);
                $total_exams = $row['total_exams'];

                $query = "SELECT COUNT(*) AS completed_exams FROM exames WHERE estado = 'concluido'";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);
                $completed_exams = $row['completed_exams'];
            ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Utilizadores</h5>
                                <p class="card-text"><?php echo $total_users; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Exames</h5>
                                <p class="card-text"><?php echo $total_exams; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Exames Concluídos</h5>
                                <p class="card-text"><?php echo $completed_exams; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { // If not admin, show a single card with input and start button 
                // Get pending exams from user by username and exam's userId
                $query = "SELECT * FROM exames WHERE estado = 'pendente' AND userId = (SELECT id FROM users WHERE username = '" . $_SESSION['username'] . "')";
                $result = mysqli_query($con, $query);
                $num_rows = mysqli_num_rows($result);
            ?>
                <div class="container-fluid" style="margin-top: 80px;">
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">Exame</h5>
                                    <?php if ($num_rows > 0) {
                                        // If there are pending exams, display resume buttons
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $link = $row['link'];
                                    ?>
                                            <div class="input-group mb-3">
                                                <a href="exame.php?link=<?php echo $link; ?>" class="btn btn-primary">Retomar</a>
                                            </div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="exam-link" placeholder="Exame Link" aria-label="Exam Link" aria-describedby="start-btn" required>
                                            <button class="btn btn-primary" type="button" id="start-btn">Iniciar</button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.getElementById("start-btn").addEventListener("click", function() {
                        var examLink = document.getElementById("exam-link").value;
                        if (examLink !== "") {
                            window.location.href = "exame.php?link=" + examLink;
                        }
                    });
                </script>
            <?php } ?>
        </div>
    </div>
</body>

</html>