<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config.php';
echo "Session user ID: " . $_SESSION['user_id'] . "<br>";

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit();
}

$company_id = $_SESSION['user_id'];

// Fetch jobs posted by this company
$jobStmt = $conn->prepare("SELECT id, job_title FROM jobs WHERE company_id = ?");
$jobStmt->bind_param("i", $company_id);
$jobStmt->execute();
$jobsResult = $jobStmt->get_result();
echo "Number of jobs found: " . $jobsResult->num_rows . "<br>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Company Dashboard</title>
    <link rel="stylesheet" href="company_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f9f9f9;
        }
        h2 {
            color: darkgreen;
        }
        .job-section {
            background: #fff;
            border-left: 5px solid darkgreen;
            margin-bottom: 30px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .shortlist {
            background-color: green;
        }
        .reject {
            background-color: red;
        }
    </style>
</head>
<body>
    <h2><?php echo $_SESSION['username']; ?> DASHBOARD</h2>
    <a href="company_page.php"><button class="btn shortlist">← Back to Job Posting</button></a>
    <a href="logout.php"><button class="btn reject">Logout</button></a>
    <br><br>

    <?php while ($job = $jobsResult->fetch_assoc()): ?>
        <div class="job-section">
            <h3>Job Title: <?php echo $job['job_title']; ?></h3>

            <?php
            // Fetch applicants for this job
            $appStmt = $conn->prepare("
                SELECT a.id AS application_id, u.username, u.email, a.application_date, a.status, a.cv_file
                FROM applications a
                JOIN users u ON a.user_id = u.id
                WHERE a.job_id = ?
            ");
            $appStmt->bind_param("i", $job['id']);
            $appStmt->execute();
            $applicants = $appStmt->get_result();
            ?>

            <?php if ($applicants->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Applicant Name</th>
                            <th>Email</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th>Credentials</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($app = $applicants->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $app['username']; ?></td>
                                <td><?php echo $app['email']; ?></td>
                                <td><?php echo $app['application_date']; ?></td>
                                <td><?php echo $app['status']; ?></td>
                                <td>
                                    <?php if (!empty($app['cv_file'])): ?>
                                    <a href="<?php echo htmlspecialchars($app['cv_file']); ?>" target="_blank">Download CV</a>
                                    <?php else: ?>
                                    No CV Uploaded
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <form action="update_application_status.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="application_id" value="<?php echo $app['application_id']; ?>">
                                        <input type="hidden" name="status" value="Shortlisted">
                                        <button type="submit" class="btn shortlist">Shortlist</button>
                                    </form>
                                    <form action="update_application_status.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="application_id" value="<?php echo $app['application_id']; ?>">
                                        <input type="hidden" name="status" value="Rejected">
                                        <button type="submit" class="btn reject">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No applicants have applied yet.</p>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>

</body>
</html>