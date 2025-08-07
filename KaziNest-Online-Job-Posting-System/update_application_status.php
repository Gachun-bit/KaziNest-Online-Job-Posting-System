<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $application_id);
    $stmt->execute();
}

header("Location: company_dashboard.php");
exit();
?>
