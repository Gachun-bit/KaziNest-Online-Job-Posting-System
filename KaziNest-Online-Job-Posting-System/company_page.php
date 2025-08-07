<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit();
}

require_once 'config.php'; // Make sure this file connects to your database

$user_id = $_SESSION['user_id'];
$query = "SELECT subscription_status, subscription_end_date FROM users WHERE id = $user_id AND role = 'company'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Check if subscription is missing or expired
if ($row['subscription_status'] != 'active' || strtotime($row['subscription_end_date']) < time()) {
    header("Location: subscription_plan.php"); 
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Post a Job</title>
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <link rel="stylesheet" href="company_style.css">
</head>
<body>
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
        <h2>You are currently signed in as <strong><?php echo $_SESSION['username']; ?></strong>.</h2>
        <div>
            <a href="company_dashboard.php"><button>Dashboard</button></a>
            <a href="logout.php"><button>Sign out</button></a>
        </div>
    </div>

    <form action="post_job.php" method="POST">
        <input type="hidden" name="company_id" value="<?php echo $_SESSION['user_id']; ?>">
        <br><br>

        <label>Company Name</label><br>
        <input type="text" name="company_name" placeholder="E.g. KaziNest" required><br><br>

        <label>Job Title</label><br>
        <input type="text" name="job_title" placeholder="E.g. Accountant" required><br><br>

        <label>Vacancy Description</label><br>
        <textarea name="vacancy_description" id="vacancy_description" required></textarea><br><br>

        <label>Job Overview (optional)</label><br>
        <textarea name="application_guidelines" id="application_guidelines" required></textarea><br><br>

        <label>Application Email/URL</label><br>
        <input type="email" name="application_email" placeholder="Enter an email address or URL" required><br><br>

        <label>CC/BCC (optional)</label><br>
        <input type="text" name="cc_bcc" placeholder="e.g cc1@domain.com, cc2@domain.com"><br><br>

        <label>Evergreen Job?</label>
        <p>An evergreen job posting is one that has no set deadline and remains open due to high turnover.</p>
        <input type="checkbox" name="evergreen"><br><br>

        <label>Closing Date (optional)</label><br>
        <input type="date" name="closing_date"><br><br>

        <label>Job Level/Contract</label><br>
        <input type="text" name="job_level"><br><br>

        <label>Remote Position?</label>
        <input type="checkbox" name="remote"><br><br>

        <label>Location(s)</label><br>
        <input type="text" name="location"><br><br>

        <label>Field(s) of Study Required</label><br>
        <input type="text" name="field_of_study"><br><br>

        <label>Minimum Qualification</label><br>
        <input type="text" name="min_qualification"><br><br>

        <label>Experience</label><br>
        <input type="text" name="experience"><br><br>

        <h3>Compensation</h3>
        <label>Salary/Stipend</label><br>
        <input type="text" name="salary"><br><br>

        <label>Salary Currency</label><br>
        <input type="text" name="currency"><br><br>

        <label>Salary Unit</label><br>
        <input type="text" name="salary_unit"><br><br>

        <input type="submit" value="Post Job">

        <script>
        CKEDITOR.replace('vacancy_description');
        CKEDITOR.replace('application_guidelines');
        </script>
    </form>
</div>
</body>
</html>
