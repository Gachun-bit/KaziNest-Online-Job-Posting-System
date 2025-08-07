<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit();
}

$result = $conn->query("SELECT * FROM jobs");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Jobs</title>
    <link rel="stylesheet" href="company_style.css">
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>.</h2> 
    <a href="logout.php"><button>Sign out</button></a><br>

    <?php
$appQuery = $conn->prepare("
    SELECT j.job_title, j.company_name, a.application_date, a.status,  a.cv_file
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE a.user_id = ?
    ORDER BY a.application_date DESC
");
$appQuery->bind_param("i", $_SESSION['user_id']);
$appQuery->execute();
$appliedJobs = $appQuery->get_result();
?>

<div class="dashboard">
    <h4>YOUR APPLICATIONS</h4>
    <table>
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>Date Applied</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($app = $appliedJobs->fetch_assoc()): ?>
            <tr>
                <td><?php echo $app['job_title']; ?></td>
                <td><?php echo $app['company_name']; ?></td>
                <td><?php echo $app['application_date']; ?></td>
                <td><?php echo $app['status']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div><br><br>

    <?php while ($job = $result->fetch_assoc()): ?>
    <div class="job-card">
        <button class="collapsible">
           <strong> <?php echo $job['job_title']; ?> </strong> - <?php echo $job['company_name']; ?>
        </button>
        <div class="job-details">
            <p><h3>COMPANY NAME:</h3> <?php echo $job['company_name']; ?></p>
            <p><strong>Job Title:</strong> <?php echo $job['job_title']; ?></p>
            <p><strong>Job Level:</strong> <?php echo $job['job_level']; ?></p>
            <p><strong>Qualification:</strong> <?php echo $job['min_qualification']; ?></p>
            <p><strong>Location:</strong> <?php echo $job['location']; ?></p>
            <p><strong>Closing Date:</strong> <?php echo $job['closing_date']; ?></p>
            <p><strong>Salary:</strong> <?php echo $job['salary'] . ' ' . $job['currency'] . '/' . $job['salary_unit']; ?></p>
            <p><strong>Experience:</strong> <?php echo $job['experience']; ?></p>
            <p><h3>DESCRIPTION:</h3><?php echo $job['vacancy_description']; ?></p>
            <p><h3>JOB OVERVIEW:</h3> <?php echo $job['application_guidelines']; ?></p>
            <form action="apply.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>"><br>
    
    <label for="cv">Upload your Credentials:</label>
    <input type="file" name="cv_file" accept=".pdf,.doc,.docx" required><br><br>

    <button type="submit">Apply Now</button>
</form>
        </div>
    </div>
<?php endwhile; ?>

<script>
    const coll = document.querySelectorAll(".collapsible");
    coll.forEach(btn => {
        btn.addEventListener("click", function() {
            this.classList.toggle("active");
            const content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    });
</script>

</body>
</html>
