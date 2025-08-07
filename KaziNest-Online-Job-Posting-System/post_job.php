<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect form data
    $company_id = $_POST['company_id'];
    $company_name = $_POST['company_name'];
    $job_title = $_POST['job_title'];
    $vacancy_description = $_POST['vacancy_description'];
    $application_guidelines = $_POST['application_guidelines'];
    $application_email = $_POST['application_email'];
    $cc_bcc = $_POST['cc_bcc'];
    $evergreen = isset($_POST['evergreen']) ? 1 : 0;
    $closing_date = $_POST['closing_date'];
    $remote = isset($_POST['remote']) ? 1 : 0;
    $job_level = $_POST['job_level'];
    $location = $_POST['location'];
    $field_of_study = $_POST['field_of_study'];
    $min_qualification = $_POST['min_qualification'];
    $experience = $_POST['experience'];
    $salary = $_POST['salary'];
    $currency = $_POST['currency'];
    $salary_unit = $_POST['salary_unit'];

    // Prepare insert query — 18 columns (excluding ID)
    $stmt = $conn->prepare("INSERT INTO jobs (company_id, company_name, job_title, vacancy_description, application_guidelines, application_email, cc_bcc, evergreen, closing_date, remote, job_level, location, field_of_study, min_qualification, experience, salary, currency, salary_unit)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters: i = integer, s = string
    $stmt->bind_param("issssssisissssssss", 
        $company_id, $company_name, $job_title, $vacancy_description, $application_guidelines, 
        $application_email, $cc_bcc, $evergreen, $closing_date, 
        $job_level, $remote, $location, $field_of_study, 
        $min_qualification, $experience, $salary, $currency, $salary_unit
    );

    if ($stmt->execute()) {
        echo "Job posted successfully!";
        header("Location: company_page.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
