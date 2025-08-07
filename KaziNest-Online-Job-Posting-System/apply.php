session_start();
echo "<pre>";
print_r($_FILES);
echo "</pre>";
exit();

<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $job_id = $_POST['job_id'];
    $cv_path = null;

    // File upload handling
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] == 0) {
        $uploadDir = __DIR__ . '/uploads/'; // Absolute path to folder
        $relativeDir = 'uploads/'; // Relative path for database/browser access

        // Create uploads folder if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['cv_file']['name']);
        $targetPath = $uploadDir . $fileName;           // Full absolute path
        $relativePath = $relativeDir . $fileName;       // Relative for database

        if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $targetPath)) {
            $cv_path = $relativePath;
        } else {
            die("Failed to move uploaded file.");
        }
    } else {
        die("No file uploaded or file error occurred.");
    }

    // Check for duplicate application
    $check = $conn->prepare("SELECT * FROM applications WHERE user_id = ? AND job_id = ?");
    $check->bind_param("ii", $user_id, $job_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO applications (user_id, job_id, cv_file) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $job_id, $cv_path);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: jobseekers_page.php");
    exit();
}
?>
