<?php
require('db.php');
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form data and save the new exame
    $link = $_POST['link'];
    $estado = $_POST['estado'];
    $userId = $_POST['userId'];

    // Connect to the database
    $conn = mysqli_connect("localhost", "root", "", "disc");

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    // Insert the new exame into the exames table
    $query = "INSERT INTO exames (link, estado, userId) VALUES ('$link', '$estado', $userId)";
    if (mysqli_query($conn, $query)) {
        // Redirect to the exames page after successful creation
        header("Location: exames.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>

?>
<html>

<head>
    <meta charset="utf-8">
    <title>DISC | Bitsapiens</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/script.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    </script>
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
            <div> <a href="index.php" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span class="nav_logo-name">DISC</span> </a>
                <!-- if user is admin -->
                <div class="nav_list"> <a href="index.php" class="nav_link"> <i class='bx bx-grid-alt nav_icon'></i> <span class="nav_name">Dashboard</span> </a> <?php
                                                                                                                                                                    if ($_SESSION['username'] == 'admin') { ?><a href="users.php" class="nav_link"> <i class='bx bx-user nav_icon'></i> <span class="nav_name">Utilizadores</span> </a>
                        <a href="exames.php" class="nav_link active"> <i class='bx bx-message-square-detail nav_icon'></i> <span class="nav_name">Exames</span> </a> <?php } ?>
                </div>
            </div> <a href="logout.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Sair</span> </a>
        </nav>
    </div>
    <!--Container Main start-->
    <div class="height-100">
        <!-- table of exames -->
        <div class="container-fluid" style="margin-top: 80px;">
            <h2 class="text-center">Criar Novo Exame</h2>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="mb-3">
                    <label for="link" class="form-label">Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="link" name="link" required placeholder="Link do exame">
                        <button type="button" class="btn btn-primary" onclick="generateLink()">Gerar Link</button>
                    </div>
                </div>
                <input type="text" class="form-control" id="estado" name="estado" required hidden value="por_iniciar">
                <div class="mb-3">
                    <label for="userId" class="form-label">Utilizador</label>
                    <select class="form-select" id="userId" name="userId" required>
                        <option value="">Selecionar Utilizador</option>
                        <?php
                        // Connect to the database
                        $conn = mysqli_connect("localhost", "root", "", "disc");

                        // Check connection
                        if (mysqli_connect_errno()) {
                            echo "Failed to connect to MySQL: " . mysqli_connect_error();
                            exit();
                        }

                        // Fetch data from the users table
                        $query = "SELECT * FROM users";
                        $result = mysqli_query($conn, $query);

                        // Loop through the result and display options
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
                        }

                        // Close the database connection
                        mysqli_close($conn);
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Criar Exame</button>
            </form>
        </div>
    </div>
    <script>
        // gerar o link do exame
        function generateLink() {
            // random string
            var randomString = Math.random().toString(36).substring(2, 7);
            // set link value
            document.getElementById("link").value = randomString;
        }
    </script>
</body>

</html>