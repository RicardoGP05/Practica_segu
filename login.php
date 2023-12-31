<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate user credentials
    $valid_user = 'admin';
    $valid_password = '12345';

    $entered_user = $_POST['usuario'];
    $entered_password = $_POST['contrasena'];

    if ($entered_user === $valid_user && $entered_password === $valid_password) {
        $_SESSION['authenticated_user'] = true;
        header("Location: index.php"); // Redirect authenticated user to the main page
        exit();
    } else {
        $error_message = "Incorrect credentials. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body class="container mt-5">

    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4">Login</h1>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="form-group">
                    <label for="usuario">User:</label>
                    <input type="text" class="form-control" name="usuario" id="usuario" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Password:</label>
                    <input type="password" class="form-control" name="contrasena" id="contrasena" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <?php
            if (isset($mensaje_error)) {
                echo '<div class="alert alert-danger mt-3" role="alert">' . $mensaje_error . '</div>';
            }
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
