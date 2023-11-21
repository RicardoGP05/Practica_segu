<?php
// Disable error and warning printing
error_reporting(0);
ini_set('display_errors', 0);
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Verify user authentication before allowing file upload
if (!isset($_SESSION['authenticated_user']) || $_SESSION['authenticated_user'] !== true) {
    header("Location: login.php");
    exit();
}

// Initialize default message
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Directory where files will be stored (ensure write permissions)
    $uploadDirectory = 'uploads/';

    // File name and full path
    $originalFileName = $_FILES['archivo']['name'];
    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $uploadedFile = $uploadDirectory . uniqid() . '.' . $fileExtension;

    // Check if the file is safe
    $fileType = strtolower($fileExtension);
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif'); // Allowed extension
    if (!in_array($fileType, $allowedExtensions)) {
        $message = "Error: Only files of type " . implode(', ', $allowedExtensions) . " are allowed.";
    } else {
        // Check file size
        $maxFileSizeBytes = 5 * 1024 * 1024; // 5 MB in bytes
        if ($_FILES['archivo']['size'] > $maxFileSizeBytes) {
            // Convert maximum allowed size to megabytes for error message display
            $maxFileSizeMB = $maxFileSizeBytes / (1024 * 1024);
            $message = "Error: The file exceeds the allowed size (maximum $maxFileSizeMB MB).";
        } else {
            // Check if the directory exists, if not, create it
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }

            // Check if the file already exists
            if (file_exists($uploadedFile)) {
                $message = "Error: The file already exists.";
            } else {
                // Check if the file is a valid image
                $imageInfo = @getimagesize($_FILES['archivo']['tmp_name']);
                if (!$imageInfo) {
                    $message = "Error: The file is not a valid image.";
                } else {
                    // Move the file to the destination directory
                    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $uploadedFile)) {
                        // Set read-only permissions for the file
                        chmod($uploadedFile, 0444);
                        $message = "The file has been uploaded successfully.";
                    } else {
                        $message = "Error uploading the file.";
                    }
                }
            }
        }
    }

    // Send the response to the client (without HTML tags)
    echo $message;
    exit;
} else {
    $message = "Access not allowed.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure File Upload</title>
    <!-- Include Bootstrap CSS (make sure you have access to the Bootstrap library) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h1 class="mb-4">Secure File Upload</h1>

    <form id="uploadForm" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="archivo" class="form-label">Select a file:</label>
            <input type="file" class="form-control" name="archivo" id="archivo" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload File</button>
    </form>

    <!-- Display messages dynamically here -->
    <div id="message" class="mt-3"></div>

    <!-- Button to log out -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="mt-3">
        <input type="hidden" name="logout" value="true">
        <button type="submit" class="btn btn-danger">Logout</button>
    </form>

    <!-- Include Bootstrap JS and dependencies (make sure you have access to the Bootstrap library) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#uploadForm").submit(function (e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "<?php echo $_SERVER['PHP_SELF']; ?>",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $("#message").html(response);
                    }
                });
            });
        });
    </script>
</body>

</html>
